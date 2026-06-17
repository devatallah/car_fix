<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SmartPatchGroup extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table      = 'smart_patch_groups';
    protected $guarded    = [];
    protected $primaryKey = 'uuid';
    protected $hidden     = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $appends    = ['ecu_name', 'brand_name', 'module_name', 'calibrations_count'];

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

    public function calibrations()
    {
        return $this->hasMany(SmartPatch::class, 'group_uuid', 'uuid');
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

    public function getCalibrationsCountAttribute()
    {
        return $this->calibrations()->count();
    }
}
