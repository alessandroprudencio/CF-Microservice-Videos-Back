<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Genre;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_list()
    {
        Genre::factory()->count(1)->create();

        $genres = Genre::all();

        $this->assertCount(1, $genres);

        $genreKey = array_keys($genres->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $genreKey
        );
    }

    public function test_create()
    {
        $genre = Genre::create([
            'name' => 'test1'
        ]);

        $genre->refresh();

        $this->assertEquals(36, strlen($genre->id));
        $this->assertEquals('test1', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function test_edit()
    {
        $data = [
            'name' => 'test',
            'is_active' => true
        ];

        $updatedData = [
            'name' => 'test_update',
            'is_active' => false
        ];

        $genre = Genre::create($data);

        $genre->update($updatedData);

        foreach ($updatedData as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function test_delete()
    {

        $genre = Genre::factory()->create();

        $genre->delete();

        $this->assertNull(Genre::find($genre->id));

        $genre->restore();

        $this->assertNotNull(Genre::find($genre->id));
    }
}
