<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class MenuPermissionService
{
    public function __construct(private readonly MenuService $menus) {}

    public function can(string $menuHref, string $permissionCode = 'VIEW', ?Authenticatable $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        return $this->menus->canAccess(
            $user->menu_branch,
            (string) $user->EmployeeCode,
            $menuHref,
            $permissionCode,
        );
    }
}
