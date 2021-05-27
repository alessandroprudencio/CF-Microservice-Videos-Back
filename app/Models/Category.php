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

    protected $fillable = [ 'name', 'description', 'is_active' ];
    protected $dates =  ['deleted_at'];

    protected $keyType = 'string';
}