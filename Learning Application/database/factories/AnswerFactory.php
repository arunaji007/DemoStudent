<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Question;

class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Answer::class;
    public function definition()
    {
        return [
            //
            "content" => $this->faker->word(10),
            "solution" => $this->faker->word(10),
            "question_id" => Question::factory(),
            "correct" => $this->faker->boolean(),
        ];
    }
}
