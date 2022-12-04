<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Webpatser\Uuid\Uuid;

class Fix extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['ecu_name', 'solution_name', 'brand_name'];
    protected $hidden = ['id', 'name', 'created_at', 'updated_at', 'deleted_at', 'solution', 'brand', 'ecu'];
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
        return $this->morphTo();
    }

    public function solution(){
        return $this->belongsTo(Solution::class)->withTrashed();
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
    public function getFixedFileAttribute($value)
    {
        $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }
    public function getSolutionNameAttribute()
    {
        return @$this->solution->name;
    }
    public function getBrandNameAttribute()
    {
        return @$this->brand->name;
    }
    public function getBrokenFileAttribute($value)
    {
        $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }


}
