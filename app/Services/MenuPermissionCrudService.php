<?php

namespace App\Services;

use App\Repositories\MenuPermissionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MenuPermissionCrudService
{
    public function __construct(private readonly MenuPermissionRepository $repository) {}

    public function paginate(array $criteria): LengthAwarePaginator
    {
        return $this->repository->paginate($criteria);
    }

    public function detail(string $menuId): object
    {
        $menu = $this->repository->findWithPermissions($menuId);

        if (! $menu) {
            throw (new ModelNotFoundException)->setModel('Menu', $menuId);
        }

        return $menu;
    }

    public function permissionCatalog(): Collection
    {
        return $this->repository->permissionCatalog();
    }

    public function updatePermissions(string $menuId, array $permissionCodes, string $actor): void
    {
        $menu = $this->repository->findWithPermissions($menuId);

        if (! $menu) {
            throw (new ModelNotFoundException)->setModel('Menu', $menuId);
        }

        $this->repository->replacePermissions($menuId, $permissionCodes, $actor);
    }

    public function deleteAllPermissions(string $menuId): void
    {
        $menu = $this->repository->findWithPermissions($menuId);

        if (! $menu) {
            throw (new ModelNotFoundException)->setModel('Menu', $menuId);
        }

        $this->repository->deleteAllForMenu($menuId);
    }
}
