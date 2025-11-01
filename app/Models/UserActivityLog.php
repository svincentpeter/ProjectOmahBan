<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'data',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Hanya pakai created_at

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static method untuk log aktivitas (shortcut)
     */
    public static function log(string $action, string $description, ?array $data = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }

    /**
     * Scope untuk filter by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get action dalam format readable
     */
    public function getActionReadable(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }
}
