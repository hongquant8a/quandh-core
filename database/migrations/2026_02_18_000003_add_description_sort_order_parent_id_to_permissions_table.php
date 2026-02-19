<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm description, sort_order, parent_id vào bảng permissions để nhóm và sắp xếp khi hiển thị frontend.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('guard_name');
            $table->unsignedInteger('sort_order')->default(0)->after('description');
            $table->unsignedBigInteger('parent_id')->nullable()->after('sort_order');
            $table->foreign('parent_id')->references('id')->on('permissions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['description', 'sort_order', 'parent_id']);
        });
    }
};
