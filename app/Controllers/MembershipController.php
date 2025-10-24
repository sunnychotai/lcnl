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
        return view('membership/register');
    }

    public function create()
    {
        if (! $this->validate('memberRegister')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $payload = $this->request->getPost();
        $payload['source'] = $this->request->getGet('src') ?? 'web';

        try {
            // 1) Create pending member
            $memberId = $this->svc->createPending($payload);

            // 2) Send verification email
            $this->sendVerificationEmail(
                $memberId,
                $payload['email'],
                $payload['first_name'],
                $payload['last_name'] ?? ''
            );

            // 3) Redirect with success message
            return redirect()->to(route_to('membership.success'))
                ->with('message', 'Please check your email to verify your account.');

        } catch (\Throwable $e) {
            log_message('error', '[MembershipController::create] ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'Sorry, something went wrong creating your account.',
                ]);
        }
    }

    public function success()
    {
        return view('membership/success');
    }

    public function verify(string $token)
    {
        $verifications = new MemberVerificationModel();
        $row = $verifications->findValidToken($token);

        if (! $row) {
            return view('membership/verify_failed');
        }

        // Mark token as used
        $verifications->markUsed((int) $row['id']);

        // Mark member as verified + activate
        $memberModel = new MemberModel();
        $memberModel->update((int) $row['member_id'], [
            'verified_at' => date('Y-m-d H:i:s'),
            'status'      => 'active', // 🚀 auto-activate after email verification
        ]);

        return view('membership/verify_success');
    }

    public function resendVerification()
    {
        $email = strtolower(trim($this->request->getGet('email') ?? ''));

        if (! $email) {
            return redirect()->back()->with('error', 'No email provided.');
        }

        $members = new MemberModel();
        $member  = $members->where('email', $email)->first();

        if (! $member) {
            return redirect()->back()->with('error', 'No member found for that email.');
        }

        if (!empty($member['verified_at'])) {
            return redirect()->back()->with('message', 'Your email is already verified.');
        }

        $this->sendVerificationEmail(
            (int) $member['id'],
            $member['email'],
            $member['first_name'],
            $member['last_name'] ?? ''
        );

        return redirect()->back()->with('message', 'Verification email has been resent.');
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
            'member_id'  => $memberId,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
        ]);

        // 3) Build verification link
        $link = base_url('membership/verify/' . $token);

        // 4) Prepare email content
        $subject  = 'Verify your LCNL account';
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
            'to_email'  => $email,
            'to_name'   => trim($firstName . ' ' . $lastName),
            'subject'   => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'priority'  => 2,
        ]);
    }
}
