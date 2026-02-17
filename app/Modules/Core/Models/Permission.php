<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Model Permission (kế thừa Spatie), bổ sung scope filter cho module Core.
 */
class Permission extends SpatiePermission
{
    /**
     * Scope lọc danh sách: tìm kiếm theo name, guard_name; sắp xếp; lọc theo khoảng ngày.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%')
                    ->orWhere('guard_name', 'like', '%' . $search . '%');
            });
        })->when(isset($filters['from_date']) && $filters['from_date'], function ($q, $v) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })->when(isset($filters['to_date']) && $filters['to_date'], function ($q, $v) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })->when($filters['sort_by'] ?? 'id', function ($q, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'guard_name', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'id';
            $q->orderBy($column, $filters['sort_order'] ?? 'desc');
        });
        return $query;
    }
}
