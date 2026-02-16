<?php

namespace App\Modules\Post\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Danh mục tin tức phân cấp (cấu trúc cây) dùng Nested Set.
 */
class PostCategory extends Model
{
    use HasFactory, NodeTrait;

    protected static function newFactory()
    {
        return \Database\Factories\PostCategoryFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Bài viết thuộc danh mục này (quan hệ nhiều-nhiều qua bảng pivot). */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_post_category', 'post_category_id', 'post_id')
            ->withTimestamps();
    }

    /**
     * Scope lọc danh sách (đồng bộ với User/Post, dùng FilterRequest).
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['sort_by'] ?? 'sort_order', function ($query, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'sort_order', 'created_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'sort_order';
            $query->orderBy($column, $filters['sort_order'] ?? 'asc');
        });
    }

    /**
     * Tự sinh slug từ name khi tạo/cập nhật.
     */
    protected static function booted()
    {
        static::creating(function (PostCategory $category) {
            $category->created_by = $category->updated_by = auth()->id();
            if (empty($category->slug)) {
                $category->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($category->name));
            }
        });
        static::updating(function (PostCategory $category) {
            $category->updated_by = auth()->id();
            if ($category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($category->name), $category->id);
            }
        });
    }

    /** Sinh slug duy nhất (tránh trùng khi tự sinh từ name). */
    protected static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $query = static::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        $c = 0;
        while ($query->exists()) {
            $slug = $base . '-' . (++$c);
            $query = static::where('slug', $slug);
            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }

    /** Dùng cho Import: sinh slug duy nhất (public). */
    public static function uniqueSlugForImport(string $base): string
    {
        return static::uniqueSlug($base, null);
    }
}
