<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class EcuSignature extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'ecu_signatures';
    protected $guarded = [];
    protected $appends = ['ecu_name', 'brand_name'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'ecu', 'ecu_file'];
    protected $primaryKey = 'uuid';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string)Uuid::generate(4);
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function ecu()
    {
        return $this->belongsTo(ECU::class, 'ecu_uuid', 'uuid')->withTrashed();
    }

    public function ecu_file()
    {
        return $this->belongsTo(ECUFile::class, 'ecu_file_uuid', 'uuid')->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getEcuNameAttribute()
    {
        return optional($this->ecu)->name;
    }

    public function getBrandNameAttribute()
    {
        return optional(optional($this->ecu)->brand)->name;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Human-readable file size (e.g., "2 MB")
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
