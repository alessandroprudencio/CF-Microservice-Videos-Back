<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Genre;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;

class GenreControllerTest extends TestCase
{
    use TestValidations, TestSaves;

    protected $data = [
        'name' => 'test controller'
    ];

    protected $newData = [
        'name' => 'new name',
        'is_active' => false
    ];

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = Genre::factory()->create();
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
            ['name' => ''],
            ['errors.name' => 'array']
        );

        $this->assertInvalidationInUpdateAction(
            ['name' => ''],
            ['errors.name' => 'array']
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
    }

    public function test_store()
    {
        $response = $this->assertStore($this->data, $this->data + ['is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure([
            'created_at', 'updated_at',
        ]);

        $this->assertStore($this->newData, $this->newData);
    }

    public function test_update()
    {
        $this->assertUpdate($this->newData, array_merge($this->newData, ['is_active' => false]));

        $this->assertUpdate($this->data, $this->data);
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
