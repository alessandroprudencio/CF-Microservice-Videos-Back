<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Video;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\Traits\TestResources;
use Tests\Traits\TestUploads;
use Arr;
use App\Http\Resources\VideoResource;

class VideoCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestSaves, TestUploads, TestResources;

    private $fieldsSerialized = [
        'id',
        'title',
        'description',
        'year_launched',
        'rating',
        'duration',
        'rating',
        'opened',
        'thumb_file_url',
        'banner_file_url',
        'video_file_url',
        'trailer_file_url',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ]
    ];

    public function test_index()
    {
        $response = $this->get(route('videos.index'));

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
            );
        $this->assertResource($response, VideoResource::collection(collect([$this->video])));

        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function test_show()
    {
        $response = $this->json(
            'GET',
            route('videos.show', ['video' => $this->video->id])
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

        $this->assertResource(
            $response,
            new VideoResource(Video::find($response->json('data.id')))
        );

        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function test_invalidation_required()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $errors = [
            'errors.title' => 'array',
            'errors.description' => 'array',
            'errors.year_launched' => 'array',
            'errors.rating' => 'array',
            'errors.duration' => 'array',
            'errors.categories_id' => 'array',
            'errors.genres_id' => 'array',
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

    public function test_invalidation_categories_id_field()
    {
        $this->test_invalidation_categories_genres_ids('categories_id');
    }

    public function test_invalidation_genres_id_field()
    {
        $this->test_invalidation_categories_genres_ids('genres_id');
    }

    private function test_invalidation_categories_genres_ids($field)
    {
        $data = [
            $field => 'a'
        ];

        $errors = [
            'errors.' . $field => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);

        $data = [
            $field => [100]
        ];

        $errors = [
            'errors.' . $field => 'array',
        ];

        $this->assertInvalidationInStoreAction($data, $errors);
        $this->assertInvalidationInUpdateAction($data, $errors);
    }

    public function test_save_without_files()
    {
        $testData = Arr::except($this->data, ['categories_id', 'genres_id']);

        $data = [
            [
                'send_data' => $this->data,
                'test_data' => $testData + ['opened' => false]
            ],
            [
                'send_data' => $this->data + [
                    'opened' => true,
                ],
                'test_data' => $testData + ['opened' => true]
            ],
            [
                'send_data' => $this->data + [
                    'rating' => Video::RATING_LIST[1],
                ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]]
            ],
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );

            // $this->assertIfFilesUrlExists($this->video, $response);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );

            // $this->assertIfFilesUrlExists($this->video, $response);
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
