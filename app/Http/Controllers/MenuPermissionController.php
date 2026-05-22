<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuPermissionDataRequest;
use App\Services\MenuPermissionCrudService;
use App\Services\MenuPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuPermissionController extends Controller
{
    private const MENU_HREF = '/menu/system/menu-permissions';

    public function __construct(
        private readonly MenuPermissionCrudService $service,
        private readonly MenuPermissionService $permissions,
    ) {}

    public function index(): View
    {
        return view('menu-permissions.index', [
            'canEdit' => $this->permissions->can(self::MENU_HREF, 'EDIT'),
            'canDelete' => $this->permissions->can(self::MENU_HREF, 'DELETE'),
        ]);
    }

    public function data(MenuPermissionDataRequest $request): JsonResponse
    {
        $result = $this->service->paginate($request->queryParams());

        return response()->json([
            'data' => $result->getCollection()->map(fn ($menu) => [
                'id_menu' => $menu->id_menu,
                'nama_menu' => $menu->nama_menu,
                'is_active' => $menu->is_active,
                'assigned_permissions' => $menu->assigned_permissions
                    ? explode(',', $menu->assigned_permissions)
                    : [],
            ]),
            'last_page' => max($result->lastPage(), 1),
            'last_row' => $result->total(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $menu = $this->service->detail($id);
        $catalog = $this->service->permissionCatalog();

        return response()->json([
            'menu' => [
                'id_menu' => $menu->id_menu,
                'nama_menu' => $menu->nama_menu,
                'assigned_permissions' => $menu->assigned_permissions,
            ],
            'catalog' => $catalog->map(fn ($p) => [
                'permission_code' => $p->permission_code,
                'permission_name' => $p->permission_name,
                'description' => $p->description,
            ]),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $codes = $request->input('permission_codes', []);

        $this->service->updatePermissions(
            $id,
            is_array($codes) ? $codes : [],
            (string) $request->user()->EmployeeCode,
        );

        return redirect()
            ->route('menu-permissions.index')
            ->with('success', 'Permission menu berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->service->deleteAllPermissions($id);

        return redirect()
            ->route('menu-permissions.index')
            ->with('success', 'Semua permission untuk menu berhasil dihapus.');
    }
}
