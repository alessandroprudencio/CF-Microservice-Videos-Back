<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Uuid;

    public $incrementing = false;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $dates =  ['deleted_at'];

    protected $attributes = [
        'is_active' => true,
        'description' => null,
        'deleted_at' => null
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];

    protected $keyType = 'string';
}
