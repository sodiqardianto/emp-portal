<?php

namespace App\Repositories;

use App\Models\EmployeeMenuPermission;
use App\Models\MenuPermission;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    public function paginate(array $criteria): LengthAwarePaginator
    {
        $query = $this->query();

        $this->applySearch($query, $criteria['search']);
        $this->applyActiveFilter($query, $criteria['is_active']);
        $this->applyHeaderFilters($query, $criteria['filters']);
        $this->applySort($query, $criteria['sortBy'], $criteria['sortDir']);

        return $query->paginate(
            perPage: $criteria['pageSize'],
            page: $criteria['page'],
        );
    }

    public function findByCode(string $code): ?Permission
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return null;
        }

        return $this->query()->where('P.permission_code', $code)->first();
    }

    public function nextSortOrder(): int
    {
        return (int) Permission::max('sort_order') + 1;
    }

    public function create(array $data, string $actor): void
    {
        Permission::create([
            'permission_code' => $data['permission_code'],
            'permission_name' => $data['permission_name'],
            'description' => $data['description'],
            'sort_order' => $data['sort_order'],
            'is_active' => $data['is_active'],
            'create_date' => DB::raw('GETDATE()'),
            'create_by' => $actor,
        ]);
    }

    public function update(string $code, array $data, string $actor): void
    {
        Permission::where('permission_code', strtoupper(trim($code)))
            ->update([
                'permission_name' => $data['permission_name'],
                'description' => $data['description'],
                'sort_order' => $data['sort_order'],
                'is_active' => $data['is_active'],
                'update_date' => DB::raw('GETDATE()'),
                'update_by' => $actor,
            ]);
    }

    public function delete(string $code): void
    {
        Permission::where('permission_code', strtoupper(trim($code)))->delete();
    }

    private function query(): Builder
    {
        $menuUsage = MenuPermission::select('permission_code')
            ->selectRaw('COUNT(*) AS menu_usage_count')
            ->groupBy('permission_code');

        $userUsage = EmployeeMenuPermission::select('permission_code')
            ->selectRaw('COUNT(*) AS user_usage_count')
            ->groupBy('permission_code');

        return Permission::from('BackOffice.dbo.mst_permission as P')
            ->select([
                'P.permission_code',
                'P.permission_name',
                'P.description',
                'P.sort_order',
                'P.is_active',
                'P.create_date',
                'P.create_by',
                'P.update_date',
                'P.update_by',
                DB::raw('COALESCE(MP.menu_usage_count, 0) AS menu_usage_count'),
                DB::raw('COALESCE(EMP.user_usage_count, 0) AS user_usage_count'),
            ])
            ->leftJoinSub($menuUsage, 'MP', fn (JoinClause $join) => $join->on('MP.permission_code', '=', 'P.permission_code'))
            ->leftJoinSub($userUsage, 'EMP', fn (JoinClause $join) => $join->on('EMP.permission_code', '=', 'P.permission_code'));
    }

    private function applySearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $query->where(function (Builder $q) use ($search): void {
            $q->where('P.permission_code', 'like', "%{$search}%")
                ->orWhere('P.permission_name', 'like', "%{$search}%")
                ->orWhere('P.description', 'like', "%{$search}%")
                ->orWhere('P.create_by', 'like', "%{$search}%")
                ->orWhere('P.update_by', 'like', "%{$search}%");
        });
    }

    private function applyActiveFilter(Builder $query, string $active): void
    {
        if ($active === '') {
            return;
        }

        $query->where('P.is_active', $active);
    }

    private function applyHeaderFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            match ($field) {
                'permission_code' => $query->where('P.permission_code', 'like', "%{$value}%"),
                'permission_name' => $query->where('P.permission_name', 'like', "%{$value}%"),
                'description' => $query->where('P.description', 'like', "%{$value}%"),
                'sort_order' => $query->whereRaw('CAST(P.sort_order AS VARCHAR(20)) LIKE ?', ["%{$value}%"]),
                'is_active' => $query->where('P.is_active', strtoupper($value)),
                default => null,
            };
        }
    }

    private function applySort(Builder $query, string $sortBy, string $sortDir): void
    {
        if ($sortBy === 'sort_order') {
            $query->orderByRaw('CASE WHEN P.sort_order IS NULL THEN 1 ELSE 0 END');
        }

        $query->orderBy($this->sortColumn($sortBy), $sortDir);

        if ($sortBy !== 'permission_code') {
            $query->orderBy('P.permission_code');
        }
    }

    private function sortColumn(string $sortBy): string
    {
        return match ($sortBy) {
            'permission_code' => 'P.permission_code',
            'permission_name' => 'P.permission_name',
            'is_active' => 'P.is_active',
            default => 'P.sort_order',
        };
    }
}
