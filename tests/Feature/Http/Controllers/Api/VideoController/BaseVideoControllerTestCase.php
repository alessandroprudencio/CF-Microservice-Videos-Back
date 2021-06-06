<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = Video::factory()->create([
            'opened' => false
        ]);

        $this->data = [
            'title' => "Homen da lua",
            'description' => "HISTORIA DE UM HOMEM NA LUA",
            'rating' => Video::RATING_LIST[0],
            'duration' => 30,
            'year_launched' => 2020,
        ];
    }
}
