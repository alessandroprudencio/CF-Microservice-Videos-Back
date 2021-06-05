<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list()
    {
        Category::factory()->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $categoryKey
        );
    }

    public function test_create()
    {
        $category = Category::create([
            'name' => 'test1'
        ]);

        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'my description'
        ]);
        $this->assertEquals('my description', $category->description);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function test_edit()
    {
        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => true
        ];

        $updatedData = [
            'name' => 'test_update',
            'description' => 'description_update',
            'is_active' => false
        ];

        $category = Category::create($data);

        $category->update($updatedData);

        foreach ($updatedData as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function test_delete()
    {

        $category = Category::factory()->create();

        $category->delete();

        $this->assertNull(Category::find($category->id));

        $category->restore();

        $this->assertNotNull(Category::find($category->id));
    }
}
