<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Board;

class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Board::class;

    public function definition()
    {
        return [
            //return [
            'name' => $this->faker->word(5),
            'shortname' => $this->faker->word(2),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
