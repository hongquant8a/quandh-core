<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm khóa ngoại roles.{team_foreign_key} -> teams.id (tùy chọn, đảm bảo toàn vẹn dữ liệu).
 * Dùng cột từ config vì create_permission_tables có thể tạo organization_id hoặc team_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        $columnName = config('permission.column_names.team_foreign_key', 'team_id');

        Schema::table('roles', function (Blueprint $table) use ($columnName) {
            $table->foreign($columnName)->references('id')->on('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $columnName = config('permission.column_names.team_foreign_key', 'team_id');

        Schema::table('roles', function (Blueprint $table) use ($columnName) {
            $table->dropForeign([$columnName]);
        });
    }
};
