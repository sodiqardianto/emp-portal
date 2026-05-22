<?php

namespace App\Http\Middleware;

use App\Services\MenuPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMenuAccess
{
    public function __construct(private readonly MenuPermissionService $permissions) {}

    public function handle(Request $request, Closure $next, string $menuHref): Response
    {
        abort_unless(
            $this->permissions->can($menuHref, 'VIEW'),
            403,
        );

        $request->attributes->set('menuHref', $menuHref);
        $request->attributes->set('menuAccessGranted', true);

        return $next($request);
    }
}
