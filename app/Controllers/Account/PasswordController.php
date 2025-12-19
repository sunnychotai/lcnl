<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\PasswordResetModel;
use App\Models\EmailQueueModel;

class PasswordController extends BaseController
{
    public function forgot()
    {
        return view('account/forgot_password');
    }

    public function sendReset()
{
    $email = trim($this->request->getPost('email'));

    if (!$email) {
        return redirect()->back()->with('error', 'Email is required.');
    }

    $memberModel = new MemberModel();
    $member = $memberModel->where('email', $email)->first();

    // SECURITY: always respond with success (no email enumeration)
    if (!$member) {
        return redirect()->back()
            ->with('message', 'If an account exists, a reset link has been sent.');
    }

    // -------------------------------------------------
    // Create reset token
    // -------------------------------------------------
    $resetModel = new PasswordResetModel();
    $token = bin2hex(random_bytes(32));

    $resetModel->insert([
        'member_id' => $member['id'],
        'token' => $token,
        'expires_at' => date('Y-m-d H:i:s', strtotime('+72 hours')),
    ]);

    // -------------------------------------------------
    // Send reset email
    // -------------------------------------------------
$resetLink = site_url(route_to('member.reset', $token));

    $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));

    (new EmailQueueModel())->enqueue([
        'to_email' => $member['email'],
        'to_name'  => $fullName,
        'subject'  => 'Reset your LCNL password',
        'body_html'=> view('emails/reset_password', [
            'name' => $fullName,
            'link' => $resetLink
        ]),
        'body_text'=> "Reset your password:\n\n" . $resetLink,
        'priority' => 1,
    ]);

    return redirect()->back()
        ->with('message', 'If an account exists, a reset link has been sent.');
}


    public function reset(string $token)
    {
        $row = (new PasswordResetModel())->findValidToken($token);

        if (!$row) {
            return view('account/reset_invalid');
        }

        return view('account/reset_password', [
            'token' => $token
        ]);
    }

    public function doReset()
    {
        $session = session();
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $reset = (new PasswordResetModel())->findValidToken($token);

        if (!$reset) {
            return redirect()->to('/membership/login')
                ->with('error', 'Invalid or expired reset link.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->find($reset['member_id']);

        if (!$member) {
            return redirect()->to('/membership/login')
                ->with('error', 'Account not found.');
        }

        // -------------------------------------------------
        // Update password
        // -------------------------------------------------
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $updateData = ['password_hash' => $hash];

        // -------------------------------------------------
        // Detect first-time activation
        // -------------------------------------------------
        $status = strtolower(trim($member['status'] ?? ''));
        $isActivation = ($status === 'pending');

        if ($isActivation) {
            $now = date('Y-m-d H:i:s');
            $updateData['status'] = 'active';
            $updateData['verified_at'] = $now;
            $updateData['activated_at'] = $now;
        }

        // -------------------------------------------------
        // Save member updates
        // -------------------------------------------------
        $memberModel->update($member['id'], $updateData);

        // -------------------------------------------------
        // Invalidate token
        // -------------------------------------------------
        (new PasswordResetModel())->update($reset['id'], [
            'used_at' => date('Y-m-d H:i:s')
        ]);

        // -------------------------------------------------
        // Activation-specific flow
        // -------------------------------------------------
        if ($isActivation) {

            $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));

            // 1) Queue membership activated email
            (new EmailQueueModel())->enqueue([
                'to_email' => $member['email'],
                'to_name' => $fullName,
                'subject' => 'Your LCNL Account is Now Active',
                'body_html' => view('emails/membership_activated', ['name' => $fullName]),
                'body_text' => strip_tags(view('emails/membership_activated', ['name' => $fullName])),
                'priority' => 2,
            ]);

            // 2) AUTO-LOGIN the member
            $session->set([
                'member_id' => $member['id'],
                'member_name' => $fullName,
                'isMemberLoggedIn' => true,
            ]);

            // 3) Redirect to dashboard
            return redirect()
                ->to('/account/dashboard')
                ->with('message', 'Welcome! Your account is now active.');
        }

        // -------------------------------------------------
        // Normal password reset flow (non-activation)
        // -------------------------------------------------
        return redirect()
            ->to(route_to('member.login'))
            ->with('message', 'Your password has been updated successfully. Please log in.');
    }
}
