<?php

namespace Tests\Production\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use Tests\Traits\TestStorages;
use Tests\Traits\TestProd;

class UploadFilesProdTest extends TestCase
{
    use TestStorages, TestProd;

    private $obj;

    protected function setup(): void
    {
        parent::setup();

        $this->skipTestIfNotProd('only production');

        $this->obj = new UploadFilesStub();

        \Config::set('filesystem.default', 'gcs');

        $this->deleteAllFiles();
    }

    public function test_upload_file()
    {
        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        Storage::disk()->assertExists("1/{$file->hashName()}");
    }

    public function test_upload_files()
    {
        $file1 = UploadedFile::fake()->create('video1.mp4');

        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->obj->uploadFiles([$file1, $file2]);

        Storage::disk()->assertExists("1/{$file1->hashName()}");

        Storage::disk()->assertExists("1/{$file2->hashName()}");
    }

    public function test_delete_old_files()
    {
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
        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        $this->obj->deleteFile($file->hashName());

        Storage::disk()->assertMissing("1/{$file->hashName()}");

        $file = UploadedFile::fake()->create('video.mp4');

        $this->obj->uploadFile($file);

        $this->obj->deleteFile($file);

        Storage::disk()->assertMissing("1/{$file->hashName()}");
    }

    public function test_delete_files()
    {
        $file1 = UploadedFile::fake()->create('video2.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->obj->uploadFiles([$file1, $file2]);

        $this->obj->deleteFiles([$file1->hashName(), $file2]);

        Storage::disk()->assertMissing("1/{$file1->hashName()}");

        Storage::disk()->assertMissing("1/{$file2->hashName()}");
    }
}
