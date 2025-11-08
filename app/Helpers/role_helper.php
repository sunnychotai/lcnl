<?php

if (!function_exists('hasRole')) {
    function hasRole(string ...$roles): bool
    {
        $userRole = strtoupper(session()->get('admin_role') ?? '');
        foreach ($roles as $r) {
            if ($userRole === strtoupper($r)) {
                return true;
            }
        }
        return false;
    }
}
