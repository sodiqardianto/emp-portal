<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MenuService
{

    private const PERMISSION_COLUMNS = [
        'VIEW' => 'view_level',
        'ADD' => 'add_level',
        'EDIT' => 'edit_level',
        'DELETE' => 'delete_level',
        'PRINT' => 'print_level',
        'UPLOAD' => 'upload_level',
        'APPROVE' => 'approve_level',
    ];

    public function __construct(private readonly MenuRepository $repository) {}

    public function getMenuTree(string $branch = 'BHI', ?string $employeeCode = null): array
    {
        $rows = $this->repository->findActiveMenus($branch);
        $filteredRows = $employeeCode
            ? $this->filterRowsByEmployeeAccess($rows, $employeeCode)
            : $rows;

        return $this->buildMenuTree($filteredRows);
    }

    public function canAccess(string $branch, string $employeeCode, string $menuHref, string $permissionCode = 'VIEW'): bool
    {
        $rows = $this->repository->findActiveMenus($branch);
        $menuId = $this->buildMenuHrefMap($rows)->get($this->normalizeHref($menuHref));

        if (! $menuId) {
            return false;
        }

        $accessRow = $this->repository
            ->findMenuAccessByEmployee($employeeCode)
            ->first(fn (object $row): bool => trim((string) $row->id_menu) === $menuId);

        if (! $accessRow) {
            return false;
        }

        $column = self::PERMISSION_COLUMNS[strtoupper(trim($permissionCode))] ?? null;

        if ($column === null || ! $this->hasPermissionValue($accessRow->view_level ?? null)) {
            return false;
        }

        return $this->hasPermissionValue($accessRow->{$column} ?? null);
    }

    private function filterRowsByEmployeeAccess(Collection $rows, string $employeeCode): Collection
    {
        $accessRows = $this->repository->findMenuAccessByEmployee($employeeCode);

        if ($accessRows->isEmpty()) {
            return collect();
        }

        $accessibleIds = $accessRows
            ->filter(fn (object $row): bool => $this->hasPermissionValue($row->view_level ?? null))
            ->map(fn (object $row): string => trim((string) $row->id_menu))
            ->filter()
            ->values();

        if ($accessibleIds->isEmpty()) {
            return collect();
        }

        $byId = $rows
            ->filter(fn (object $row): bool => trim((string) $row->id_menu) !== '')
            ->keyBy(fn (object $row): string => trim((string) $row->id_menu));

        $includedIds = [];

        foreach ($accessibleIds as $menuId) {
            $current = $byId->get($menuId);

            while ($current) {
                $currentId = trim((string) $current->id_menu);

                if (isset($includedIds[$currentId])) {
                    break;
                }

                $includedIds[$currentId] = true;
                $parentId = $this->normalizeParentId($current->parent_id ?? null);

                if ($parentId === null) {
                    break;
                }

                $current = $byId->get($parentId);
            }
        }

        return $rows->filter(
            fn (object $row): bool => isset($includedIds[trim((string) $row->id_menu)]),
        )->values();
    }

    private function hasPermissionValue(mixed $value): bool
    {
        $value = $this->trimToNull($value);

        return $value !== null && $value !== '0';
    }

    private function buildMenuTree(Collection $rows): array
    {
        $nodes = [];

        foreach ($rows as $row) {
            $id = trim((string) $row->id_menu);

            if ($id === '') {
                continue;
            }

            $nodes[$id] = [
                'id' => $id,
                'label' => $this->trimToNull($row->nama_menu ?? null) ?? "Menu {$id}",
                'link' => $this->trimToNull($row->link ?? null),
                'icon' => $this->trimToNull($row->icon ?? null),
                'order' => $row->urutan ?? PHP_INT_MAX,
                'parentId' => $this->normalizeParentId($row->parent_id ?? null),
                'depth' => 1,
                'branch' => $this->trimToNull($row->Branch ?? null),
                'href' => '#',
                'children' => [],
            ];
        }

        $roots = [];

        foreach (array_keys($nodes) as $id) {
            $parentId = $nodes[$id]['parentId'];

            if ($parentId === null || ! isset($nodes[$parentId])) {
                $nodes[$id]['parentId'] = null;
                $roots[] = &$nodes[$id];

                continue;
            }

            $nodes[$id]['depth'] = $nodes[$parentId]['depth'] + 1;
            $nodes[$parentId]['children'][] = &$nodes[$id];
        }

        unset($id);

        $this->sortTree($roots);
        $this->assignHref($roots);

        return $roots;
    }

    private function sortTree(array &$items): void
    {
        usort($items, function (array $left, array $right): int {
            return $left['order'] !== $right['order']
                ? $left['order'] <=> $right['order']
                : strcmp($left['label'], $right['label']);
        });

        foreach ($items as &$item) {
            $this->sortTree($item['children']);
        }
    }

    private function assignHref(array &$items, array $parentTrail = []): void
    {
        foreach ($items as &$item) {
            $trail = [...$parentTrail, $item['label']];

            if (count($item['children']) === 0) {
                $link = Str::lower((string) $item['link']);
                $label = Str::lower((string) $item['label']);

                $item['href'] = $link === 'dashboard/home' || $label === 'dashboard'
                    ? route('home')
                    : '/menu/'.collect($trail)
                        ->map(fn (string $label): string => Str::slug($label))
                        ->filter()
                        ->implode('/');
            }

            $this->assignHref($item['children'], $trail);
        }
    }

    private function buildMenuHrefMap(Collection $rows): Collection
    {
        $tree = $this->buildMenuTree($rows);
        $hrefMap = collect();

        $walk = function (array $items) use (&$walk, $hrefMap): void {
            foreach ($items as $item) {
                if (count($item['children']) === 0) {
                    $hrefMap->put($this->normalizeHref($item['href']), $item['id']);
                }

                $walk($item['children']);
            }
        };

        $walk($tree);

        return $hrefMap;
    }

    private function normalizeHref(string $value): string
    {
        $value = trim($value);

        if ($value === '' || $value === '/') {
            return '/';
        }

        $href = str_starts_with($value, '/') ? $value : "/{$value}";

        return strtolower(rtrim($href, '/'));
    }

    private function normalizeParentId(mixed $value): ?string
    {
        $normalized = $this->trimToNull($value);

        return ($normalized === null || $normalized === '0') ? null : $normalized;
    }

    private function trimToNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
