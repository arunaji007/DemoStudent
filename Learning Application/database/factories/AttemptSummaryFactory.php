<?php

namespace Database\Factories;

use App\Models\Attempt;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Answer;
use App\Models\Question;


class AttemptSummaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\AttemptSummary::class;
    public function definition()
    {
        return [
            "answer_type" => $this->faker->numberBetween(1, 3),
            "answer" => $this->faker->word(5),
            "mark" => $this->faker->numberBetween(0, 1),
            "attempt_id" => Attempt::factory(),
            "question_id" => Question::factory(),
            "answer_id" => Answer::factory(),
        ];
    }
}
