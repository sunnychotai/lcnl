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

        // --- 1. Not logged in → go to login ---
        if (! $session->get('isAdminLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // --- 2. Role-restricted route handling ---
        if (!empty($arguments)) {
            $userRole = strtoupper($session->get('admin_role') ?? '');
            $allowed  = array_map('strtoupper', $arguments);

            // If not authorised and not already on dashboard, send them there
            if (!in_array($userRole, $allowed)) {
                $currentPath = $request->getUri()->getPath();
                // prevent redirect loop
                if ($currentPath !== 'admin/system/dashboard') {
                    return redirect()->to('/admin/system/dashboard')
                        ->with('error', 'Access denied for your role.');
                }
                // Already on dashboard, just allow so they see “access denied” message
                return;
            }
        }

        // --- 3. Otherwise allow through ---
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
