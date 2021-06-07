<?php

namespace Tests\Traits;

use Illuminate\Http\UploadedFile;
use App\Traits\UploadFiles;

trait TestUploads
{
    protected function assertInvalidationFile(
        $field,
        $extension,
        $maxSize,
        $rule
    ) {
        $routes = [
            [
                'method' => 'POST',
                'route' => $this->routeStore()
            ],
            [
                'method' => 'PUT',
                'route' => $this->routeUpdate()
            ]
        ];

        foreach ($routes as $route) {
            $file = UploadedFile::fake()->create("$field.1$extension");

            $response = [];

            if ($route['method'] === 'POST') {
                $response = $this->postJson($route['route'], [$field => $file]);
            } else {
                $response = $this->putJson($route['route'], [$field => $file]);
            }

            $this->assertInvalidationFields($response, [$field], $rule);

            $file = UploadedFile::fake()->create("$field.$extension")->size($maxSize + 1);

            $response = [];

            if ($route['method'] === 'POST') {
                $response = $this->postJson($route['route'], [$field => $file]);
            } else {
                $response = $this->putJson($route['route'], [$field => $file]);
            }

            $this->assertInvalidationFields($response, [$field], $rule);
        }
    }

    protected function assertFilesExistsInStorage($model, array $files)
    {
        /** @var UploadFiles $model */
        foreach ($files as $file) {
            \Storage::assertExists($model->relativeFilePath($file->hashName()));
        }
    }
}
