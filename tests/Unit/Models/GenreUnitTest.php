<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class GenreUnitTest extends TestCase
{
    private $genre;

    protected function setup(): void
    {
        parent::setup();
        $this->genre = new Genre();
    }

    public function test_fillable_attribute()
    {
        $this->assertEquals(['name', 'is_active'], $this->genre->getFillable());
    }

    public function test_incrementing_attribute()
    {
        $this->assertFalse($this->genre->getIncrementing());
    }

    public function test_keytype_attribute()
    {
        $this->assertEquals('string', $this->genre->getKeyType());
    }

    public function test_dates_attribute()
    {
        $dates =  ['deleted_at', 'created_at', 'updated_at'];

        $this->assertEquals($dates, $this->genre->getDates());

        $this->assertCount(count($dates), $this->genre->getDates());
    }

    public function test_if_use_traits()
    {
        $traits =  [
            SoftDeletes::class,
            HasFactory::class,
            Uuid::class,
        ];

        $categoryTraits = array_keys(class_uses(Genre::class));

        $this->assertEquals($traits, $categoryTraits);
    }
}
