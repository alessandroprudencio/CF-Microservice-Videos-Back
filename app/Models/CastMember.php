<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class CastMember extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Uuid;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    public $incrementing = false;

    protected $fillable = ['name', 'type'];

    protected $dates =  ['deleted_at'];

    // protected $attributes = [
    //     // 'is_active' => true,
    //     // 'type' => 0,
    // ];

    protected $casts = [
        'id' => 'string',
        'type' => 'integer'
    ];

    protected $keyType = 'string';
}
