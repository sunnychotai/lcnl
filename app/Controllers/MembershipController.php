<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\MemberService;
use App\Models\MemberVerificationModel;
use App\Models\EmailQueueModel;
use App\Models\MemberModel;

class MembershipController extends BaseController
{
    private MemberService $svc;

    public function __construct()
    {
        $this->svc = new MemberService();
    }

    public function register()
    {
        return view('membership/register', [
            'title'           => 'Join Us',
            'metaDescription' => 'Register for membership of Lohana Community North London.',
        ]);
    }

    public function activated()
    {
        return view('membership/activated_success', ['title' => 'Account Activated']);
    }

    public function create()
    {
        // Throttle: max 3 attempts per minute per IP
        $throttler = service('throttler');
        if ($throttler->check('register-' . $this->request->getIPAddress(), 3, MINUTE) === false) {
            return redirect()->back()->withInput()->with('errors', ['Too many attempts. Please try again shortly.']);
        }

        // Honeypot: hidden field 'website' should be empty
        if (!empty($this->request->getPost('website'))) {
            // silently succeed (anti-bot)
            return redirect()->to(route_to('membership.success'));
        }

        // Every rule field present (as '' if not posted): the trim rule rejects null
        $payload = array_merge(
            array_fill_keys(array_keys(config('Validation')->memberRegisterV2), ''),
            $this->request->getPost()
        );

        // People type mobiles as "07700 900123" or "(020) 8000-0000"; strip the
        // separators before validating so they aren't rejected for formatting.
        $payload['mobile'] = preg_replace('/[\s\-().]/', '', (string) ($payload['mobile'] ?? ''));

        if (!$this->validateData($payload, 'memberRegisterV2')) {
            // An existing member (often one imported from the old list) trying to
            // register again: point them at login / password reset instead.
            $email = strtolower(trim((string) ($payload['email'] ?? '')));
            $emailExists = $email !== '' && (new MemberModel())->where('email', $email)->countAllResults() > 0;

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('email_exists', $emailExists);
        }

        $payload['source'] = $this->request->getGet('src') ?? 'web';

        try {
            // 1) Create pending member + pending membership + (optional) family
            $memberId = $this->svc->createPending($payload);

            // 2) Send verification email
            $this->sendVerificationEmail(
                $memberId,
                $payload['email'],
                $payload['first_name'],
                $payload['last_name'] ?? ''
            );

            // 3) Show "check your email" with a resend button for this address
            session()->set('pending_email', strtolower(trim($payload['email'])));

            return redirect()->to(route_to('membership.success'));

        } catch (\Throwable $e) {
            log_message('error', '[MembershipController::create V2] ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'Sorry, something went wrong creating your account.',
                ]);
        }
    }

    public function verify(string $token)
    {
        $verifications = new MemberVerificationModel();
        $row = $verifications->findValidToken($token);

        if (!$row) {
            return view('membership/verify_failed', ['title' => 'Verification Failed']);
        }

        // Mark token as used
        $verifications->markUsed((int) $row['id']);

        // Only a pending member is activated by verifying. activate() clears the
        // disabled fields, so a disabled member must never come through here.
        $member = (new MemberModel())->find((int) $row['member_id']);
        $status = $member['status'] ?? null;

        if ($status === 'disabled') {
            return view('membership/verify_failed', [
                'title'    => 'Verification Failed',
                'disabled' => true,
            ]);
        }

        if ($status === 'pending') {
            $this->svc->activate((int) $row['member_id'], null);
        }

        return view('membership/verify_success', ['title' => 'Email Verified']);
    }


    public function success()
    {
        return view('membership/success', ['title' => 'Registration Received']);
    }

    /**
     * GET from the links in the login error and dashboard checklist,
     * POST from the forms on the "check your email" and link-expired pages.
     */
    public function resendVerification()
    {
        $email = strtolower(trim((string) $this->request->getVar('email')));

        if (!$email) {
            return redirect()->back()->with('error', 'Please enter your email address.');
        }

        // Throttle: max 3 resends per 10 minutes per IP
        if (service('throttler')->check('resend-verify-' . $this->request->getIPAddress(), 3, MINUTE * 10) === false) {
            return redirect()->back()->with('error', 'Too many requests. Please wait a few minutes and try again.');
        }

        $members = new MemberModel();
        $member = $members->where('email', $email)->first();

        if (!$member) {
            return redirect()->back()->with('error', 'We couldn’t find an account for that email address. Please check the spelling, or register as a new member.');
        }

        if (($member['status'] ?? '') === 'disabled') {
            return redirect()->back()->with('error', 'This account is closed. Please contact membership@lcnl.org if you think this is a mistake.');
        }

        if (($member['status'] ?? '') !== 'pending') {
            return redirect()->to(route_to('member.login'))->with('message', 'Your email is already verified. Please log in.');
        }

        $this->sendVerificationEmail(
            (int) $member['id'],
            $member['email'],
            $member['first_name'],
            $member['last_name'] ?? ''
        );

        session()->set('pending_email', $email);

        return redirect()->to(route_to('membership.success'))
            ->with('message', 'We’ve sent you a new verification email.');
    }

    private function sendVerificationEmail(
        int $memberId,
        string $email,
        string $firstName,
        string $lastName = ''
    ) {
        // 1) Generate secure token
        $token = bin2hex(random_bytes(32));

        // 2) Store verification entry
        $verifications = new MemberVerificationModel();
        $verifications->insert([
            'member_id' => $memberId,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
        ]);

        // 3) Build verification link
        $link = base_url('membership/verify/' . $token);

        // 4) Prepare email content
        $subject = 'Verify your LCNL account';
        $bodyHtml = view('emails/verify_member', [
            'link' => $link,
            'name' => $firstName,
        ]);
        $bodyText = view('emails/verify_member_text', [
            'link' => $link,
            'name' => $firstName,
        ]);

        // 5) Enqueue email
        $queue = new EmailQueueModel();
        $queue->enqueue([
            'to_email' => $email,
            'to_name' => trim($firstName . ' ' . $lastName),
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'priority' => 2,
        ]);
    }
}
