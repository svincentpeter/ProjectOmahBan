<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'notification_settings';

    protected $fillable = [
        'type',
        'label',
        'description',
        'icon',
        'is_enabled',
        'template',
        'placeholders',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'placeholders' => 'array',
    ];

    /**
     * Get setting by type
     */
    public static function getByType(string $type): ?self
    {
        return static::where('type', $type)->first();
    }

    /**
     * Check if notification type is enabled
     */
    public static function isEnabled(string $type): bool
    {
        $setting = static::getByType($type);
        return $setting ? $setting->is_enabled : false;
    }

    /**
     * Get template for a notification type
     */
    public static function getTemplate(string $type): ?string
    {
        $setting = static::getByType($type);
        return $setting ? $setting->template : null;
    }

    /**
     * Parse template with given data
     */
    public function parseTemplate(array $data): string
    {
        $message = $this->template;
        
        foreach ($data as $key => $value) {
            $message = str_replace("{{$key}}", (string) $value, $message);
        }
        
        return $message;
    }

    /**
     * Get available notification types
     */
    public static function getTypes(): array
    {
        return [
            'manual_input' => 'Manual Input Alert',
            'low_stock' => 'Low Stock Alert',
            'daily_report' => 'Laporan Harian',
            'login_alert' => 'Login Alert',
        ];
    }
}
