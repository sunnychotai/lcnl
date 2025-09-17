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
        $email = strtolower(trim($this->request->getPost('email')));
        $member = (new MemberModel())->where('email', $email)->first();

        if (! $member) {
            return redirect()->back()->with('error', 'No account found with that email.');
        }

        $token = bin2hex(random_bytes(32));
        $resets = new PasswordResetModel();
        $resets->insert([
            'member_id'  => $member['id'],
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')),
        ]);

        $link = base_url('member/reset/' . $token);

        // Queue email
        (new EmailQueueModel())->enqueue([
            'to_email'  => $email,
            'to_name'   => $member['first_name'].' '.$member['last_name'],
            'subject'   => 'Reset your LCNL password',
            'body_html' => view('emails/reset_password', ['link' => $link,'name' => $member['first_name']]),
            'body_text' => view('emails/reset_password_text', ['link' => $link,'name' => $member['first_name']]),
        ]);

        return redirect()->to('/member/login')->with('message','Check your email for reset instructions.');
    }

    public function reset(string $token)
    {
        $row = (new PasswordResetModel())->findValidToken($token);
        if (! $row) {
            return view('account/reset_invalid');
        }

        return view('account/reset_password', ['token' => $token]);
    }

    public function doReset()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $reset = (new PasswordResetModel())->findValidToken($token);
        if (! $reset) {
            return redirect()->to('/member/login')->with('error','Invalid or expired reset link.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        (new MemberModel())->update($reset['member_id'], ['password_hash' => $hash]);

        (new PasswordResetModel())->update($reset['id'], ['used_at' => date('Y-m-d H:i:s')]);

return redirect()
    ->to(route_to('member.login'))
    ->with('message', 'Your password has been updated successfully. Please log in.');
    }
}
