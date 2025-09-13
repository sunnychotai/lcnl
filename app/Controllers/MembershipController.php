<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\MemberService;

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
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->request->getPost();
        $payload['source'] = $this->request->getGet('src') ?? 'web';

        try {
            $id = $this->svc->createPending($payload);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('errors', ['general' => 'Sorry, something went wrong creating your account.']);
        }

        return redirect()->to(route_to('membership.success'))
                         ->with('pending_email', strtolower($payload['email']));
    }

    public function success()
    {
        return view('membership/success');
    }

    public function verify(string $token)
    {
        // Placeholder until SMTP is enabled
        return redirect()->to('/login')
            ->with('message', 'Email verification coming soon. An admin will activate your membership.');
    }
}
