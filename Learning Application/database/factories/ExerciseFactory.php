<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Chapter;

class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Exercise::class;
    public function definition()
    {
        return [
            //
            'name' => $this->faker->word(5),
            'duration' => $this->faker->time('H:i:s', "01:00:00"),
            'noOfQuestions' => $this->faker->numberBetween(30, 50),
            "chapter_id" => Chapter::all()->random()->id,
        ];
    }
}
