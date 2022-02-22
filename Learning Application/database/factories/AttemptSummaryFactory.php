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
            "answer_type"
            => 1,
            "answer" => $this->faker->word(5),
            "mark" => 1,
            "attempt_id" => Attempt::all()->random()->id,
            "question_id" => Question::all()->random()->id,
            "answer_id" => Answer::all()->random()->id,
        ];
    }
}
