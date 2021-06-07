<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestSaves
{
    protected function assertStore(
        array $sendData,
        array $testDatabase,
        array $testJsonData = null
    ): TestResponse {
        $response = $this->postJson($this->routeStore(), $sendData);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}: \n {$response->content()}");
        }

        $model = $this->model();

        $table = (new $model)->getTable();

        $this->assertDatabaseHas($table, $testDatabase + ['id' =>  $this->getIdFromResponse($response)]);

        $testResponse = $testJsonData ?? $testDatabase;

        $response->assertJsonFragment($testResponse + ['id' =>  $this->getIdFromResponse($response)]);

        return $response;
    }

    protected function assertUpdate(
        array $sendData,
        array $testDatabase,
        array $testJsonData = null
    ): TestResponse {
        $response = $this->putJson($this->routeUpdate(), $sendData);

        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 201, given {$response->status()}: \n {$response->content()}");
        }

        $this->assertInDatabase($response, $testDatabase);

        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    private function assertInDatabase($response, $testDatabase)
    {
        $model = $this->model();

        $table = (new $model)->getTable();

        $this->assertDatabaseHas($table, $testDatabase + ['id' =>  $this->getIdFromResponse($response)]);
    }

    private function assertJsonResponseContent($response, $testDatabase, $testJsonData)
    {
        $testResponse = $testJsonData ?? $testDatabase;

        $response->assertJsonFragment($testResponse + ['id' =>  $this->getIdFromResponse($response)]);
    }

    private function getIdFromResponse(TestResponse $response)
    {
        return $response->json('id') ?? $response->json('data.id');
    }
}
