<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionDataRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\MenuPermissionService;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    private const MENU_HREF = '/menu/system/permissions';

    public function __construct(
        private readonly PermissionService $permissions,
        private readonly MenuPermissionService $menuPermissions,
    ) {}

    public function index(): View
    {
        return view('permissions.index', [
            'canAdd' => $this->menuPermissions->can(self::MENU_HREF, 'ADD'),
            'canEdit' => $this->menuPermissions->can(self::MENU_HREF, 'EDIT'),
            'canDelete' => $this->menuPermissions->can(self::MENU_HREF, 'DELETE'),
        ]);
    }

    public function data(PermissionDataRequest $request): JsonResponse
    {
        $permissions = $this->permissions->paginate($request->queryParams());

        return response()->json([
            'data' => PermissionResource::collection($permissions->getCollection())->resolve(),
            'last_page' => max($permissions->lastPage(), 1),
            'last_row' => $permissions->total(),
        ]);
    }

    public function store(PermissionRequest $request): RedirectResponse
    {
        $this->permissions->create($request->validated(), (string) $request->user()->EmployeeCode);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dibuat.');
    }

    public function update(PermissionRequest $request, string $code): RedirectResponse
    {
        $this->permissions->update($code, $request->validated(), (string) $request->user()->EmployeeCode);

        return redirect()
            ->route('permissions.index', $request->query())
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(string $code): RedirectResponse
    {
        $this->permissions->delete($code);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
