<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

class Model extends EloquentModel
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Model $model) {
            return $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
}
