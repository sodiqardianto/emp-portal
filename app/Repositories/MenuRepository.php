<?php

namespace App\Repositories;

use App\Models\EmployeeMenuPermission;
use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MenuRepository
{
    private const CACHE_TTL = 300; // 5 minutes

    public function findActiveMenus(?string $branch = 'BHI'): Collection
    {
        $branch = trim((string) $branch);

        return Cache::remember("menus:active:{$branch}", self::CACHE_TTL, function () use ($branch): Collection {
            $query = Menu::active()
                ->select([
                    DB::raw('CAST(id_menu AS varchar(50)) AS id_menu'),
                    'nama_menu',
                    'link',
                    'icon',
                    'urutan',
                    'is_active',
                    'parent',
                    'status_menu',
                    DB::raw('CAST(parent_id AS varchar(50)) AS parent_id'),
                ]);

            if ($this->hasBranchColumn()) {
                $query->addSelect(DB::raw('CAST(Branch AS varchar(50)) AS Branch'))
                    ->forBranch($branch);
            } else {
                $query->addSelect(DB::raw('CAST(NULL AS varchar(50)) AS Branch'));
            }

            return $query
                ->orderByRaw('CASE WHEN TRY_CAST(parent_id AS bigint) IS NULL THEN 0 ELSE 1 END')
                ->orderByRaw('TRY_CAST(parent_id AS bigint)')
                ->orderByRaw('ISNULL(urutan, 2147483647)')
                ->orderByRaw('TRY_CAST(id_menu AS bigint)')
                ->get();
        });
    }

    public function findMenuAccessByEmployee(string $employeeCode): Collection
    {
        return Cache::remember("menus:access:{$employeeCode}", self::CACHE_TTL, function () use ($employeeCode): Collection {
            if ($this->employeeMenuPermissionTableExists()) {
                $result = EmployeeMenuPermission::select(DB::raw('CAST(id_menu AS varchar(50)) AS id_menu'))
                    ->selectRaw("MAX(CASE WHEN permission_code = 'VIEW' THEN 'Y' END) AS view_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'ADD' THEN 'Y' END) AS add_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'EDIT' THEN 'Y' END) AS edit_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'DELETE' THEN 'Y' END) AS delete_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'PRINT' THEN 'Y' END) AS print_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'UPLOAD' THEN 'Y' END) AS upload_level")
                    ->selectRaw("MAX(CASE WHEN permission_code = 'APPROVE' THEN 'Y' END) AS approve_level")
                    ->where('employee_code', $employeeCode)
                    ->groupBy('id_menu')
                    ->get();

                if ($result->isNotEmpty()) {
                    return $result;
                }
            }

            return DB::connection('employee_sqlsrv')
                ->table('BackOffice.dbo.tbl_akses_menu')
                ->select([
                    DB::raw('CAST(id_menu AS varchar(50)) AS id_menu'),
                    'view_level',
                    'add_level',
                    'edit_level',
                    'delete_level',
                    'print_level',
                    'upload_level',
                    'approve_level',
                ])
                ->where('EmployeeCode', $employeeCode)
                ->get();
        });
    }

    private function hasBranchColumn(): bool
    {
        return Cache::remember('schema:tbl_menu:has_branch', 3600, function (): bool {
            $row = DB::connection('employee_sqlsrv')->selectOne(
                "SELECT 1 AS exists_flag FROM [BackOffice].INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                ['dbo', 'tbl_menu', 'Branch'],
            );

            return $row !== null;
        });
    }

    private function employeeMenuPermissionTableExists(): bool
    {
        return Cache::remember('schema:tbl_employee_menu_permission:exists', 3600, function (): bool {
            $row = DB::connection('employee_sqlsrv')->selectOne(
                "SELECT 1 AS exists_flag FROM [BackOffice].INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                ['dbo', 'tbl_employee_menu_permission'],
            );

            return $row !== null;
        });
    }
}
