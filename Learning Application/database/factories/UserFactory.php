<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\User;
use App\Models\Board;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */


    protected $model = User::class;

    public function definition()
    {
        return [
            //
            "name" => $this->faker->word(4),
            "mobile_no" => $this->faker->unique()->numerify('##########'),
            "email" => $this->faker->unique()->email,
            "dob" =>  $this->faker->dateTimeBetween('1950-01-01', '2019-01-01')->format('Y/m/d'),
            // "board_id" => (Board::all()->random()->id),
            // "grade_id" => (Grade::all()->random()->id),
        ];
    }
}
