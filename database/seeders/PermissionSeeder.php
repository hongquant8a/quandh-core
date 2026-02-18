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
    /** Guard cho RESTful API (Spatie permission/role). Trùng với config auth.defaults.guard khi xây dựng API. */
    protected const GUARD = 'api';

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
        // Core - Roles
        'roles' => [
            'stats', 'index', 'show', 'store', 'update', 'destroy',
            'bulkDestroy', 'bulkUpdateStatus', 'changeStatus', 'export', 'import',
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
        $this->seedTeams();
        $this->seedPermissions();
        $this->seedRoles();
        $this->assignPermissionsToRoles();
        $this->assignSuperAdminToFirstUser();
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
        // Super Admin: toàn quyền, thuộc team mặc định
        Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id],
            ['status' => 'active']
        );
        // Admin: toàn quyền
        Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id],
            ['status' => 'active']
        );
        // Editor: chỉ posts và post-categories
        Role::firstOrCreate(
            ['name' => 'Editor', 'guard_name' => self::GUARD, 'team_id' => $defaultTeam->id],
            ['status' => 'active']
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
    }

    /** Gán role Super Admin cho user đầu tiên (id=1). */
    protected function assignSuperAdminToFirstUser(): void
    {
        $defaultTeam = Team::where('slug', 'default')->first();
        if (! $defaultTeam) {
            return;
        }
        // Spatie với teams: team_id trong pivot model_has_roles lấy từ setPermissionsTeamId(), không từ role
        setPermissionsTeamId($defaultTeam->id);
        $user = User::find(1);
        $superAdmin = Role::where('name', 'Super Admin')->where('team_id', $defaultTeam->id)->where('guard_name', self::GUARD)->first();
        if ($user && $superAdmin && ! $user->hasRole($superAdmin)) {
            $user->assignRole($superAdmin);
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
}
