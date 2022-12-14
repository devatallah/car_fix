<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $guarded = [];
    protected $appends = [];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];
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
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'ecus', 'module_uuid', 'brand_uuid', 'uuid', 'uuid')->distinct();
    }

    public function ecus()
    {
        return $this->hasMany(ECU::class, 'module_uuid', 'uuid');
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

    public function getModuleNameAttribute($value)
    {
        return @$this->name . " " . ($this->is_free ? 'free' : ('price (' . $this->price . ')'));
    }


}
