<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Exercise;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Question::class;
    public function definition()
    {
        return [
            //
            "content" => $this->faker->word(10),
            "type" => $this->faker->numberBetween(1, 3),
            "maxMark" => $this->faker->numberBetween(50, 100),
            "exercise_id"
            => Exercise::all()->random()->id,
        ];
    }
}
