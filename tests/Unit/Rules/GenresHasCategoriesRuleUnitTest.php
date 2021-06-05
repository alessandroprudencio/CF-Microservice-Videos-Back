<?php

namespace Tests\Feature\Rules;

use Tests\TestCase;
use App\Rules\GenresHasCategoriesRule;

class GenresHasCategoriesRuleUnitTest extends TestCase
{
    public function test_categories_id_field()
    {
        $rule =  new GenresHasCategoriesRule(
            [1, 1, 2, 2]
        );

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);

        $reflectionProperty = $reflectionClass->getProperty('categoriesId');

        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);

        $this->assertEqualsCanonicalizing([1, 2], $categoriesId);
    }

    public function test_genres_id_value()
    {
        $rule = $this->createRuleMock([]);

        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1, 1, 2, 2]);

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);

        $reflectionProperty = $reflectionClass->getProperty('genresId');

        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);

        $this->assertEqualsCanonicalizing([1, 2], $genresId);
    }

    public function test_passes_returns_false_when_categories_or_genres_is_array_empty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function test_passes_returns_false_when_has_categories_without_genres()
    {
        $rule =  $this->createRuleMock([1, 2]);

        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));

        $this->assertFalse($rule->passes('', [1]));
    }

    public function test_passes_is_valid()
    {
        $rule = $this->createRuleMock([1, 2]);

        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));

        $this->assertTrue($rule->passes('', [1]));
    }


    protected function createRuleMock(array $categoriesId)
    {
        return
            $this->instance(Request::class, \Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId]))
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
