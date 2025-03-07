<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bookmark extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'url',
        'title',
        'description'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::orderedUuid();
        });
    }
}
