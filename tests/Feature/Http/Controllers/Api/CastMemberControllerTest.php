<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use Tests\TestCase;
use App\Models\CastMember;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestResources;

class CastMemberControllerTest extends TestCase
{
    use TestValidations, TestSaves, RefreshDatabase, TestResources;

    protected $data = [
        'name' => 'test controller',
        'type' => CastMember::TYPE_DIRECTOR
    ];

    protected $newData = [
        'name' => 'new name',
        'type' =>  CastMember::TYPE_ACTOR
    ];

    private $castMember;

    private $fieldsSerialized = [
        'id',
        'name',
        'type',
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = CastMember::factory()->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function test_index()
    {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->fieldsSerialized
                    ],
                    'meta' => [],
                    'links' => []
                ]
            )
            ->assertJsonFragment($this->castMember->toArray());
    }

    public function test_show()
    {
        $response = $this->getJson(route('cast_members.show', $this->castMember->id));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ])
            ->assertJsonFragment($this->castMember->toArray());

        $this->assertResource($response, new CastMemberResource($this->castMember));
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
            ['name' => str_repeat('a', 256), 'type' => 'a'],
            [
                'errors.name' => 'array',
                'errors.type' => 'array',
            ]
        );

        $this->assertInvalidationInUpdateAction(
            ['name' => str_repeat('a', 256), 'type' => 's'],
            [
                'errors.name' => 'array',
                'errors.type' => 'array',
            ]
        );
    }

    public function test_store()
    {
        $data = [
            [
                'name' => 'new name',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'new name',
                'type' => CastMember::TYPE_ACTOR
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value, $value + ['deleted_at' => null]);

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $this->assertResource($response, new CastMemberResource(
                CastMember::find($response->json('data.id'))
            ));
        }
    }

    public function test_update()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->fieldsSerialized
        ]);

        $this->assertResource($response, new CastMemberResource(
            CastMember::find($response->json('data.id'))
        ));
    }

    public function test_destroy()
    {
        $response = $this->deleteJson(route('cast_members.destroy', $this->castMember->id));

        $response->assertStatus(204);

        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
