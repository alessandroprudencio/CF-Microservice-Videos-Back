<?php

namespace Tests\Feature\Models\Video;

use App\Models\Category;
use App\Models\Video;
use App\Models\Genre;
use Illuminate\Database\QueryException;

class VideoCrudTest extends BaseVideoTestCase
{
    private $fileFieldsData = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (Video::$fileFields as $field) {
            $this->fileFieldsData[$field] = "$field.test";
        }
    }

    public function test_list()
    {
        Video::factory()->create();

        $videos = Video::all();

        $this->assertCount(1, $videos);

        $videosKeys = array_keys($videos->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'video_file',
                'thumb_file',
                'banner_file',
                'trailer_file',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $videosKeys
        );
    }

    public function test_create_with_basic_fields()
    {
        $video = Video::create($this->data + $this->fileFieldsData);

        $video->refresh();

        $this->assertEquals(36, strlen($video->id));

        $this->assertFalse($video->opened);

        $this->assertDatabaseHas('videos', $this->data + $this->fileFieldsData + ['opened' => false]);

        $video = Video::create($this->data + ['opened' => true]);

        $this->assertTrue($video->opened);

        $this->assertDatabaseHas('videos', ['opened' => true]);
    }

    public function test_create_with_relations()
    {
        $category = Category::factory()->create();

        $genre = Genre::factory()->create();

        $video = Video::create($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);

        $this->assertHasCategory($video->id, $category->id);

        $this->assertHasGenre($video->id, $genre->id);
    }

    public function test_update_with_basic_fields()
    {
        $video = Video::factory()->create(['opened' => false]);

        $video->update($this->data + $this->fileFieldsData);

        $this->assertFalse($video->opened);

        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = Video::factory()->create(['opened' => true]);

        $video->update($this->data + $this->fileFieldsData + ['opened' => true]);

        $this->assertTrue($video->opened);

        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

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

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

    public function test_delete()
    {
        $video = Video::factory()->create();

        $video->delete();

        $this->assertNull(Video::find($video->id));

        $video->restore();

        $this->assertNotNull(Video::find($video->id));
    }
}
