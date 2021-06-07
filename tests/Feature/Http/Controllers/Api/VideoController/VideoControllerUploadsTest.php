<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Tests\Traits\TestValidations;
use Tests\Traits\TestUploads;
use Tests\Traits\TestSaves;
use Illuminate\Http\UploadedFile;
use App\Models\Video;
use Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

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

    public function test_store_with_files()
    {
        Storage::fake();

        $files = $this->getFiles();

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->data + $files
        );

        $response->assertStatus(201);

        $this->assertFilesOnPersist($response, $files);
    }

    public function test_update_with_files()
    {
        Storage::fake();

        $files = $this->getFiles();

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->data + $files
        );

        $response->assertStatus(200);

        $this->assertFilesOnPersist($response, $files);

        $newFiles = [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->data + $newFiles
        );

        $response->assertStatus(200);

        $this->assertFilesOnPersist(
            $response,
            Arr::except($files, ['thumb_file', 'video_file']) + $newFiles
        );

        $id = $response->json('data.id');

        $video = Video::find($id);

        Storage::assertMissing($video->relativeFilePath($files['thumb_file']->hashName()));

        Storage::assertMissing($video->relativeFilePath($files['video_file']->hashName()));
    }

    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('id') ?? $response->json('data.id');
        $video = Video::find($id);
        $this->assertFilesExistsInStorage($video, $files);
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
