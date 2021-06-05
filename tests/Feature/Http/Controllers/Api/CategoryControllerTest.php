<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Category;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use TestValidations, TestSaves, RefreshDatabase;

    protected $data = [
        'name' => 'test controller',
        'description' => null
    ];

    protected $newData = [
        'name' => 'new name',
        'description' => 'new description'
    ];

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }

    public function test_show()
    {
        $response = $this->getJson(route('categories.show', $this->category->id));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
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
        $response = $this->assertStore($this->data, $this->data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure([
            'created_at', 'updated_at',
        ]);

        $this->assertStore($this->newData, $this->newData + ['description' => 'new description']);
    }

    public function test_update()
    {
        $this->assertUpdate($this->newData, $this->newData + ['description' => 'new description']);

        $this->assertUpdate($this->data, array_merge($this->data, ['description' => null]));

        $this->newData['description'] = null;
        $this->assertUpdate($this->newData, $this->newData + ['description' => null]);
    }

    public function test_destroy()
    {
        $response = $this->deleteJson(route('categories.destroy', $this->category->id));

        $response->assertStatus(204);

        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
