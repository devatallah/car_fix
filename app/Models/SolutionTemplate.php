<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SolutionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $table = 'solution_templates';
    protected $guarded = [];
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
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function script()
    {
        return $this->belongsTo(Script::class, 'script_uuid', 'uuid');
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
}