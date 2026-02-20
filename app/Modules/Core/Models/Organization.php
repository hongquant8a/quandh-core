<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Organization – tổ chức (thay thế teams, dùng cho Spatie Permission teams).
 */
class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'parent_id',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id')->orderBy('sort_order');
    }

    protected static function booted()
    {
        static::creating(function (Organization $organization) {
            $organization->created_by = $organization->updated_by = auth()->id();
            if (empty($organization->slug)) {
                $organization->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($organization->name));
            }
        });
        static::updating(function (Organization $organization) {
            $organization->updated_by = auth()->id();
            if ($organization->isDirty('name') && ! $organization->isDirty('slug')) {
                $organization->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($organization->name), $organization->id);
            }
        });
        static::deleting(function (Organization $organization) {
            foreach ($organization->children as $child) {
                $child->delete();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        })->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        })->when(isset($filters['from_date']) && $filters['from_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })->when(isset($filters['to_date']) && $filters['to_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })->when($filters['sort_by'] ?? 'id', function ($q, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'slug', 'status', 'parent_id', 'sort_order', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'id';
            $q->orderBy($column, $filters['sort_order'] ?? 'desc');
        });
        return $query;
    }

    public function scopeTreeOrder($query)
    {
        return $query->orderByRaw('COALESCE(parent_id, 0), sort_order, id');
    }

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

    public function getDepthAttribute(): int
    {
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
}
