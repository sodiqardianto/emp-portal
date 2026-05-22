<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeMenuPermission;
use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeAccessRepository
{
    /**
     * List employees who have at least one permission assigned.
     */
    public function paginate(array $criteria): LengthAwarePaginator
    {
        $employeeCodes = EmployeeMenuPermission::select('employee_code')
            ->distinct()
            ->pluck('employee_code');

        $query = Employee::query()
            ->select(['EmployeeCode', 'EmployeeName', 'DivName', 'DeptName', 'UnitName'])
            ->whereIn('EmployeeCode', $employeeCodes);

        $this->applyFilters($query, $criteria);
        $this->applySort($query, $criteria['sortBy'] ?? 'EmployeeCode', $criteria['sortDir'] ?? 'asc');

        return $query->paginate(
            perPage: $criteria['pageSize'] ?? 10,
            page: $criteria['page'] ?? 1,
        );
    }

    /**
     * Get employee info for the edit page.
     */
    public function findEmployee(string $employeeCode): ?Employee
    {
        return Employee::select(['EmployeeCode', 'EmployeeName', 'DivName', 'DeptName', 'UnitName'])
            ->where('EmployeeCode', $employeeCode)
            ->first();
    }

    /**
     * Get all menus with their available permissions and employee's granted permissions.
     */
    public function getAccessMatrix(string $employeeCode): array
    {
        // All active menus sorted as tree
        $rawMenus = Menu::active()
            ->select([
                DB::raw('CAST(id_menu AS varchar(50)) AS id_menu'),
                'nama_menu',
                DB::raw('CAST(parent_id AS varchar(50)) AS parent_id'),
                'urutan',
            ])
            ->orderByRaw('ISNULL(urutan, 2147483647)')
            ->orderBy('id_menu')
            ->get();

        $menus = $this->sortAsTree($rawMenus);

        // Available permissions per menu (from tbl_menu_permission)
        $menuPerms = MenuPermission::select(['id_menu', 'permission_code'])->get()
            ->groupBy(fn ($item) => trim((string) $item->id_menu))
            ->map(fn ($items) => $items->pluck('permission_code')->toArray());

        // Employee's current granted permissions
        $granted = EmployeeMenuPermission::where('employee_code', $employeeCode)
            ->select(['id_menu', 'permission_code'])
            ->get()
            ->groupBy(fn ($item) => trim((string) $item->id_menu))
            ->map(fn ($items) => $items->pluck('permission_code')->toArray());

        // Permission catalog
        $catalog = Permission::where('is_active', 'Y')
            ->orderBy('sort_order')
            ->get(['permission_code', 'permission_name', 'description']);

        return [
            'menus' => $menus,
            'menuPermissions' => $menuPerms,
            'grantedPermissions' => $granted,
            'catalog' => $catalog,
        ];
    }

    /**
     * Replace all permissions for an employee (dual-write: flexible + legacy).
     */
    public function replaceAccess(string $employeeCode, array $assignments, string $actor): void
    {
        DB::connection('employee_sqlsrv')->transaction(function () use ($employeeCode, $assignments, $actor) {
            // Clear flexible model
            EmployeeMenuPermission::where('employee_code', $employeeCode)->delete();

            // Clear legacy model
            DB::connection('employee_sqlsrv')
                ->table('BackOffice.dbo.tbl_akses_menu')
                ->where('EmployeeCode', $employeeCode)
                ->delete();

            // Insert flexible model
            foreach ($assignments as $assignment) {
                $menuId = $assignment['menuId'];
                foreach ($assignment['permissionCodes'] as $code) {
                    EmployeeMenuPermission::insert([
                        'employee_code' => $employeeCode,
                        'id_menu' => $menuId,
                        'permission_code' => $code,
                        'create_date' => DB::raw('GETDATE()'),
                        'create_by' => $actor,
                    ]);
                }

                // Insert legacy model
                $codes = $assignment['permissionCodes'];
                DB::connection('employee_sqlsrv')
                    ->table('BackOffice.dbo.tbl_akses_menu')
                    ->insert([
                        'EmployeeCode' => $employeeCode,
                        'id_menu' => $menuId,
                        'view_level' => in_array('VIEW', $codes) ? 'Y' : null,
                        'add_level' => in_array('ADD', $codes) ? 'Y' : null,
                        'edit_level' => in_array('EDIT', $codes) ? 'Y' : null,
                        'delete_level' => in_array('DELETE', $codes) ? 'Y' : null,
                        'print_level' => in_array('PRINT', $codes) ? 'Y' : null,
                        'upload_level' => in_array('UPLOAD', $codes) ? 'Y' : null,
                        'approve_level' => in_array('APPROVE', $codes) ? 'Y' : null,
                    ]);
            }
        });
    }

    /**
     * Delete all access for an employee.
     */
    public function deleteAccess(string $employeeCode): void
    {
        DB::connection('employee_sqlsrv')->transaction(function () use ($employeeCode) {
            EmployeeMenuPermission::where('employee_code', $employeeCode)->delete();
            DB::connection('employee_sqlsrv')
                ->table('BackOffice.dbo.tbl_akses_menu')
                ->where('EmployeeCode', $employeeCode)
                ->delete();
        });
    }

    private function sortAsTree(Collection $menus): Collection
    {
        $byParent = $menus->groupBy(function ($m) {
            $pid = trim((string) $m->parent_id);
            return ($pid === '' || $pid === '0') ? '__root__' : $pid;
        });

        $result = collect();

        $walk = function (string $parentKey) use (&$walk, $byParent, &$result): void {
            foreach ($byParent->get($parentKey, collect()) as $menu) {
                $result->push($menu);
                $walk(trim((string) $menu->id_menu));
            }
        };

        $walk('__root__');

        return $result;
    }

    private function applyFilters(Builder $query, array $criteria): void
    {
        if (! empty($criteria['filters'])) {
            foreach ($criteria['filters'] as $filter) {
                $field = $filter['field'] ?? '';
                $value = trim($filter['value'] ?? '');
                if ($value === '') continue;

                $column = match ($field) {
                    'EmployeeCode' => 'EmployeeCode',
                    'EmployeeName' => 'EmployeeName',
                    'DivName' => 'DivName',
                    'DeptName' => 'DeptName',
                    'UnitName' => 'UnitName',
                    default => null,
                };

                if ($column) {
                    $query->where($column, 'like', "%{$value}%");
                }
            }
        }
    }

    private function applySort(Builder $query, string $sortBy, string $sortDir): void
    {
        $column = match ($sortBy) {
            'EmployeeName' => 'EmployeeName',
            'DivName' => 'DivName',
            'DeptName' => 'DeptName',
            'UnitName' => 'UnitName',
            default => 'EmployeeCode',
        };

        $query->orderBy($column, $sortDir);
    }
}
