<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;
use App\Models\User;
use App\Models\Content;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Review::class;
    public function definition()
    {
        return [
            //

            'user_id' =>  User::factory(),
            'content_id' => Content::all()->random()->id,
            'notes' => '',
            'like' => 0,
            'lastWatched' => $this->faker->time('H:i:s', "01:00:04"),
            'lastRead' => 2,
        ];
    }
}
