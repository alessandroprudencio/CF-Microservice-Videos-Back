<?php

namespace Tests\Unit\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesUnitTest extends TestCase
{
    private $obj;

    protected function setup(): void
    {
        parent::setup();
        $this->obj = new UploadFilesStub();
    }

    public function test_relative_file_path()
    {
        $this->assertEquals("1/video.mp4", $this->obj->relativeFilePath('video.mp4'));
    }

    public function test_upload_file()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        Storage::disk()->assertExists("1/{$file->hashName()}");
    }

    public function test_upload_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video1.mp4');

        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->obj->uploadFiles([$file1, $file2]);

        Storage::disk()->assertExists("1/{$file1->hashName()}");
        Storage::disk()->assertExists("1/{$file2->hashName()}");
    }

    public function test_delete_old_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video1.mp4')->size(1);

        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1);

        $this->obj->uploadFiles([$file1, $file2]);

        $this->obj->deleteOldFiles();

        $this->assertCount(2, Storage::allFiles());

        $this->obj->oldFiles = [$file1->hashName()];

        $this->obj->deleteOldFiles();

        Storage::assertMissing("1/{$file1->hashName()}");

        Storage::assertExists("1/{$file2->hashName()}");
    }

    public function test_delete_file()
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        $this->obj->deleteFile($file->hashName());

        Storage::disk()->assertMissing("1/{$file->hashName()}");

        Storage::fake();

        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        $this->obj->deleteFile($file);

        Storage::disk()->assertMissing("1/{$file->hashName()}");
    }

    public function test_delete_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video2.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->obj->uploadFiles([$file1, $file2]);

        $this->obj->deleteFiles([$file1->hashName(), $file2]);

        Storage::disk()->assertMissing("1/{$file1->hashName()}");
        Storage::disk()->assertMissing("1/{$file2->hashName()}");
    }

    public function test_extract_files()
    {
        $attributes = [];

        $files =  UploadFilesStub::extractFiles($attributes);

        $this->assertCount(0, $attributes);

        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test'];

        $files =  UploadFilesStub::extractFiles($attributes);

        $this->assertCount(1, $attributes);

        $this->assertEquals(['file1' => 'test'], $attributes);

        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test', 'file2' => 'test'];

        $files =  UploadFilesStub::extractFiles($attributes);

        $this->assertCount(2, $attributes);

        $this->assertEquals(['file1' => 'test', 'file2' => 'test'], $attributes);

        $this->assertCount(0, $files);

        $file1 = UploadedFile::fake()->create('video1.mp4');

        $attributes = ['file1' => $file1, 'other' => 'test'];

        $files =  UploadFilesStub::extractFiles($attributes);

        $this->assertCount(2, $attributes);

        $this->assertEquals(['file1' => $file1->hashName(), 'other' => 'test'], $attributes);

        $this->assertEquals([$file1], $files);
    }
}
