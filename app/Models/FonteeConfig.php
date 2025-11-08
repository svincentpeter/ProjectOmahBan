<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonteeConfig extends Model
{
    protected $table = 'fontee_config'; // mengikuti dump SQL kamu
    protected $fillable = ['config_key', 'config_value', 'description', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Ambil nilai config by key (aktif saja). */
    public static function getValue(string $key, $default = null)
    {
        $row = static::query()
            ->where('config_key', $key)
            ->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            })
            ->first();

        return $row?->config_value ?? $default;
    }

    /** Alias biar fleksibel kalau service pakai nama lain. */
    public static function value(string $key, $default = null)      { return static::getValue($key, $default); }
    public static function get(string $key, $default = null)        { return static::getValue($key, $default); }
}
