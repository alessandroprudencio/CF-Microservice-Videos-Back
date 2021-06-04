<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Genre;
use App\Models\Category;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use App\Http\Controllers\Api\GenreController;

class GenreControllerTest extends TestCase
{
    use TestValidations, TestSaves;

    private $data;

    private $newData;

    private $genre;

    function setUp(): void
    {
        parent::setUp();

        $this->genre = Genre::factory()->create();

        $this->data = [
            'name' => 'test genre'
        ];
        $this->newData = [
            'name' => 'new genre',
            'is_active' => false
        ];
    }

    public function test_rollback_store()
    {
        $controller = $this->instance(GenreController::class, \Mockery::mock(GenreController::class))
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->data);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = $this->instance(Request::class, \Mockery::mock(Request::class));

        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function test_index()
    {
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function test_show()
    {
        $response = $this->getJson(route('genres.show', $this->genre->id));

        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function test_invalidation_data()
    {
        $this->assertInvalidationInStoreAction(
            ['name' => '', 'categories_id' => ''],
            ['errors.name' => 'array', 'errors.categories_id' => 'array']
        );

        $this->assertInvalidationInUpdateAction(
            ['name' => '', 'categories_id' => ''],
            ['errors.name' => 'array', 'errors.categories_id' => 'array']
        );

        $this->assertInvalidationInStoreAction(
            ['name' => str_repeat('a', 256), 'is_active' => 'a'],
            [
                'errors.name' => 'array',
                'errors.is_active' => 'array',
            ]
        );

        $this->assertInvalidationInUpdateAction(
            ['name' => str_repeat('a', 256), 'is_active' => 'a'],
            [
                'errors.name' => 'array',
                'errors.is_active' => 'array',
            ]
        );

        $this->assertInvalidationInStoreAction(
            ['categories_id' => 'a'],
            ['errors.categories_id' => 'array']
        );

        $this->assertInvalidationInUpdateAction(
            ['categories_id' => 'a'],
            ['errors.categories_id' => 'array']
        );

        $this->assertInvalidationInStoreAction(
            ['categories_id' => [100]],
            ['errors.categories_id' => 'array']
        );

        $this->assertInvalidationInUpdateAction(
            ['categories_id' => [100]],
            ['errors.categories_id' => 'array']
        );

        $category = Category::factory()->create();
        $category->delete();

        $this->assertInvalidationInStoreAction(
            ['categories_id' => $category->id],
            ['errors.categories_id' => 'array']
        );

        $this->assertInvalidationInUpdateAction(
            ['categories_id' => $category->id],
            ['errors.categories_id' => 'array']
        );
    }

    public function test_sync_categories()
    {
        $categoriesId = Category::factory()->count(3)->create()->pluck('id')->toArray();

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response =  $this->postJson($this->routeStore(), $sendData);

        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $response->json('id'),
            'category_id' => $categoriesId[0],
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->putJson($this->routeUpdate(), $sendData);

        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $response->json('id'),
            'category_id' => $categoriesId[1]
        ]);
    }

    public function test_store()
    {
        $category = Category::factory()->create();

        $response = $this->assertStore(
            $this->data + ['categories_id' => [$category->id]],
            $this->data + ['is_active' => true, 'deleted_at' => null]
        );

        $response->assertJsonStructure(
            [
                'created_at',
                'updated_at'
            ]
        );
    }

    public function test_update()
    {
        $categoryId = Category::factory()->create()->id;

        $data = [
            'name' => 'test',
            'is_active' => true
        ];

        $response = $this->assertUpdate(
            $data + ['categories_id' => [$categoryId]],
            $data + ['deleted_at' => null]
        );

        $response->assertJsonStructure(
            [
                'created_at',
                'updated_at'
            ]
        );
    }

    public function test_destroy()
    {
        $response = $this->deleteJson(route('genres.destroy', $this->genre->id));

        $response->assertStatus(204);

        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function assertInvalidationRequired($response)
    {
        $this->assertInvalidationFields(
            $response,
            ['name'],
            ['errors.name' => 'array']
        );

        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationData($response)
    {
        $this->assertInvalidationFields(
            $response,
            ['name', 'is_active'],
            [
                'errors.name' => 'array',
                'errors.is_active' => 'array',
            ]
        );
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
