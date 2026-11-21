<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockCache;
use Config\Services;

/**
 * The contact form's spam defences: the content check and Cloudflare Turnstile.
 *
 * @internal
 */
final class ContactFormTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;
    protected $refresh = false;

    /** What the fake Cloudflare saw, and what it answers. */
    private array $cloudflare = ['calls' => 0, 'reply' => '{"success":true}', 'throw' => false];

    protected function setUp(): void
    {
        parent::setUp();

        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        // The form's per-IP limit lives in the cache; start each test clean.
        Services::injectMock('cache', new MockCache());

        $cf = &$this->cloudflare;
        Services::injectMock('curlrequest', new class ($cf) {
            public function __construct(private array &$cf)
            {
            }

            public function post(string $url, array $options)
            {
                $this->cf['calls']++;
                $this->cf['last'] = $options['form_params'] ?? [];
                if ($this->cf['throw']) {
                    throw new \RuntimeException('Connection timed out');
                }
                $body = $this->cf['reply'];

                return new class ($body) {
                    public function __construct(private string $body)
                    {
                    }

                    public function getBody(): string
                    {
                        return $this->body;
                    }
                };
            }
        });

        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('email_queue', true);
        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'group_email_id', 'body_html', 'body_text',
            'headers_json', 'priority', 'status', 'attempts', 'last_error', 'scheduled_at', 'sent_at',
            'created_at', 'updated_at', 'deleted_at',
        ], ['type' => 'TEXT', 'null' => true]));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function turnstileOn(): void
    {
        $config = config('Turnstile');
        $config->siteKey   = 'site-key';
        $config->secretKey = 'secret-key';
        Factories::injectMock('config', 'Turnstile', $config);
    }

    private function form(array $overrides = []): array
    {
        return array_merge([
            'form_time' => time() - 30,
            'website'   => '',
            'name'      => 'Priya Shah',
            'email'     => 'priya@example.com',
            'subject'   => 'Navratri volunteering',
            'message'   => 'Hello, I would like to help at Navratri this year.',
        ], $overrides);
    }

    private function queued(): int
    {
        return $this->db->table('email_queue')->countAllResults();
    }

    public function testAGenuineMessageIsQueuedWithTurnstileOff(): void
    {
        $this->post('contact/send', $this->form())->assertSessionHas('success');

        $this->assertSame(2, $this->queued()); // info@lcnl.co.uk and info@lcnl.org
        $this->assertSame(0, $this->cloudflare['calls']);
    }

    public function testLinkStuffedMessagesAreSentBackWithAReason(): void
    {
        $result = $this->post('contact/send', $this->form([
            'message' => 'Great site! https://a.example https://b.example https://c.example',
        ]));

        $result->assertSessionHas('errors', ['Please include no more than 2 web links in your message.']);
        $this->assertSame(0, $this->queued());
    }

    public function testTurnstileMustPassOnceSwitchedOn(): void
    {
        $this->turnstileOn();

        // No token at all: Cloudflare isn't even asked.
        $this->post('contact/send', $this->form())->assertSessionHas('errors');
        $this->assertSame(0, $this->cloudflare['calls']);

        // Cloudflare says no.
        $this->cloudflare['reply'] = '{"success":false,"error-codes":["invalid-input-response"]}';
        $this->post('contact/send', $this->form(['cf-turnstile-response' => 'bad-token']))->assertSessionHas('errors');
        $this->assertSame(0, $this->queued());

        // Cloudflare says yes.
        $this->cloudflare['reply'] = '{"success":true}';
        $this->post('contact/send', $this->form(['cf-turnstile-response' => 'good-token']))->assertSessionHas('success');
        $this->assertSame(2, $this->queued());
        $this->assertSame('secret-key', $this->cloudflare['last']['secret']);
        $this->assertSame('good-token', $this->cloudflare['last']['response']);
    }

    public function testACloudflareOutageDoesNotStopPeopleContactingUs(): void
    {
        $this->turnstileOn();
        $this->cloudflare['throw'] = true;

        $this->post('contact/send', $this->form(['cf-turnstile-response' => 'any-token']))->assertSessionHas('success');
        $this->assertSame(2, $this->queued());
    }

    public function testTheWidgetOnlyAppearsOnceConfigured(): void
    {
        $this->get('contact')->assertDontSee('cf-turnstile');

        $this->turnstileOn();
        $page = $this->get('contact');
        $page->assertSee('class="cf-turnstile');
        $page->assertSee('data-sitekey="site-key"');
    }
}
