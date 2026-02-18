<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Model Role (kế thừa Spatie), bổ sung scope filter. Cột theo mặc định Spatie: id, name, guard_name, team_id, timestamps.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'team_id',
    ];

    /**
     * Scope lọc: search, from_date, to_date, sort (không có status).
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('guard_name', 'like', '%' . $search . '%');
        })->when(isset($filters['from_date']) && $filters['from_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })->when(isset($filters['to_date']) && $filters['to_date'], function ($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })->when($filters['sort_by'] ?? 'id', function ($q, $sortBy) use ($filters) {
            $allowed = ['id', 'name', 'guard_name', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'id';
            $q->orderBy($column, $filters['sort_order'] ?? 'desc');
        });
        return $query;
    }

    /** Quan hệ team (nếu dùng bảng teams). */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
