<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rating = Video::RATING_LIST[array_rand(Video::RATING_LIST)];

        return [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(10),
            'year_launched' => rand(2000, 2021),
            'opened' => rand(0, 1),
            'rating' => $rating,
            'duration' => rand(30, 60),
            // 'thumb_file' => null,
            // 'banner_file' => null,
            // 'trailer_file' => null,
            // 'video-file' => null,
            // 'published' => rand(0, 1),
        ];
    }
}
