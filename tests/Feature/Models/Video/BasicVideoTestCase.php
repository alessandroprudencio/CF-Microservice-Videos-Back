<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use PHPUnit\Framework\TestCase;

abstract class BasicVideoTestCase extends TestCase
{
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => "Homen da lua",
            'description' => "HISTORIA DE UM HOMEM NA LUA",
            'rating' => Video::RATING_LIST[0],
            'duration' => 30,
            'year_launched' => 2020,
        ];
    }
}
