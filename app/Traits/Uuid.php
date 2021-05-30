<?php

namespace App\Traits;
use App\Models\User;
use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid {
    protected static function booted()
    {
        static::creating(fn ($model) => $model->id = (string) RamseyUuid::uuid4());
    }
}
