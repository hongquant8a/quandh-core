<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Team;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed permission, role, team và phân quyền cho dự án.
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
     * Định dạng: 'resource.action' — resource trùng prefix API (users, permissions, roles, teams, posts, post-categories).
     * Khi thêm module/chức năng: bổ sung vào đúng nhóm và chạy sail artisan db:seed --class=PermissionSeeder.
     */
    protected static array $PERMISSIONS = [
        // Core - Users
        'users' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
        ],
        // Core - Permissions
        'permissions' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'export', 'import',
        ],
        // Core - Roles (bảng roles chuẩn Spatie, không có cột status)
        'roles' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'export', 'import',
        ],
        // Core - Teams (cấu trúc cây parent_id)
        'teams' => [
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
        $this->seedTeams();
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

    /** Tạo team mặc định. */
    protected function seedTeams(): void
    {
        Team::firstOrCreate(
            ['slug' => 'default'],
            [
                'name'        => 'Default',
                'description' => 'Team mặc định của hệ thống',
                'status'      => 'active',
            ]
        );
    }

    /** Tạo đầy đủ permission từ danh sách PERMISSIONS. */
    protected function seedPermissions(): void
    {
        foreach (self::$PERMISSIONS as $resource => $actions) {
            foreach ($actions as $action) {
                $name = "{$resource}.{$action}";
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => self::GUARD],
                    ['name' => $name, 'guard_name' => self::GUARD]
                );
            }
        }
    }

    /** Tạo các role mặc định. */
    protected function seedRoles(): void
    {
        $defaultTeam = Team::where('slug', 'default')->first();
        if (! $defaultTeam) {
            return;
        }

        // Tất cả role gắn với team mặc định (model_has_roles.team_id NOT NULL khi bật teams)
        // Super Admin: toàn quyền, thuộc team mặc định (roles chuẩn Spatie, không có status)
        Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id]
        );
        Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id]
        );
        Role::firstOrCreate(
            ['name' => 'Editor', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id]
        );
        Role::firstOrCreate(
            ['name' => 'Vai trò mẫu', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id]
        );
    }

    /** Gán permission cho từng role. */
    protected function assignPermissionsToRoles(): void
    {
        $defaultTeam = Team::where('slug', 'default')->first();
        if (! $defaultTeam) {
            return;
        }

        $allPermissionNames = $this->getAllPermissionNames();
        $superAdmin = Role::where('name', 'Super Admin')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($allPermissionNames);
        }

        $admin = Role::where('name', 'Admin')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        if ($admin) {
            $admin->syncPermissions($allPermissionNames);
        }

        $editorPermissionNames = $this->getEditorPermissionNames();
        $editor = Role::where('name', 'Editor')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        if ($editor) {
            $editor->syncPermissions($editorPermissionNames);
        }

        $samplePermissionNames = $this->getSamplePermissionNames();
        $sampleRole = Role::where('name', 'Vai trò mẫu')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        if ($sampleRole) {
            $sampleRole->syncPermissions($samplePermissionNames);
        }
    }

    /** Gán role cho user: user 1 = Super Admin, user 2 = Admin, user 3 = Vai trò mẫu. */
    protected function assignSuperAdminToFirstUser(): void
    {
        $defaultTeam = Team::where('slug', 'default')->first();
        if (! $defaultTeam) {
            return;
        }
        setPermissionsTeamId($defaultTeam->id);

        $superAdmin = Role::where('name', 'Super Admin')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        $admin = Role::where('name', 'Admin')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        $sampleRole = Role::where('name', 'Vai trò mẫu')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();

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
