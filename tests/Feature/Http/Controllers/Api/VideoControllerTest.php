<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Video;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;

class VideoControllerTest extends TestCase
{
    use TestValidations, TestSaves;

    private $data;
    private $newData;
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = Video::factory()->create();
        $this->data = [
            'title' => "Homen da lua",
            'description' => "HISTORIA DE UM HOMEM NA LUA",
            'rating' => Video::RATING_LIST[0],
            'duration' => 30,
            'year_launched' => 2020,
        ];
        $this->newData = [
            'title' => "Homen da lua 2",
            'description' => "HISTORIA DE UM HOMEM NA LUA 2",
            'rating' => Video::RATING_LIST[1],
            'duration' => 30,
            'year_launched' => 2022,
        ];
    }

    public function test_index()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }

    public function test_show()
    {
        $response = $this->getJson(route('videos.show', $this->video->id));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function test_invalidation_required()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => ''
        ];

        $errors = [
            'errors.title' => 'array',
            'errors.description' => 'array',
            'errors.year_launched' => 'array',
            'errors.rating' => 'array',
            'errors.duration' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_invalidation_max()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];

        $errors = [
            'errors.title' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_invalidation_integer()
    {
        $data = [
            'duration' => 's'
        ];

        $errors = [
            'errors.duration' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_invalidation_year_launched_field()
    {
        $data = [
            'year_launched' => 's'
        ];

        $errors = [
            'errors.year_launched' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_invalidation_opened_field()
    {
        $data = [
            'opened' => 's'
        ];

        $errors = [
            'errors.opened' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_invalidation_rating_field()
    {
        $data = [
            'rating' => 0
        ];

        $errors = [
            'errors.rating' => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }


    public function test_save()
    {
        $data = [
            [
                'send_data' => $this->data,
                'test_data' => $this->data + ['opened' => false],
            ],
            [
                'send_data' => $this->data + ['opened' => true],
                'test_data' => $this->data + ['opened' => true],
            ],
            [
                'send_data' => $this->data + ['rating' => Video::RATING_LIST[1]],
                'test_data' => $this->data + ['rating' => Video::RATING_LIST[1]],
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure(
                [
                    'created_at',
                    'updated_at'
                ]
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure(
                [
                    'created_at',
                    'updated_at'
                ]
            );
        }
    }

    public function test_destroy()
    {
        $response = $this->deleteJson(route('videos.destroy', $this->video->id));

        $response->assertStatus(204);

        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}
