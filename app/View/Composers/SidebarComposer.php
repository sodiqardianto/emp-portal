<?php

namespace App\View\Composers;

use App\Services\MenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class SidebarComposer
{
    public function __construct(private readonly MenuService $menus) {}

    public function compose(View $view): void
    {
        $sidebarMenus = [];

        if (Auth::check()) {
            try {
                $user = Auth::user();

                $sidebarMenus = $this->menus->getMenuTree(
                    $user->menu_branch,
                    (string) $user->EmployeeCode,
                );
            } catch (Throwable $exception) {
                Log::warning('Failed to load employee sidebar menu.', [
                    'employee_code' => Auth::user()->EmployeeCode ?? null,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $view->with('sidebarMenus', $sidebarMenus);
    }
}
