<?php

namespace Tests\Feature;

use App\Services\SurveyService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class TicketingSurveyTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the table
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test Admin'];

    private string $slug;

    protected function setUp(): void
    {
        parent::setUp();

        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        Services::injectMock('throttler', new class () {
            public function check(string $key, int $capacity, int $seconds, int $cost = 1): bool
            {
                return true;
            }
        });

        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('survey_responses', true);

        require_once APPPATH . 'Database/Migrations/2026-11-16-150000_CreateSurveyResponses.php';
        (new \App\Database\Migrations\CreateSurveyResponses($forge))->up();

        $this->slug = config('TicketingSurvey')->slug;
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function form(array $q = []): array
    {
        return [
            'form_time' => time() - 120,
            'q'         => array_replace([
                'name'       => 'Asha Chotai',
                'committees' => ['executive'],
                'quote'      => 'named',
            ], $q),
        ];
    }

    private function rows(): array
    {
        return $this->db->table('survey_responses')->get()->getResultArray();
    }

    public function testPageIsUnlistedAndShowsTheQuestions(): void
    {
        $result = $this->get($this->slug);

        $result->assertOK();
        $result->assertSee('noindex, nofollow');
        $result->assertSee('Which committee(s) are you on?');
        $result->assertSee('Must have');
        $this->assertSame('noindex, nofollow', $result->response()->getHeaderLine('X-Robots-Tag'));
    }

    public function testRequiredAnswersAreEnforced(): void
    {
        $this->post($this->slug, ['form_time' => time() - 120, 'q' => ['name' => '  ']])->assertRedirect();

        $errors = session('errors');
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('committees', $errors);
        $this->assertArrayHasKey('quote', $errors);
        $this->assertSame([], $this->rows());
    }

    public function testAnOtherAnswerSatisfiesARequiredTickBoxQuestion(): void
    {
        $this->post($this->slug, $this->form(['committees' => [], 'committees_other' => 'Navratri team']))
            ->assertRedirectTo(site_url($this->slug . '/thank-you'));

        $this->assertCount(1, $this->rows());
    }

    public function testASubmissionIsStoredWithUnknownValuesDropped(): void
    {
        $this->post($this->slug, $this->form([
            'email'          => 'asha@example.com',
            'committees'     => ['mahila', 'made-up', 'executive'],
            'current_rating' => '9',
            'support'        => 'support',
            'features'       => ['phone_booking' => 'must', 'checkin' => 'nice', 'nope' => 'must', 'rsvp' => 'maybe'],
            'one_change'     => "  Stop chasing\r\n\r\n\r\n\r\npayments  ",
            'quote'          => 'anonymous',
        ]))->assertRedirectTo(site_url($this->slug . '/thank-you'));

        $rows = $this->rows();
        $this->assertCount(1, $rows);
        $this->assertSame('Asha Chotai', $rows[0]['respondent_name']);
        $this->assertSame('asha@example.com', $rows[0]['email']);
        $this->assertSame('anonymous', $rows[0]['quote_preference']);

        $answers = json_decode($rows[0]['answers'], true);
        $this->assertSame(['executive', 'mahila'], $answers['committees'], 'kept in display order');
        $this->assertArrayNotHasKey('current_rating', $answers);
        $this->assertSame(['phone_booking' => 'must', 'checkin' => 'nice'], $answers['features']);
        $this->assertSame("Stop chasing\n\npayments", $answers['one_change']);
        $this->assertArrayNotHasKey('name', $answers, 'name lives in its own column');

        $this->assertSame('Asha', session('ticketing_survey_done'));
        $this->withSession(['ticketing_survey_done' => 'Asha'])->get($this->slug . '/thank-you')->assertSee('Thank you, Asha');
    }

    public function testABadEmailIsRejected(): void
    {
        $this->post($this->slug, $this->form(['email' => 'not-an-email']))->assertRedirect();

        $this->assertArrayHasKey('email', session('errors'));
        $this->assertSame([], $this->rows());
    }

    public function testBotsAreTurnedAway(): void
    {
        $this->post($this->slug, ['website' => 'spam'] + $this->form())->assertRedirectTo(site_url($this->slug));
        $this->post($this->slug, ['form_time' => time()] + $this->form())->assertRedirect();

        $this->assertSame([], $this->rows());
    }

    public function testAClosedSurveyTakesNoAnswers(): void
    {
        $config              = config('TicketingSurvey');
        $config->closingDate = '2000-01-01';
        Factories::injectMock('config', 'TicketingSurvey', $config);

        $this->get($this->slug)->assertSee('This questionnaire has closed');
        $this->post($this->slug, $this->form())->assertRedirectTo(site_url($this->slug));
        $this->assertSame([], $this->rows());
    }

    public function testThankYouNeedsASubmissionFirst(): void
    {
        $this->get($this->slug . '/thank-you')->assertRedirectTo(site_url($this->slug));
    }

    public function testResultsAreForAdminsOnly(): void
    {
        $this->get('admin/surveys/ticketing')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS'])
            ->get('admin/surveys/ticketing')
            ->assertRedirectTo('/admin/system/dashboard');
    }

    public function testResultsSummaryAndExport(): void
    {
        $this->post($this->slug, $this->form([
            'features'   => ['phone_booking' => 'must', 'waitlist' => 'no'],
            'one_change' => '=HYPERLINK("x") please',
            'quote'      => 'anonymous',
        ]));
        $this->post($this->slug, $this->form([
            'name'     => 'Ronak Pawar',
            'features' => ['phone_booking' => 'must', 'waitlist' => 'nice'],
        ]));

        $result = $this->withSession(self::ADMIN)->get('admin/surveys/ticketing');
        $result->assertOK();
        $result->assertSee('2 responses');
        $result->assertSee('Feature priorities');
        $result->assertSee('Ronak Pawar');
        $result->assertSee('not for quoting by name');

        $csv = $this->withSession(self::ADMIN)->get('admin/surveys/ticketing/export');
        $csv->assertOK();
        $body = $csv->response()->getBody();
        $this->assertStringContainsString('Ronak Pawar', $body);
        $this->assertStringContainsString('Must have', $body);
        $this->assertStringContainsString("'=HYPERLINK", $body, 'formulas are neutralised');
    }

    public function testAdminCanRemoveATestResponse(): void
    {
        $this->post($this->slug, $this->form());
        $id = (int) $this->rows()[0]['id'];

        $this->withSession(self::ADMIN)->post("admin/surveys/ticketing/delete/{$id}")
            ->assertRedirectTo(site_url('admin/surveys/ticketing'));

        $this->assertNotNull($this->rows()[0]['deleted_at']);
    }

    public function testGridRowsAreRankedByPriority(): void
    {
        $questions = array_intersect_key(config('TicketingSurvey')->questions(), ['features' => true]);
        $answers   = [
            ['phone_booking' => 'must', 'waitlist' => 'nice', 'rsvp' => 'no'],
            ['phone_booking' => 'must', 'waitlist' => 'no', 'rsvp' => 'unsure'],
        ];
        $responses = array_map(static fn(array $f, int $i): array => [
            'id' => $i + 1, 'respondent_name' => 'R' . $i, 'quote_preference' => 'named', 'answers' => ['features' => $f],
        ], $answers, array_keys($answers));

        $rows = (new SurveyService())->summarise($questions, $responses)['features']['rows'];

        $this->assertSame(['phone_booking', 'waitlist', 'rsvp'], array_slice(array_keys($rows), 0, 3));
        $this->assertEqualsWithDelta(2.0, $rows['phone_booking']['score'], 0.001);
        $this->assertEqualsWithDelta(0.5, $rows['waitlist']['score'], 0.001);
        $this->assertEqualsWithDelta(0.0, $rows['rsvp']['score'], 0.001, '"Not sure" is left out of the score');
        $this->assertSame(1, $rows['rsvp']['counts']['unsure']);
        $this->assertNull($rows['codes']['score']);
    }
}
