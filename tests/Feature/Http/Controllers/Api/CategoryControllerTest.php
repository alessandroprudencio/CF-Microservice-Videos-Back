<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use App\Models\Category;

class CategoryControllerTest extends TestCase
{

    protected $data = [
        'name' => 'test controller'
    ];

    protected $newData = [
        'name' => 'new name',
        'description' => 'new description'
    ];

    public function test_index()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$category->toArray()]);
    }

    public function test_show()
    {
        $category = Category::factory()->create();

        $response = $this->getJson(route('categories.show', $category->id));

        $response
        ->assertStatus(200)
        ->assertJson($category->toArray());
    }

    public function test_invalidation_data()
    {
        $response = $this->postJson(route('categories.store'), []);

        $this->assertInvalidationRequired($response);

        $response = $this->postJson(route('categories.store'), ['name'=>str_repeat('a', 256), 'is_active' => 'a']);
        $this->assertInvalidationData($response);

        $category = Category::factory()->create();
        $response = $this->putJson(route('categories.update', ['category' => $category->id ]), []);
        $this->assertInvalidationRequired($response);

        $category = Category::factory()->create();
        $response = $this->putJson(route('categories.update', ['category' => $category->id ]), ['name'=>str_repeat('a', 256), 'is_active' => 'a']);
        $this->assertInvalidationData($response);
    }

    public function test_store()
    {
        $response = $this->postJson(route('categories.store'), $this->data);

        $response
            ->assertStatus(201)
            ->assertJson($this->data);

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->postJson(
            route('categories.store'),
            array_merge($this->data, [ 'is_active' => false, 'description' => 'description'])
        );

        $response
            ->assertJsonFragment(   [
                'is_active' => false,
                'description' => 'description'
            ])
            ->assertStatus(201)
            ->assertJson($this->data);
    }

    public function test_update()
    {

        $category = Category::factory()->create();

        $response = $this->putJson(route('categories.update', $category->id), $this->newData);

        $response
            ->assertStatus(200)
            ->assertJson($this->newData)
            ->assertJsonFragment([
                'is_active' => true,
                'description' => 'new description'
            ]);

        $response = $this->putJson(
            route('categories.update', $category->id),
            array_merge( $this->newData, ['description' => ''] )
        );

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'description' => null
            ]);
    }

    public function test_destroy()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson(route('categories.destroy', $category->id));

        $response->assertStatus(204);

        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
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
