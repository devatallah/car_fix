<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class ECU extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $table = 'ecus';
    protected $guarded = [];
    protected $appends = ['solution_name', 'brand_name'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'solution', 'brand'];
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

    public function solution(){
        return $this->belongsTo(Solution::class)->withTrashed();
    }
    public function brand(){
        return $this->belongsTo(Brand::class)->withTrashed();
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

    public function getSolutionNameAttribute()
    {
        return @$this->solution->name;
    }
    public function getBrandNameAttribute()
    {
        return @$this->brand->name;
    }

    public function getFileAttribute($value)
    {
        $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }


}
