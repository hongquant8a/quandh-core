<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đổi bảng teams thành organizations, team_id thành organization_id trong toàn bộ hệ thống.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
        Schema::rename('teams', 'organizations');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'name', 'guard_name']);
            $table->dropIndex('roles_team_foreign_key_index');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('team_id', 'organization_id');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->index('organization_id', 'roles_organization_foreign_key_index');
            $table->unique(['organization_id', 'name', 'guard_name']);
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->renameColumn('team_id', 'organization_id');
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_team_foreign_key_index');
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index('organization_id', 'model_has_roles_organization_foreign_key_index');
            $table->primary(['organization_id', 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->renameColumn('team_id', 'organization_id');
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_team_foreign_key_index');
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index('organization_id', 'model_has_permissions_organization_foreign_key_index');
            $table->primary(['organization_id', 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_organization_foreign_key_index');
            $table->renameColumn('organization_id', 'team_id');
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index('team_id', 'model_has_permissions_team_foreign_key_index');
            $table->primary(['team_id', 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_organization_foreign_key_index');
            $table->renameColumn('organization_id', 'team_id');
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index('team_id', 'model_has_roles_team_foreign_key_index');
            $table->primary(['team_id', 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('roles_organization_foreign_key_index');
            $table->dropUnique(['organization_id', 'name', 'guard_name']);
            $table->renameColumn('organization_id', 'team_id');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->index('team_id', 'roles_team_foreign_key_index');
            $table->unique(['team_id', 'name', 'guard_name']);
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });

        Schema::rename('organizations', 'teams');

        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('teams')->nullOnDelete();
        });
    }
};
