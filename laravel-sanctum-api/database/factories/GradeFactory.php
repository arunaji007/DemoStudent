<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Grade::class;

    public function definition()
    {
        return [
            //
            'name' => $this->faker->name(),

        ];
    }
}
