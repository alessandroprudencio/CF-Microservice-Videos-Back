<?php

namespace App\Traits;

use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    protected static function booted()
    {
        static::creating(fn ($model) => $model->id = (string) RamseyUuid::uuid4());
    }
}
