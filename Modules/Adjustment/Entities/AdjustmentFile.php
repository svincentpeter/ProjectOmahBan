<?php

namespace Modules\Adjustment\Entities;

use Illuminate\Database\Eloquent\Model;

class AdjustmentFile extends Model
{
    protected $table = 'adjustment_files';

    protected $fillable = ['adjustment_id', 'file_path', 'file_name', 'file_size', 'mime_type', 'created_at', 'updated_at'];

    protected $casts = [
        'adjustment_id' => 'integer',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id');
    }

    /** Accessor URL file (existing) */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    /** NEW: ukuran file human readable (e.g. "1.2 MB") */
    public function getFileSizeHumanAttribute(): ?string
    {
        if (is_null($this->file_size)) {
            return null;
        }

        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return number_format($size, $i ? 1 : 0) . ' ' . $units[$i];
    }

    protected $appends = ['file_url', 'file_size_human'];

    /** OPTIONAL: validasi ringan di level model (tidak mengganggu controller) */
    protected static function booted()
    {
        static::saving(function (self $model) {
            // minimal guard supaya tidak tersimpan file kosong
            if (empty($model->file_path) || empty($model->file_name)) {
                throw new \InvalidArgumentException('Path dan nama file wajib diisi untuk AdjustmentFile.');
            }
        });
    }
}
