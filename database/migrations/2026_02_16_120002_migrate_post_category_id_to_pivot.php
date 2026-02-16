<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Chuyển dữ liệu category_id hiện có sang bảng pivot post_post_category.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('posts')->whereNotNull('category_id')->get(['id', 'category_id']);
        $now = now();
        foreach ($rows as $row) {
            DB::table('post_post_category')->insertOrIgnore([
                'post_id' => $row->id,
                'post_category_id' => $row->category_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Không xóa dữ liệu pivot khi rollback; migration trước sẽ drop bảng.
    }
};
