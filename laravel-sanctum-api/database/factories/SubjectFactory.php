<?php

namespace Database\Factories;

use \App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Generator as Faker;

class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Subject::class;

    public function definition()
    {
        return [
            //
            'name' => $this->faker->word(5),
            "gradeId" => (Grade::all()->random()->id),
        ];
    }
}
