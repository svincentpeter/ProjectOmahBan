<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    use HasFactory;

    protected $table = 'notification_recipients';

    protected $fillable = [
        'recipient_name',
        'recipient_phone',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Check if recipient should receive a specific notification type
     */
    public function shouldReceive(string $type): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        
        // If empty permissions, assume NO permissions (opt-in)
        if (empty($permissions)) {
            return false;
        }

        return in_array($type, $permissions);
    }
}
