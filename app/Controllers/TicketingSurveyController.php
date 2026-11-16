<?php

namespace App\Controllers;

use App\Models\SurveyResponseModel;
use App\Services\SurveyService;
use Config\TicketingSurvey;

/**
 * The committee questionnaire on ticketing and event money. Unlisted: the
 * link is shared with committee members directly, and the page is noindex.
 *
 * Only the name, committee and quoting preference are required, so nobody
 * gives up halfway; everything else can be skipped.
 */
class TicketingSurveyController extends BaseController
{
    /** Session key holding the first name of whoever has just answered. */
    private const SESSION_KEY = 'ticketing_survey_done';

    protected TicketingSurvey $config;
    protected SurveyResponseModel $responses;
    protected SurveyService $service;

    public function __construct()
    {
        $this->config    = config('TicketingSurvey');
        $this->responses = new SurveyResponseModel();
        $this->service   = new SurveyService();
    }

    public function index()
    {
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        return view('survey/ticketing', $this->pageData());
    }

    public function submit()
    {
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        if (! $this->config->isOpen()) {
            return redirect()->to($this->url());
        }

        $post = $this->request->getPost();

        // Honeypot: hidden from people, so anything in it is a bot.
        if (trim((string) ($post['website'] ?? '')) !== '') {
            log_message('info', 'Ticketing survey: honeypot triggered from {ip}', ['ip' => $this->request->getIPAddress()]);

            return redirect()->to($this->url());
        }

        // Nobody answers this in under ten seconds.
        $startedAt = (int) ($post['form_time'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < 10) {
            return redirect()->back()->withInput()
                ->with('errors', ['form' => 'Something went wrong sending your answers. Please try again.']);
        }

        // Committee members may well answer from the same Wi-Fi at a meeting,
        // so allow a few per minute from one address.
        if (service('throttler')->check('survey-' . md5($this->request->getIPAddress()), 6, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['form' => 'Too many submissions from this connection. Please wait a minute and try again.']);
        }

        $parsed = $this->service->parse($this->config->questions(), (array) ($post['q'] ?? []));

        if ($parsed['errors'] !== []) {
            return redirect()->back()->withInput()->with('errors', $parsed['errors']);
        }

        $answers = $parsed['answers'];
        $name    = $answers['name'];
        $email   = $answers['email'] ?? null;
        unset($answers['name'], $answers['email']);

        $this->responses->insert([
            'survey'           => $this->config->key,
            'respondent_name'  => $name,
            'email'            => $email,
            'quote_preference' => $answers['quote'],
            'answers'          => json_encode($answers, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'ip_address'       => $this->request->getIPAddress(),
        ]);

        session()->set(self::SESSION_KEY, strtok($name, ' '));

        return redirect()->to($this->url('thank-you'));
    }

    public function thankYou()
    {
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        $firstName = session()->get(self::SESSION_KEY);

        if (! is_string($firstName)) {
            return redirect()->to($this->url());
        }

        return view('survey/ticketing_thanks', $this->pageData([
            'firstName' => $firstName,
        ]));
    }

    private function url(string $suffix = ''): string
    {
        return site_url($this->config->slug . ($suffix !== '' ? '/' . $suffix : ''));
    }

    private function pageData(array $extra = []): array
    {
        return array_merge([
            'title'           => $this->config->title . ' – LCNL',
            'metaDescription' => 'A short questionnaire for LCNL committee members.',
            'noindex'         => true,
            'config'          => $this->config,
            'sections'        => $this->config->sections(),
            'gridColumns'     => $this->config->gridColumns,
            'isOpen'          => $this->config->isOpen(),
        ], $extra);
    }
}
