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
    protected $appends = ['file_name', 'category_name', 'manufacturer_name', 'car_model_name'];
    protected $hidden = ['id', 'name', 'created_at', 'updated_at', 'deleted_at', 'category', 'manufacturer', 'car_model', 'file'];
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

    public function category(){
        return $this->belongsTo(Category::class)->withTrashed();
    }
    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class)->withTrashed();
    }
    public function car_model(){
        return $this->belongsTo(CarModel::class)->withTrashed();
    }
    public function file(){
        return $this->belongsTo(File::class)->withTrashed();
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

    public function getFileNameAttribute()
    {
        return @$this->file->name;
    }
    public function getFixedFileAttribute()
    {
        return @$this->file->file;
    }
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

    public function getBrokenFileAttribute($value)
    {
        return !is_null($value) ? asset(Storage::url($value)) : '';
    }


}
