<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm khóa ngoại roles.team_id -> teams.id (tùy chọn, đảm bảo toàn vẹn dữ liệu).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
    }
};
