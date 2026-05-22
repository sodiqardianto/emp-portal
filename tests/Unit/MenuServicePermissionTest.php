<?php

namespace Tests\Unit;

use App\Repositories\MenuRepository;
use App\Services\MenuService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MenuServicePermissionTest extends TestCase
{
    public function test_view_permission_allows_access_when_view_level_exists(): void
    {
        $service = new MenuService(new FakeMenuRepository([
            $this->accessRow(viewLevel: 'Y'),
        ]));

        $this->assertTrue($service->canAccess('BHI', 'EMP001', '/menu/system/permissions', 'VIEW'));
    }

    public function test_action_permission_requires_view_access_too(): void
    {
        $service = new MenuService(new FakeMenuRepository([
            $this->accessRow(viewLevel: null, addLevel: 'Y'),
        ]));

        $this->assertFalse($service->canAccess('BHI', 'EMP001', '/menu/system/permissions', 'ADD'));
    }

    public function test_unknown_permission_code_is_denied(): void
    {
        $service = new MenuService(new FakeMenuRepository([
            $this->accessRow(viewLevel: 'Y'),
        ]));

        $this->assertFalse($service->canAccess('BHI', 'EMP001', '/menu/system/permissions', 'EXPORT'));
    }

    public function test_action_permission_allows_access_when_view_and_action_exist(): void
    {
        $service = new MenuService(new FakeMenuRepository([
            $this->accessRow(viewLevel: 'Y', addLevel: 'Y'),
        ]));

        $this->assertTrue($service->canAccess('BHI', 'EMP001', '/menu/system/permissions', 'ADD'));
    }

    private function accessRow(?string $viewLevel = null, ?string $addLevel = null): object
    {
        return (object) [
            'id_menu' => '2',
            'view_level' => $viewLevel,
            'add_level' => $addLevel,
            'edit_level' => null,
            'delete_level' => null,
            'print_level' => null,
            'upload_level' => null,
            'approve_level' => null,
        ];
    }
}

class FakeMenuRepository extends MenuRepository
{
    /**
     * @param  array<int, object>  $accessRows
     */
    public function __construct(private readonly array $accessRows) {}

    public function findActiveMenus(?string $branch = 'BHI'): Collection
    {
        return collect([
            (object) [
                'id_menu' => '1',
                'nama_menu' => 'System',
                'link' => null,
                'icon' => null,
                'urutan' => 1,
                'parent_id' => '0',
                'Branch' => $branch,
            ],
            (object) [
                'id_menu' => '2',
                'nama_menu' => 'Permissions',
                'link' => null,
                'icon' => null,
                'urutan' => 1,
                'parent_id' => '1',
                'Branch' => $branch,
            ],
        ]);
    }

    public function findMenuAccessByEmployee(string $employeeCode): Collection
    {
        return collect($this->accessRows);
    }
}
