<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Adjust to your admin session key
        if (! session()->get('user_id')) {
            return redirect()->to('/auth/login')->with('error', 'Please login as admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
