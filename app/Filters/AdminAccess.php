<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 'filter' => 'can:gallery' lets an admin through if their role can use
 * that feature (Config\AdminAccess). With several features, any one will do.
 */
class AdminAccess implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isAdminLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        helper('role');

        if (!canAccess(...(array) $arguments)) {
            // The dashboard is open to every role, so it's a safe place to land.
            if (trim($request->getUri()->getPath(), '/') !== 'admin/system/dashboard') {
                return redirect()->to('/admin/system/dashboard')
                    ->with('error', 'Your role doesn\'t have access to that page.');
            }

            return redirect()->to('/auth/login')->with('error', 'Your role doesn\'t have access to the admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
