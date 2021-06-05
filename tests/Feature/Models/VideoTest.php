<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Database\QueryException;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_rollback_create()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => "Homen da lua",
                'description' => "HISTORIA DE UM HOMEM NA LUA",
                'rating' => Video::RATING_LIST[0],
                'duration' => 30,
                'year_launched' => 2020,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());

            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function test_rollback_update()
    {
        $hasError = false;
        try {
            $video = Video::factory()->create();

            $oldTitle = $video->title;

            $video->update([
                'title' => "Homen da lua",
                'description' => "HISTORIA DE UM HOMEM NA LUA",
                'rating' => Video::RATING_LIST[0],
                'duration' => 30,
                'year_launched' => 2020,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', ['title' => $oldTitle]);

            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}
