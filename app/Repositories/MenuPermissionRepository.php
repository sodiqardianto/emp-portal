<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MenuPermissionRepository
{
    public function paginate(array $criteria): LengthAwarePaginator
    {
        $query = Menu::active()
            ->from('BackOffice.dbo.tbl_menu as M')
            ->select([
                'M.id_menu',
                'M.nama_menu',
                'M.is_active',
                'M.urutan',
                'M.parent_id',
            ])
            ->selectRaw("(SELECT STUFF((SELECT ',' + permission_code FROM BackOffice.dbo.tbl_menu_permission WHERE id_menu = M.id_menu FOR XML PATH('')), 1, 1, '')) AS assigned_permissions");

        $this->applySearch($query, $criteria['search'] ?? '');
        $this->applyActiveFilter($query, $criteria['is_active'] ?? '');
        $this->applySort($query, $criteria['sortBy'] ?? 'urutan', $criteria['sortDir'] ?? 'asc');

        return $query->paginate(
            perPage: $criteria['pageSize'] ?? 10,
            page: $criteria['page'] ?? 1,
        );
    }

    public function findWithPermissions(string $menuId): ?object
    {
        $menu = Menu::from('BackOffice.dbo.tbl_menu as M')
            ->select(['M.id_menu', 'M.nama_menu', 'M.is_active', 'M.parent_id'])
            ->where('M.id_menu', $menuId)
            ->first();

        if (! $menu) {
            return null;
        }

        $menu->assigned_permissions = MenuPermission::where('id_menu', $menuId)
            ->pluck('permission_code')
            ->toArray();

        return $menu;
    }

    public function permissionCatalog(): Collection
    {
        return Permission::where('is_active', 'Y')
            ->orderBy('sort_order')
            ->get(['permission_code', 'permission_name', 'description']);
    }

    public function replacePermissions(string $menuId, array $permissionCodes, string $actor): void
    {
        DB::connection('employee_sqlsrv')->transaction(function () use ($menuId, $permissionCodes, $actor) {
            MenuPermission::where('id_menu', $menuId)->delete();

            $rows = array_map(fn (string $code) => [
                'id_menu' => $menuId,
                'permission_code' => $code,
                'create_date' => DB::raw('GETDATE()'),
                'create_by' => $actor,
            ], $permissionCodes);

            if ($rows) {
                foreach ($rows as $row) {
                    MenuPermission::insert($row);
                }
            }
        });
    }

    public function deleteAllForMenu(string $menuId): void
    {
        MenuPermission::where('id_menu', $menuId)->delete();
    }

    private function applySearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $query->where(function (Builder $q) use ($search) {
            $q->where('M.nama_menu', 'like', "%{$search}%");
        });
    }

    private function applyActiveFilter(Builder $query, string $active): void
    {
        if ($active === '' || ! in_array($active, ['Y', 'N'], true)) {
            return;
        }

        $query->where('M.is_active', $active);
    }

    private function applySort(Builder $query, string $sortBy, string $sortDir): void
    {
        $column = match ($sortBy) {
            'nama_menu' => 'M.nama_menu',
            'is_active' => 'M.is_active',
            default => 'M.urutan',
        };

        $query->orderBy($column, $sortDir)
            ->orderBy('M.id_menu');
    }
}
