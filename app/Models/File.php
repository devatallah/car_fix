<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class File extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['category_name', 'manufacturer_name', 'car_model_name'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'category', 'manufacturer', 'car_model'];
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

    public function category(){
        return $this->belongsTo(Category::class)->withTrashed();
    }
    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class)->withTrashed();
    }
    public function car_model(){
        return $this->belongsTo(CarModel::class)->withTrashed();
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

    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }
    public function getManufacturerNameAttribute()
    {
        return @$this->manufacturer->name;
    }
    public function getCarModelNameAttribute()
    {
        return @$this->car_model->name;
    }

    public function getFileAttribute($value)
    {
        return !is_null($value) ? asset(Storage::url($value)) : '';
    }


}
