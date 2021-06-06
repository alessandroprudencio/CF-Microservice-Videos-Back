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

    public function test_invalidation_video_field()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            15000,
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
        UploadedFile::fake()->image('image.jpg');

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

    // public function test_update_with_files()
    // {
    //     \Storage::fake();

    //     $files = $this->getFiles();

    //     $category = Category::factory()->create();

    //     $genre = Genre::factory()->create();

    //     $genre->categories()->sync($category->id);

    //     $response = $this->putJson($this->routeUpdate(), $this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]] + $files);

    //     $response->assertStatus(200);

    //     $id = $response->json('id');

    //     foreach ($files as $file) {
    //         \Storage::assertExists("$id/{$file->hashName()}");
    //     }
    // }

    protected function getFiles()
    {
        return [
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
