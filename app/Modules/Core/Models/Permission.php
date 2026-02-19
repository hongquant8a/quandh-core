<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Model Permission (kế thừa Spatie), bổ sung description, sort_order, parent_id, scope filter.
 */
class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'sort_order',
        'parent_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope lọc: search (name, guard_name, description); from_date, to_date; sort_by.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%')
                    ->orWhere('guard_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when(isset($filters['from_date']) && $filters['from_date'], function ($q, $v) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })->when(isset($filters['to_date']) && $filters['to_date'], function ($q, $v) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })->when($filters['sort_by'] ?? 'sort_order', function ($q, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'guard_name', 'description', 'sort_order', 'parent_id', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'sort_order';
            $q->orderBy($column, $filters['sort_order'] ?? 'asc');
        });
        return $query;
    }

    /** Sắp xếp theo cây: parent trước, rồi sort_order. */
    public function scopeTreeOrder($query)
    {
        return $query->orderByRaw('COALESCE(parent_id, 0), sort_order, id');
    }

    /** Xây cây từ collection. */
    public static function buildTree($items)
    {
        $grouped = $items->groupBy('parent_id');
        $build = function ($parentId) use ($grouped, &$build) {
            return ($grouped->get($parentId) ?? collect())
                ->sortBy('sort_order')
                ->map(fn ($item) => $item->setRelation('children', $build($item->id)));
        };
        return $build(null)->values();
    }
}
