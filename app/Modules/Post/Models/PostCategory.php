<?php

namespace App\Modules\Post\Models;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Danh mục tin tức phân cấp (cấu trúc cây) dùng parent_id.
 */
class PostCategory extends Model
{
    use HasFactory;

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
        'parent_id',
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

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id')->orderBy('sort_order');
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
        })->when(isset($filters['from_date']) && $filters['from_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })->when(isset($filters['to_date']) && $filters['to_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })->when($filters['sort_by'] ?? 'sort_order', function ($query, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'sort_order', 'parent_id', 'created_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'sort_order';
            $query->orderBy($column, $filters['sort_order'] ?? 'asc');
        });
    }

    /** Sắp xếp theo cây: cha trước con (theo sort_order, parent_id). */
    public function scopeTreeOrder($query)
    {
        return $query->orderByRaw('COALESCE(parent_id, 0), sort_order, id');
    }

    /**
     * Xây cây từ collection (parent_id). Trả về collection các node gốc có children lồng nhau.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @return \Illuminate\Support\Collection
     */
    public static function buildTree($items)
    {
        $grouped = $items->groupBy('parent_id');
        $builder = function ($parentId) use ($grouped, &$builder) {
            return ($grouped->get($parentId) ?? collect())->map(function ($node) use (&$builder) {
                $node->setRelation('children', $builder($node->id));
                return $node;
            })->values();
        };
        return $builder(null);
    }

    /** Danh sách phẳng theo thứ tự cây (cha trước con) để export. */
    public static function getFlatTreeOrdered(array $filters = [])
    {
        $query = static::with(['creator', 'editor'])->filter($filters);
        $all = $query->get();
        $tree = static::buildTree($all);
        $result = collect();
        $flatten = function ($nodes) use (&$flatten, &$result) {
            foreach ($nodes as $node) {
                $result->push($node);
                $flatten($node->children);
            }
        };
        $flatten($tree);
        return $result;
    }

    /** Độ sâu (số cấp từ gốc). 0 = gốc. */
    public function getDepthAttribute(): int
    {
        if (array_key_exists('depth', $this->attributes)) {
            return (int) $this->attributes['depth'];
        }
        $d = 0;
        $p = $this->parent_id;
        $ids = [$this->id];
        while ($p) {
            if (in_array($p, $ids)) {
                break;
            }
            $ids[] = $p;
            $parent = static::find($p);
            $p = $parent ? $parent->parent_id : null;
            $d++;
        }
        return $d;
    }

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
        static::deleting(function (PostCategory $category) {
            foreach ($category->children as $child) {
                $child->delete();
            }
        });
    }

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

    public static function uniqueSlugForImport(string $base): string
    {
        return static::uniqueSlug($base, null);
    }
}
