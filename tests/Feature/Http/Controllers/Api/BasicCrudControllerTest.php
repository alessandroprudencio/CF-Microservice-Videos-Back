<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;

use Tests\Stubs\Models\CategoryStub;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\BasicCrudController;

class BasicCrudControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        CategoryStub::dropTable();

        CategoryStub::createTable();

        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();

        parent::tearDown();
    }

    public function test_index()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $resource = $this->controller->index();

        $serialized = $resource->response()->getData(true);

        $this->assertEquals(
            [$category->toArray()],
            $serialized['data']
        );

        $this->assertArrayHasKey('meta', $serialized);

        $this->assertArrayHasKey('links', $serialized);
    }

    public function test_invalidation_data_in_store()
    {
        $this->expectException(ValidationException::class);

        $request =  $this->instance(Request::class, \Mockery::mock(Request::class));

        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);

        $this->controller->store($request);
    }

    public function test_store()
    {
        $request =  $this->instance(Request::class, \Mockery::mock(Request::class));

        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'test_description']);

        $resource = $this->controller->store($request);

        $serialized = $resource->response()->getData(true);

        $this->assertEquals(CategoryStub::first()->toArray(), $serialized['data']);
    }

    public function test_if_find_or_fail_fetch_model()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);

        $reflectionMethod =  $reflectionClass->getMethod('findOrFail');

        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);

        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function test_if_find_or_fail_throw_exception_when_invalid_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);

        $reflectionMethod =  $reflectionClass->getMethod('findOrFail');

        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function test_show()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $resource = $this->controller->show($category->id);

        $serialized = $resource->response()->getData(true);

        $this->assertEquals($category->toArray(), $serialized['data']);
    }

    public function test_update()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);

        $request = $this->instance(Request::class, \Mockery::mock(Request::class));

        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test name update', 'description' => 'test description update']);

        $resource = $this->controller->update($request, $category->id);

        $serialized = $resource->response()->getData(true);

        $category->refresh();

        $this->assertEquals($category->toArray(), $serialized['data']);
    }

    public function test_destroy()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);

        $response = $this->controller->destroy($category->id);

        $this
            ->createTestResponse($response)
            ->assertStatus(204);

        $this->assertCount(0, CategoryStub::all());
    }
}
