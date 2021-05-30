<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use App\Models\Genre;

class GenreControllerTest extends TestCase
{

    protected $data = [
        'name' => 'test controller'
    ];

    protected $newData = [
        'name' => 'new name',
    ];

    public function test_index()
    {
        $genre = Genre::factory()->create();

        $response = $this->get(route('genres.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$genre->toArray()]);
    }

    public function test_show()
    {
        $genre = Genre::factory()->create();

        $response = $this->getJson(route('genres.show', $genre->id));

        $response
        ->assertStatus(200)
        ->assertJson($genre->toArray());
    }

    public function test_invalidation_data()
    {
        $response = $this->postJson(route('genres.store'), []);

        $this->assertInvalidationRequired($response);

        $response = $this->postJson(route('genres.store'), ['name'=>str_repeat('a', 256), 'is_active' => 'a']);
        $this->assertInvalidationData($response);

        $genre = Genre::factory()->create();
        $response = $this->putJson(route('genres.update', ['genre' => $genre->id ]), []);
        $this->assertInvalidationRequired($response);

        $genre = Genre::factory()->create();
        $response = $this->putJson(route('genres.update', ['genre' => $genre->id ]), ['name'=>str_repeat('a', 256), 'is_active' => 'a']);
        $this->assertInvalidationData($response);
    }

    public function test_store()
    {
        $response = $this->postJson(route('genres.store'), $this->data);

        $response
            ->assertStatus(201)
            ->assertJson($this->data);

        $this->assertTrue($response->json('is_active'));

        $response = $this->postJson(
            route('genres.store'),
            array_merge($this->data, [ 'is_active' => false])
        );

        $response
            ->assertJsonFragment(   [
                'is_active' => false,
            ])
            ->assertStatus(201)
            ->assertJson($this->data);
    }

    public function test_update()
    {

        $genre = Genre::factory()->create();

        $response = $this->putJson(route('genres.update', $genre->id), $this->newData);

        $response
            ->assertStatus(200)
            ->assertJson($this->newData)
            ->assertJsonFragment([
                'is_active' => true,
            ]);
    }

    public function test_destroy()
    {
        $genre = Genre::factory()->create();
        $response = $this->deleteJson(route('genres.destroy', $genre->id));

        $response->assertStatus(204);

        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

    protected function assertInvalidationRequired($response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJson(fn ($json) =>
                $json->whereType('message', 'string')
                    ->whereAllType([
                        'errors.name' => 'array',
                    ])
                );
    }

    protected function assertInvalidationData($response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJson(fn ($json) =>
                $json->whereType('message', 'string')
                    ->whereAllType([
                        'errors.name' => 'array',
                        'errors.is_active' => 'array',
                    ])
                );
    }
}
