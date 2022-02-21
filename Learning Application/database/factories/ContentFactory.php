<?php

namespace Database\Factories;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Content::class;

    public function definition()
    {
        return [
            //
            'name' => $this->faker->word(5),
            'path' => $this->faker->imageUrl(),
            'type' => $this->faker->numberBetween(1, 2),
            'pages' => $this->faker->numberBetween(1, 1000),
            'duration' => $this->faker->time('H:i:s', "01:00:04"),
            'chapter_id'
            => Chapter::all()->random()->id,

        ];
    }
}
