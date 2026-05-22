<?php

namespace App\Http\Middleware;

use App\Services\MenuPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMenuPermission
{
    public function __construct(private readonly MenuPermissionService $permissions) {}

    public function handle(Request $request, Closure $next, string $permissionCode = 'VIEW'): Response
    {
        $menuHref = $request->attributes->get('menuHref');

        abort_if($menuHref === null, 500, 'Menu access middleware belum dikonfigurasi.');

        if (strtoupper(trim($permissionCode)) === 'VIEW' && $request->attributes->get('menuAccessGranted') === true) {
            return $next($request);
        }

        abort_unless(
            $this->permissions->can($menuHref, $permissionCode),
            403,
        );

        return $next($request);
    }
}
