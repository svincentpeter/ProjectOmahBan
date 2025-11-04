<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ServiceMasterAudit extends Model
{
    protected $table = 'service_master_audits';

    protected $fillable = ['service_master_id', 'old_price', 'new_price', 'reason', 'changed_by'];

    protected $casts = [
        'old_price' => 'integer',
        'new_price' => 'integer',
    ];

    public function serviceMaster()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_master_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }
}
