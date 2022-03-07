<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataSource extends Model
{
    use HasFactory;

    protected $casts = [
        'mappings' => 'array',
        'last_fetched_at' => 'datetime'
    ];

    public function getIndexNameAttribute()
    {
        return Str::of($this->csv_path)->afterLast('/')->beforeLast('.')->__toString();
    }
}
