<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Http\Controllers\Api\BasicCrudController;

class VideoController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
