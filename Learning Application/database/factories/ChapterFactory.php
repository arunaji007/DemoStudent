<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subject;

class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Chapter::class;
    public function definition()
    {
        return [
            //
            'name' => $this->faker->word(5),
            'subject_id' => Subject::all()->random()->id,
            'noOfExercises' => $this->faker->numberBetween(1, 10),
        ];
    }
}
