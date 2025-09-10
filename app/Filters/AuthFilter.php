<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Please log in first');
        }

        // If specific role(s) required, enforce it
        if ($arguments) {
            $userRole = $session->get('user_role');
            if (! in_array($userRole, $arguments)) {
                return redirect()->to('/admin/dashboard')->with('error', 'Access denied');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}
