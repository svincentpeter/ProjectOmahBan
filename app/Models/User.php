<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
// ❌ HAPUS LINE INI (jika tidak pakai Sanctum)
// use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    // ❌ HAPUS LINE INI (jika tidak pakai Sanctum)
    // use HasApiTokens;
    
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        // ✅ Kolom baru untuk Tahap 1
        'supervisor_pin',
        'phone_number',
        'last_login_at',
        'login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'supervisor_pin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $with = ['media'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackUrl('https://www.gravatar.com/avatar/' . md5("test@mail.com"));
    }

    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    // ========================================
    // ✅ METHOD BARU UNTUK TAHAP 1
    // ========================================

    /**
     * Relasi ke Activity Logs
     */
    public function activityLogs()
    {
        return $this->hasMany(\App\Models\UserActivityLog::class);
    }

    /**
     * Helper: Check apakah user adalah Owner
     */
    public function isOwner(): bool
    {
        return $this->hasRole('Owner');
    }

    /**
     * Helper: Check apakah user adalah Supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->hasRole('Supervisor');
    }

    /**
     * Helper: Check apakah user adalah Kasir
     */
    public function isKasir(): bool
    {
        return $this->hasRole('Kasir');
    }

    /**
     * Verify Supervisor PIN
     */
    public function verifySupervisorPin(string $pin): bool
    {
        if (!$this->supervisor_pin) {
            return false;
        }
        return Hash::check($pin, $this->supervisor_pin);
    }

    /**
     * Set Supervisor PIN (auto-encrypt)
     */
    public function setSupervisorPin(string $pin): void
    {
        $this->supervisor_pin = Hash::make($pin);
        $this->save();
    }

    /**
     * Get nama role user (untuk display)
     */
    public function getRoleName(): string
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    /**
     * Get warna badge sesuai role
     */
    public function getRoleBadgeColor(): string
    {
        $roleName = $this->getRoleName();

        return match ($roleName) {
            'Owner' => 'danger',
            'Supervisor' => 'warning',
            'Kasir' => 'info',
            'Admin', 'Super Admin' => 'primary',
            default => 'secondary'
        };
    }
}
