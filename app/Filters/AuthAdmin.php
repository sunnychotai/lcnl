<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isAdminLoggedIn')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Please login as an administrator.');
        }

        // Optionally enforce roles
        if ($arguments) {
            $role = $session->get('admin_role');
            if (! in_array($role, $arguments)) {
                return redirect()->to('/admin/system/dashboard')
                    ->with('error', 'Access denied.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
