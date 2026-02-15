<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'status', 'created_by', 'updated_by'];

    protected static function booted()
    {
        static::creating(fn($post) => $post->created_by = $post->updated_by = auth()->id());
        static::updating(fn($post) => $post->updated_by = auth()->id());
    }

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class , 'updated_by');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['sort_by'] ?? 'created_at', function ($query, $sortBy) use ($filters) {
            $query->orderBy($sortBy, $filters['sort_order'] ?? 'desc');
        });
    }
}
