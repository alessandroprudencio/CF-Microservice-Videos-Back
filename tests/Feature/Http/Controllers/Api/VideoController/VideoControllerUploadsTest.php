<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Tests\Traits\TestValidations;
use Tests\Traits\TestUploads;
use Tests\Traits\TestSaves;
use Illuminate\Http\UploadedFile;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoControllerUploadsTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestUploads, TestSaves;

    public function test_invalidation_thumb_field()
    {
        $this->assertInvalidationFile(
            'thumb_file',
            'mp4',
            Video::THUMB_FILE_MAX_SIZE,
            [
                'errors.thumb_file' => 'array',
            ]
        );
    }

    public function test_invalidation_banner_field()
    {
        $this->assertInvalidationFile(
            'banner_file',
            'mp4',
            Video::BANNER_FILE_MAX_SIZE,
            [
                'errors.banner_file' => 'array',
            ]
        );
    }

    public function test_invalidation_trailer_field()
    {
        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::TRAILER_FILE_MAX_SIZE,
            [
                'errors.trailer_file' => 'array',
            ]
        );
    }

    public function test_invalidation_video_field()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            [
                'errors.video_file' => 'array',
            ]
        );
    }

    public function test_save_without_files()
    {
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();
        $genre->categories()->sync($category->id);

        $newData = [
            [
                'send_data' => $this->data + [
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id],
                ],
                'test_data' => $this->data + ['opened' => false]
            ],
            [
                'send_data' => $this->data + [
                    'opened' => true,
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id],
                ],
                'test_data' => $this->data + ['opened' => true]
            ],
            [
                'send_data' => $this->data + [
                    'rating' => Video::RATING_LIST[1],
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id],
                ],
                'test_data' => $this->data + ['rating' => Video::RATING_LIST[1]]
            ],
        ];

        foreach ($newData as $key => $value) {

            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
        }
    }

    public function test_store_with_files()
    {
        Storage::fake();

        $files = $this->getFiles();

        $category = Category::factory()->create();

        $genre = Genre::factory()->create();

        $genre->categories()->sync($category->id);

        $response = $this->postJson($this->routeStore(), $this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]] + $files);

        $response->assertStatus(201);

        $id = $response->json('id');

        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    public function test_update_with_files()
    {
        \Storage::fake();

        $files = $this->getFiles();

        $category = Category::factory()->create();

        $genre = Genre::factory()->create();

        $genre->categories()->sync($category->id);

        $response = $this->putJson($this->routeUpdate(), $this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]] + $files);

        $response->assertStatus(200);

        $id = $response->json('id');

        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }

        $newFiles = [
            'video_file' => UploadedFile::fake()->create('video_file.mp4'),
            'thumb_file' => UploadedFile::fake()->create('thumb_file.jpg'),
        ];

        $response = $this->putJson($this->routeUpdate(), $this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]] + $newFiles);

        $response->assertStatus(200);

        $id = $response->json('id');

        $video = Video::find($id);

        Storage::assertMissing($video->relativeFilePath("$id/{$files['thumb_file']->hashName()}"));

        Storage::assertMissing($video->relativeFilePath("$id/{$files['video_file']->hashName()}"));
    }

    protected function getFiles()
    {
        return [
            'thumb_file' => UploadedFile::fake()->create('thumb_file.jpg'),
            'banner_file' => UploadedFile::fake()->create('banner_file.jpg'),
            'trailer_file' => UploadedFile::fake()->create('trailer_file.mp4'),
            'video_file' => UploadedFile::fake()->create('video_file.mp4')
        ];
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}
