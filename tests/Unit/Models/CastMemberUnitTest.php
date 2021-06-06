<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CastMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class CastMemberUnitTest extends TestCase
{
    private $category;

    protected function setup(): void
    {
        parent::setup();
        $this->category = new CastMember();
    }

    public function test_fillable_attribute()
    {
        $this->assertEquals(['name', 'type'],  $this->category->getFillable());
    }

    public function test_incrementing_attribute()
    {
        $this->assertFalse($this->category->getIncrementing());
    }

    public function test_keytype_attribute()
    {
        $this->assertEquals('string',  $this->category->getKeyType());
    }

    public function test_dates_attribute()
    {
        $dates =  ['deleted_at', 'created_at', 'updated_at'];

        $this->assertEquals($dates,  $this->category->getDates());

        $this->assertCount(count($dates),  $this->category->getDates());
    }

    public function test_if_use_traits()
    {
        $traits =  [
            SoftDeletes::class,
            HasFactory::class,
            Uuid::class,
        ];

        $categoryTraits = array_keys(class_uses(CastMember::class));

        $this->assertEquals($traits, $categoryTraits);
    }
}
