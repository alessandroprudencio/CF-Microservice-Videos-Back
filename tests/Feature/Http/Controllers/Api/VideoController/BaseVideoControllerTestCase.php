<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Tests\TestCase;
use App\Models\Video;
use App\Models\Genre;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = Video::factory()->create([
            'opened' => false,
            'thumb_file' => 'thumb.jpg',
            'banner_file' => 'banner.jpg',
            'video_file' => 'video.mp4',
            'trailer_file' => 'trailer.mp4',
        ]);

        $genre = Genre::factory()->create();

        $category = Category::factory()->create();

        $genre->categories()->sync($category->id);

        $this->data = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ];
    }

    protected function assertIfFilesUrlExists(Video $video, TestResponse $response)
    {
        $fileFields = Video::$fileFields;

        $data = $response->json('data');

        $data = array_key_exists(0, $data) ? $data[0] : $data;

        foreach ($fileFields as $field) {
            $file = $video->{$field};

            $this->assertEquals(
                Storage::url($video->relativeFilePath($file)),
                $data[$field . '_url']
            );
        }
    }
}
