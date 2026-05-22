<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PermissionService
{
    public function __construct(private readonly PermissionRepository $permissions) {}

    public function paginate(array $criteria): LengthAwarePaginator
    {
        return $this->permissions->paginate($criteria);
    }

    public function create(array $data, string $actor): void
    {
        $data['sort_order'] ??= $this->permissions->nextSortOrder();
        $data['is_active'] ??= 'Y';

        if ($this->permissions->findByCode($data['permission_code'])) {
            throw ValidationException::withMessages([
                'permission_code' => 'Permission code sudah digunakan.',
            ]);
        }

        $this->permissions->create($data, $actor);
    }

    public function update(string $code, array $data, string $actor): void
    {
        $existing = $this->permissions->findByCode($code);

        if (! $existing) {
            throw (new ModelNotFoundException)->setModel(Permission::class, $code);
        }

        if ($data['permission_code'] !== strtoupper(trim($code))) {
            throw ValidationException::withMessages([
                'permission_code' => 'Permission code tidak dapat diubah.',
            ]);
        }

        $data['sort_order'] ??= (int) $existing->sort_order;
        $data['is_active'] ??= $existing->is_active ?? 'Y';

        $this->permissions->update($code, $data, $actor);
    }

    public function delete(string $code): void
    {
        $existing = $this->permissions->findByCode($code);

        if (! $existing) {
            throw (new ModelNotFoundException)->setModel(Permission::class, $code);
        }

        if ((int) ($existing->menu_usage_count ?? 0) > 0 || (int) ($existing->user_usage_count ?? 0) > 0) {
            throw ValidationException::withMessages([
                'delete' => 'Permission masih dipakai pada menu atau hak akses user dan tidak bisa dihapus.',
            ]);
        }

        $this->permissions->delete($code);
    }
}
