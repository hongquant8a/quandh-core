<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed permission, role, organization và phân quyền cho dự án.
 *
 * Khi thêm module mới hoặc thêm action (stats, index, show, store, ...) vào module,
 * bắt buộc cập nhật danh sách PERMISSIONS bên dưới cho đầy đủ, sau đó chạy lại seed.
 */
class PermissionSeeder extends Seeder
{
    /** Guard thống nhất cho Spatie (web + API Sanctum), tránh nhân đôi permission trong DB. */
    protected const GUARD = 'web';

    /**
     * Danh sách đầy đủ permission theo module và resource.
     * Định dạng: 'resource.action' — resource trùng prefix API (users, permissions, roles, organizations, posts, post-categories).
     * Khi thêm module/chức năng: bổ sung vào đúng nhóm và chạy sail artisan db:seed --class=PermissionSeeder.
     */
    protected static array $PERMISSIONS = [
        // Core - Users
        'users' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
        ],
        // Core - Permissions (có description, sort_order, parent_id để nhóm frontend)
        'permissions' => [
            'stats', 'index', 'tree', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'export', 'import',
        ],
        // Core - Roles (bảng roles chuẩn Spatie, không có cột status)
        'roles' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'export', 'import',
        ],
        // Core - Organizations (cấu trúc cây parent_id)
        'organizations' => [
            'stats', 'index', 'tree', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
        ],
        // Post - Bài viết
        'posts' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
            'incrementView',
        ],
        // Post - Danh mục bài viết
        'post-categories' => [
            'stats', 'index', 'tree', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
        ],
    ];

    public function run(): void
    {
        $this->migrateGuardApiToWeb();
        $this->seedOrganizations();
        $this->seedPermissions();
        $this->seedRoles();
        $this->assignPermissionsToRoles();
        $this->assignSuperAdminToFirstUser();
    }

    /** Chuyển permission/role từ guard api sang web (một lần khi đổi chiến lược guard). */
    protected function migrateGuardApiToWeb(): void
    {
        Permission::where('guard_name', 'api')->update(['guard_name' => 'web']);
        Role::where('guard_name', 'api')->update(['guard_name' => 'web']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Tạo organization mặc định. */
    protected function seedOrganizations(): void
    {
        Organization::firstOrCreate(
            ['slug' => 'default'],
            [
                'name'        => 'Default',
                'description' => 'Organization mặc định của hệ thống',
                'status'      => 'active',
            ]
        );
    }

    /** Nhãn nhóm permission theo resource (để description). */
    protected static array $RESOURCE_LABELS = [
        'users'          => 'Người dùng',
        'permissions'    => 'Quyền',
        'roles'          => 'Vai trò',
        'organizations'  => 'Tổ chức',
        'posts'          => 'Bài viết',
        'post-categories' => 'Danh mục bài viết',
    ];

    /** Nhãn action (để description). */
    protected static array $ACTION_LABELS = [
        'stats'            => 'Thống kê',
        'index'            => 'Danh sách',
        'tree'             => 'Cây',
        'show'             => 'Chi tiết',
        'store'            => 'Tạo mới',
        'update'           => 'Cập nhật',
        'destroy'          => 'Xóa',
        'bulkDestroy'      => 'Xóa hàng loạt',
        'bulkUpdateStatus' => 'Cập nhật trạng thái hàng loạt',
        'changeStatus'     => 'Đổi trạng thái',
        'export'           => 'Xuất Excel',
        'import'           => 'Nhập Excel',
        'incrementView'    => 'Tăng lượt xem',
    ];

    /** Tạo đầy đủ permission từ danh sách PERMISSIONS (kèm description, sort_order, parent_id). */
    protected function seedPermissions(): void
    {
        $sortOrder = 0;
        $parentIds = [];

        foreach (self::$PERMISSIONS as $resource => $actions) {
            $groupName = "group:{$resource}";
            $groupLabel = self::$RESOURCE_LABELS[$resource] ?? ucfirst($resource);
            $group = Permission::firstOrCreate(
                ['name' => $groupName, 'guard_name' => self::GUARD],
                ['name' => $groupName, 'guard_name' => self::GUARD, 'description' => $groupLabel, 'sort_order' => $sortOrder++, 'parent_id' => null]
            );
            $parentIds[$resource] = $group->id;

            foreach ($actions as $idx => $action) {
                $name = "{$resource}.{$action}";
                $actionLabel = self::$ACTION_LABELS[$action] ?? $action;
                $desc = ($groupLabel ?? '') . ' - ' . $actionLabel;
                Permission::updateOrCreate(
                    ['name' => $name, 'guard_name' => self::GUARD],
                    ['description' => $desc, 'sort_order' => $idx, 'parent_id' => $group->id]
                );
            }
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Tạo các role mặc định. */
    protected function seedRoles(): void
    {
        $defaultOrganization = Organization::where('slug', 'default')->first();
        if (! $defaultOrganization) {
            return;
        }

        // Tất cả role gắn với team mặc định (model_has_roles.organization_id NOT NULL khi bật teams)
        // Super Admin: toàn quyền, thuộc team mặc định (roles chuẩn Spatie, không có status)
        Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => self::GUARD, 'organization_id' => $defaultOrganization->id]
        );
        Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => self::GUARD, 'organization_id' => $defaultOrganization->id]
        );
        Role::firstOrCreate(
            ['name' => 'Editor', 'guard_name' => self::GUARD, 'organization_id' => $defaultOrganization->id]
        );
        Role::firstOrCreate(
            ['name' => 'Vai trò mẫu', 'guard_name' => self::GUARD, 'organization_id' => $defaultOrganization->id]
        );
    }

    /** Gán permission cho từng role. */
    protected function assignPermissionsToRoles(): void
    {
        $defaultOrganization = Organization::where('slug', 'default')->first();
        if (! $defaultOrganization) {
            return;
        }

        $allPermissionNames = $this->getAllPermissionNames();
        $superAdmin = Role::where('name', 'Super Admin')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($allPermissionNames);
        }

        $admin = Role::where('name', 'Admin')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        if ($admin) {
            $admin->syncPermissions($allPermissionNames);
        }

        $editorPermissionNames = $this->getEditorPermissionNames();
        $editor = Role::where('name', 'Editor')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        if ($editor) {
            $editor->syncPermissions($editorPermissionNames);
        }

        $samplePermissionNames = $this->getSamplePermissionNames();
        $sampleRole = Role::where('name', 'Vai trò mẫu')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        if ($sampleRole) {
            $sampleRole->syncPermissions($samplePermissionNames);
        }
    }

    /** Gán role cho user: user 1 = Super Admin, user 2 = Admin, user 3 = Vai trò mẫu. */
    protected function assignSuperAdminToFirstUser(): void
    {
        $defaultOrganization = Organization::where('slug', 'default')->first();
        if (! $defaultOrganization) {
            return;
        }
        setPermissionsTeamId($defaultOrganization->id);

        $superAdmin = Role::where('name', 'Super Admin')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        $admin = Role::where('name', 'Admin')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();
        $sampleRole = Role::where('name', 'Vai trò mẫu')->where('organization_id', $defaultOrganization->id)->where('guard_name', self::GUARD)->first();

        $user1 = User::find(1);
        if ($user1 && $superAdmin && ! $user1->hasRole($superAdmin)) {
            $user1->assignRole($superAdmin);
        }

        $user2 = User::find(2);
        if ($user2 && $admin && ! $user2->hasRole($admin)) {
            $user2->assignRole($admin);
        }

        $user3 = User::find(3);
        if ($user3 && $sampleRole && ! $user3->hasRole($sampleRole)) {
            $user3->assignRole($sampleRole);
        }
    }

    /** Lấy toàn bộ tên permission (resource.action). */
    protected function getAllPermissionNames(): array
    {
        $names = [];
        foreach (self::$PERMISSIONS as $resource => $actions) {
            foreach ($actions as $action) {
                $names[] = "{$resource}.{$action}";
            }
        }
        return $names;
    }

    /** Permission cho role Editor: chỉ posts và post-categories. */
    protected function getEditorPermissionNames(): array
    {
        $names = [];
        foreach (['posts' => self::$PERMISSIONS['posts'], 'post-categories' => self::$PERMISSIONS['post-categories']] as $resource => $actions) {
            foreach ($actions as $action) {
                $names[] = "{$resource}.{$action}";
            }
        }
        return $names;
    }

    /** Permission cho Vai trò mẫu: chỉ xem bài viết và danh mục (index, show, tree, stats, incrementView). */
    protected function getSamplePermissionNames(): array
    {
        return [
            'posts.stats',
            'posts.index',
            'posts.show',
            'posts.incrementView',
            'post-categories.stats',
            'post-categories.index',
            'post-categories.tree',
            'post-categories.show',
        ];
    }
}
