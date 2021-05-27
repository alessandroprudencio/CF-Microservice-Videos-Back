<?php

namespace App\Traits;
use App\Models\User;
use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid {
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->id = RamseyUuid::uuid4()->toString();
            }
        });
    }
}