<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use App\Models\Category;

class Genre extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Uuid;

    public $incrementing = false;

    protected $fillable = ['name', 'is_active'];

    protected $dates =  ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];

    protected $attributes = [
        'is_active' => true,
        'deleted_at' => null
    ];

    protected $keyType = 'string';

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }
}
