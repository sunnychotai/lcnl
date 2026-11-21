<?php

if (!function_exists('hasRole')) {
    /**
     * Whether the logged-in admin has one of these exact roles. To decide
     * what someone may see or do, use canAccess() instead, so the rules
     * stay in Config\AdminAccess.
     */
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

if (!function_exists('canAccess')) {
    /**
     * Whether the logged-in admin can use any of these features
     * (Config\AdminAccess::$features). ADMIN can use everything.
     */
    function canAccess(string ...$features): bool
    {
        if (!session()->get('isAdminLoggedIn')) {
            return false;
        }

        $role = strtoupper((string) session()->get('admin_role'));

        if ($role === 'ADMIN') {
            return true;
        }

        $map = config('AdminAccess')->features;

        foreach ($features as $feature) {
            if (in_array($role, $map[$feature] ?? [], true)) {
                return true;
            }
        }

        return false;
    }
}
