<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Team – nhóm/quyền theo ngữ cảnh (Spatie Permission teams).
 */
class Team extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function (Team $team) {
            $team->created_by = $team->updated_by = auth()->id();
            if (empty($team->slug)) {
                $team->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($team->name));
            }
        });
        static::updating(function (Team $team) {
            $team->updated_by = auth()->id();
            if ($team->isDirty('name') && ! $team->isDirty('slug')) {
                $team->slug = static::uniqueSlug(\Illuminate\Support\Str::slug($team->name), $team->id);
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
            $allowed = ['id', 'name', 'slug', 'status', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'id';
            $q->orderBy($column, $filters['sort_order'] ?? 'desc');
        });
        return $query;
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
