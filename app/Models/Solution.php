<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Webpatser\Uuid\Uuid;

class Solution extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['ecu_name', 'module_name', 'brand_name', 'owner_name'];
    protected $hidden = ['id', 'name', 'updated_at', 'deleted_at', 'module', 'brand', 'ecu'];
    protected $translatable = ['name'];
    protected $primaryKey = 'uuid';


    /*
    |--------------------------------------------------------------------------
    | BOOTS
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string)Uuid::generate(4);
        });

    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName()
    {
        return 'uuid';
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function ownerable()
    {
        return $this->morphTo('ownerable', 'ownerable_type', 'ownerable_uuid', 'uuid');
    }

    public function module(){
        return $this->belongsTo(Module::class)->withTrashed();
    }
    public function brand(){
        return $this->belongsTo(Brand::class)->withTrashed();
    }
    public function ecu(){
        return $this->belongsTo(ECU::class)->withTrashed();
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getECUNameAttribute()
    {
        return @$this->ecu->name;
    }
    public function getOwnerNameAttribute()
    {
        return @$this->ownerable->name;
    }
    public function getFixedFileAttribute($value)
    {
        $path = 'https://mycarfixbucket.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }
    public function getModuleNameAttribute()
    {
        return @$this->module->name;
    }
    public function getBrandNameAttribute()
    {
        return @$this->brand->name;
    }
    public function getBrokenFileAttribute($value)
    {
        $path = 'https://mycarfixbucket.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }


}
