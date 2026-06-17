<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SmartPatch extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'smart_patches';
    protected $guarded = [];
    protected $primaryKey = 'uuid';
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['ecu_name', 'brand_name', 'module_name'];
    protected $casts = [
        'file_size'      => 'integer',
        'patches_count'  => 'integer',
        'wildcard_count' => 'integer',
        'context_size'   => 'integer',
        'gap_tolerance'  => 'integer',
        'ecu_software_number' => 'string',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function ecu()
    {
        return $this->belongsTo(ECU::class, 'ecu_uuid', 'uuid')->withTrashed();
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_uuid', 'uuid')->withTrashed();
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getEcuNameAttribute()
    {
        return optional($this->ecu)->name;
    }

    public function getBrandNameAttribute()
    {
        return optional(optional($this->ecu)->brand)->name;
    }

    public function getModuleNameAttribute()
    {
        return optional($this->module)->name;
    }

    public function getPatchMapDecodedAttribute()
    {
        return json_decode($this->patch_map, true);
    }
}
