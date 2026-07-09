<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GolfRegistrationModel;
use App\Models\EmailQueueModel;

class GolfController extends BaseController
{
    protected GolfRegistrationModel $model;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->model  = new GolfRegistrationModel();
        $this->emails = new EmailQueueModel();
    }

    public function info()
    {
        return view('golf/info', [
            'title'           => 'LCNL Golf Event 2026 – Moor Park Golf Club',
            'metaDescription' => 'Join LCNL for the Golf Event 2026 at Moor Park Golf Club, Rickmansworth on Wednesday 29th July 2026.',
        ]);
    }

    public function register()
    {
        $formToken = bin2hex(random_bytes(16));
        session()->set('golf_form_token', $formToken);
        session()->set('golf_form_token_time', time());

        $playerCap  = 26;
        $registered = $this->model->countTotalPlayers();
        $remaining  = max(0, $playerCap - $registered);

        return view('golf/register', [
            'title'          => 'LCNL Golf Event 2026 – Register',
            'formToken'      => $formToken,
            'spotsRemaining' => $remaining,
            'isFull'         => $remaining === 0,
        ]);
    }

    public function submit()
    {
        // Time-based honeypot
        $submittedAt = (int) $this->request->getPost('form_time');
        if ($submittedAt === 0 || (time() - $submittedAt) < 3) {
            return redirect()->back()->with('errors', ['Submission rejected. Please try again.']);
        }

        // Hidden field honeypot
        if (!empty($this->request->getPost('website'))) {
            return redirect()->to(site_url('golf/register'));
        }

        // Rate limiting
        $throttler = service('throttler');
        if ($throttler->check('golf-' . $this->request->getIPAddress(), 3, MINUTE) === false) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Too many submissions. Please try again shortly.']);
        }

        $p2Active = trim($this->request->getPost('p2_first_name') ?? '') !== '';
        $p3Active = trim($this->request->getPost('p3_first_name') ?? '') !== '';
        $p4Active = trim($this->request->getPost('p4_first_name') ?? '') !== '';

        // Player cap check
        $playerCap      = 26;
        $incomingCount  = 1 + ($p2Active ? 1 : 0) + ($p3Active ? 1 : 0) + ($p4Active ? 1 : 0);
        $registeredCount = $this->model->countTotalPlayers();
        if ($registeredCount + $incomingCount > $playerCap) {
            $remaining = max(0, $playerCap - $registeredCount);
            $msg = $remaining === 0
                ? 'Sorry, the event is now full. No further registrations are being accepted.'
                : 'Sorry, only ' . $remaining . ' player ' . ($remaining === 1 ? 'spot remains' : 'spots remain') . '. Please reduce the number of players in your registration.';
            return redirect()->back()->withInput()->with('errors', [$msg]);
        }

        // Build validation rules
        $rules = [
            'team_name'     => 'required|min_length[2]|max_length[100]',
            'p1_first_name' => 'required|min_length[2]|max_length[100]',
            'p1_last_name'  => 'required|min_length[2]|max_length[100]',
            'p1_email'      => 'required|valid_email',
            'p1_phone'      => 'required|min_length[7]|max_length[30]',
            'p1_handicap'   => 'required|numeric',
            'p1_meal'       => 'required|in_list[vegetarian,non_vegetarian]',
            'agreed_terms'  => 'required|in_list[1]',
        ];

        if ($p2Active) {
            $rules['p2_first_name'] = 'required|min_length[2]|max_length[100]';
            $rules['p2_last_name']  = 'required|min_length[2]|max_length[100]';
            $rules['p2_email']      = 'required|valid_email';
            $rules['p2_phone']      = 'required|min_length[7]|max_length[30]';
            $rules['p2_handicap']   = 'required|numeric';
            $rules['p2_meal']       = 'required|in_list[vegetarian,non_vegetarian]';
        }

        if ($p3Active) {
            $rules['p3_first_name'] = 'required|min_length[2]|max_length[100]';
            $rules['p3_last_name']  = 'required|min_length[2]|max_length[100]';
            $rules['p3_email']      = 'required|valid_email';
            $rules['p3_phone']      = 'required|min_length[7]|max_length[30]';
            $rules['p3_handicap']   = 'required|numeric';
            $rules['p3_meal']       = 'required|in_list[vegetarian,non_vegetarian]';
        }

        if ($p4Active) {
            $rules['p4_first_name'] = 'required|min_length[2]|max_length[100]';
            $rules['p4_last_name']  = 'required|min_length[2]|max_length[100]';
            $rules['p4_email']      = 'required|valid_email';
            $rules['p4_phone']      = 'required|min_length[7]|max_length[30]';
            $rules['p4_handicap']   = 'required|numeric';
            $rules['p4_meal']       = 'required|in_list[vegetarian,non_vegetarian]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Generate a unique reference based on lead player's surname
        $ref = $this->model->generateRef($this->request->getPost('p1_last_name') ?? '');

        $data = [
            'registration_ref' => $ref,
            'team_name'        => strip_tags($this->request->getPost('team_name') ?? ''),
            'p1_first_name'    => strip_tags($this->request->getPost('p1_first_name')),
            'p1_last_name'     => strip_tags($this->request->getPost('p1_last_name')),
            'p1_email'         => trim($this->request->getPost('p1_email')),
            'p1_phone'         => strip_tags($this->request->getPost('p1_phone')),
            'p1_handicap'      => (float) $this->request->getPost('p1_handicap'),
            'p1_meal'          => $this->request->getPost('p1_meal'),
            'status'           => 'submitted',
            'agreed_terms'     => 1,
            'ip_address'       => $this->request->getIPAddress(),
        ];

        if ($p2Active) {
            $data['p2_first_name'] = strip_tags($this->request->getPost('p2_first_name'));
            $data['p2_last_name']  = strip_tags($this->request->getPost('p2_last_name'));
            $data['p2_email']      = trim($this->request->getPost('p2_email'));
            $data['p2_phone']      = strip_tags($this->request->getPost('p2_phone'));
            $data['p2_handicap']   = (float) $this->request->getPost('p2_handicap');
            $data['p2_meal']       = $this->request->getPost('p2_meal');
        }

        if ($p3Active) {
            $data['p3_first_name'] = strip_tags($this->request->getPost('p3_first_name'));
            $data['p3_last_name']  = strip_tags($this->request->getPost('p3_last_name'));
            $data['p3_email']      = trim($this->request->getPost('p3_email'));
            $data['p3_phone']      = strip_tags($this->request->getPost('p3_phone'));
            $data['p3_handicap']   = (float) $this->request->getPost('p3_handicap');
            $data['p3_meal']       = $this->request->getPost('p3_meal');
        }

        if ($p4Active) {
            $data['p4_first_name'] = strip_tags($this->request->getPost('p4_first_name'));
            $data['p4_last_name']  = strip_tags($this->request->getPost('p4_last_name'));
            $data['p4_email']      = trim($this->request->getPost('p4_email'));
            $data['p4_phone']      = strip_tags($this->request->getPost('p4_phone'));
            $data['p4_handicap']   = (float) $this->request->getPost('p4_handicap');
            $data['p4_meal']       = $this->request->getPost('p4_meal');
        }

        $this->model->insert($data);

        // Build list of players to email
        $players = [[
            'first_name' => $data['p1_first_name'],
            'full_name'  => $data['p1_first_name'] . ' ' . $data['p1_last_name'],
            'email'      => $data['p1_email'],
            'handicap'   => $data['p1_handicap'],
            'meal'       => $data['p1_meal'],
        ]];

        if ($p2Active) {
            $players[] = [
                'first_name' => $data['p2_first_name'],
                'full_name'  => $data['p2_first_name'] . ' ' . $data['p2_last_name'],
                'email'      => $data['p2_email'],
                'handicap'   => $data['p2_handicap'],
                'meal'       => $data['p2_meal'],
            ];
        }

        if ($p3Active) {
            $players[] = [
                'first_name' => $data['p3_first_name'],
                'full_name'  => $data['p3_first_name'] . ' ' . $data['p3_last_name'],
                'email'      => $data['p3_email'],
                'handicap'   => $data['p3_handicap'],
                'meal'       => $data['p3_meal'],
            ];
        }

        if ($p4Active) {
            $players[] = [
                'first_name' => $data['p4_first_name'],
                'full_name'  => $data['p4_first_name'] . ' ' . $data['p4_last_name'],
                'email'      => $data['p4_email'],
                'handicap'   => $data['p4_handicap'],
                'meal'       => $data['p4_meal'],
            ];
        }

        foreach ($players as $player) {
            $html = view('emails/golf_registration', [
                'first_name'       => $player['first_name'],
                'registration_ref' => $ref,
                'team_name'        => $data['team_name'],
                'all_players'      => $players,
                'handicap'         => $player['handicap'],
                'meal'             => $player['meal'],
            ]);

            $this->emails->enqueue([
                'to_email'  => $player['email'],
                'to_name'   => $player['full_name'],
                'subject'   => 'LCNL Golf Event 2026 – Registration Received',
                'body_html' => $html,
                'body_text' => strip_tags($html),
                'priority'  => 1,
            ]);
        }

        return redirect()->to(site_url('golf/confirmation/' . $ref));
    }

    public function confirmation(string $ref)
    {
        $reg = $this->model->where('registration_ref', $ref)->first();

        if (!$reg) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('golf/confirmation', [
            'title' => 'Golf Event 2026 – Registration Received',
            'reg'   => $reg,
        ]);
    }
}
