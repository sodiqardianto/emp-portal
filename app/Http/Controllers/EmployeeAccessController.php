<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeAccessDataRequest;
use App\Services\EmployeeAccessService;
use App\Services\MenuPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeAccessController extends Controller
{
    private const MENU_HREF = '/menu/system/hak-akses';

    public function __construct(
        private readonly EmployeeAccessService $service,
        private readonly MenuPermissionService $permissions,
    ) {}

    public function index(): View
    {
        return view('employee-access.index', [
            'canEdit' => $this->permissions->can(self::MENU_HREF, 'EDIT'),
            'canDelete' => $this->permissions->can(self::MENU_HREF, 'DELETE'),
        ]);
    }

    public function data(EmployeeAccessDataRequest $request): JsonResponse
    {
        $result = $this->service->paginate($request->queryParams());

        return response()->json([
            'data' => $result->getCollection()->map(fn ($emp) => [
                'EmployeeCode' => $emp->EmployeeCode,
                'EmployeeName' => $emp->EmployeeName,
                'DivName' => $emp->DivName,
                'DeptName' => $emp->DeptName,
                'UnitName' => $emp->UnitName,
            ]),
            'last_page' => max($result->lastPage(), 1),
            'last_row' => $result->total(),
        ]);
    }

    public function edit(string $employeeCode): View
    {
        $data = $this->service->getEditData($employeeCode);

        return view('employee-access.edit', [
            'employee' => $data['employee'],
            'menus' => $data['menus'],
            'menuPermissions' => $data['menuPermissions'],
            'grantedPermissions' => $data['grantedPermissions'],
            'catalog' => $data['catalog'],
            'canEdit' => $this->permissions->can(self::MENU_HREF, 'EDIT'),
        ]);
    }

    public function update(Request $request, string $employeeCode): RedirectResponse
    {
        $raw = $request->input('assignments', []);

        // Convert form format: assignments[menuId][] = code → [{menuId, permissionCodes}]
        $assignments = [];
        if (is_array($raw)) {
            foreach ($raw as $menuId => $codes) {
                if (is_array($codes) && count($codes)) {
                    $assignments[] = ['menuId' => (string) $menuId, 'permissionCodes' => $codes];
                }
            }
        }

        $this->service->save(
            $employeeCode,
            $assignments,
            (string) $request->user()->EmployeeCode,
        );

        return redirect()
            ->route('employee-access.edit', $employeeCode)
            ->with('success', 'Hak akses berhasil diperbarui.');
    }

    public function destroy(string $employeeCode): RedirectResponse
    {
        $this->service->delete($employeeCode);

        return redirect()
            ->route('employee-access.index')
            ->with('success', 'Semua hak akses berhasil dihapus.');
    }
}
