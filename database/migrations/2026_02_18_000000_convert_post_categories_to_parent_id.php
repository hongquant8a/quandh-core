<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chuyển post_categories từ Nested Set (_lft, _rgt) sang cấu trúc cây đơn giản parent_id.
 * Chỉ xóa _lft, _rgt; đổi kiểu parent_id sang unsignedBigInteger (khớp id) rồi thêm FK.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropColumn(['_lft', '_rgt']);
        });
        // parent_id từ nestedSet() là unsignedInteger, id là bigint → MODIFY để thêm FK (không cần doctrine/dbal)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE post_categories MODIFY parent_id BIGINT UNSIGNED NULL');
        }
        Schema::table('post_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('post_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::table('post_categories', function (Blueprint $table) {
            $table->unsignedInteger('_lft')->default(0);
            $table->unsignedInteger('_rgt')->default(0);
        });
    }
};
