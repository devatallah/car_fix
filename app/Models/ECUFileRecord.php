<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class ECUFileRecord extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $table = 'ecu_file_records';
    protected $guarded = [];
    protected $appends = ['module_name', 'ecu_file_id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'module', 'ecu_file'];
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

    public function ecu_file()
    {
        return $this->belongsTo(ECUFile::class, 'ecu_file_uuid', 'uuid');
    }

    public function module()
    {
        return $this->belongsTo(Module::class)->withTrashed();
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getFileAttribute($value)
    {
        $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
        return !is_null($value) ? $path . $value : '';
    }

    public function getModuleNameAttribute()
    {
        return $this->module->name;
    }
    public function getEcuFileIdAttribute()
    {
        return $this->ecu_file->id;
    }
}
