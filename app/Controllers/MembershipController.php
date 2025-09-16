<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\MemberService;
use App\Models\MemberVerificationModel;
use App\Models\EmailQueueModel;

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

            // 2) Generate secure token
            $token = bin2hex(random_bytes(32));

            // 3) Store verification entry
            $verifications = new MemberVerificationModel();
            $verifications->insert([
                'member_id'  => $memberId,
                'token'      => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
            ]);

            // 4) Build verification link
            $link = base_url('membership/verify/' . $token);

            // 5) Prepare email content
            $subject  = 'Verify your LCNL account';
            $bodyHtml = view('emails/verify_member', [
                'link' => $link,
                'name' => $payload['first_name'],
            ]);
            $bodyText = view('emails/verify_member_text', [
                'link' => $link,
                'name' => $payload['first_name'],
            ]);

            // 6) Enqueue email (via EmailQueueModel - array style)
            $queue = new EmailQueueModel();
            $queue->enqueue([
                'to_email'  => $payload['email'],
                'to_name'   => trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? '')),
                'subject'   => $subject,
                'body_html' => $bodyHtml,
                'body_text' => $bodyText,
                'priority'  => 2,
            ]);

            // 7) Redirect with success message
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

        // Activate member
        $memberService = new MemberService();
        $memberService->activate((int) $row['member_id']);

        return view('membership/verify_success');
    }
}
