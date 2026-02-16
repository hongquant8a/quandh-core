<?php

namespace App\Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Đính kèm hình ảnh (hoặc file) cho bài viết.
 */
class PostAttachment extends Model
{
    protected $fillable = [
        'post_id',
        'path',
        'disk',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * URL công khai để hiển thị (dùng storage link).
     */
    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::disk($this->disk)->url($this->path) : null;
    }

    /**
     * Xóa file vật lý khi xóa bản ghi.
     */
    protected static function booted()
    {
        static::deleting(function (PostAttachment $attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }
}
