<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class ECUFile extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $table = 'ecu_files';
    protected $guarded = [];
    protected $appends = ['ecu_name'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'ecu'];
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
    public function getOriginFileAttribute($value)
    {
        $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path.$value : '';
    }


}
