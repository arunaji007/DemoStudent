<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Attempt::class;
    public function definition()
    {
        return [
            //
            'score' => 0,
            'duration' => $this->faker->time('H:i:s', "01:00:00"),
            'user_id' => User::factory(),
            'exercise_id' => Exercise::all()->random()->id,
        ];
    }
}
