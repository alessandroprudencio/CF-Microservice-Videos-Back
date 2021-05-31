<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestValidations
{

    protected function assertInvalidationInStoreAction(
        array $data,
        $ruleParams = []
    ) {
        $response = $this->postJson($this->routeStore(), $data);

        $fields = array_keys($data);

        $this->assertInvalidationFields($response, $fields, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        $ruleParams = []
    ) {
        $response = $this->putJson($this->routeUpdate(), $data);

        $fields = array_keys($data);

        $this->assertInvalidationFields($response, $fields, $ruleParams);
    }


    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        array $ruleParams = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJson(
                fn ($json) =>
                $json->whereType('message', 'string')
                    ->whereAllType($ruleParams)
            );
        }
    }
}
