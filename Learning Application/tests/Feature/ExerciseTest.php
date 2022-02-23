<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Board;
use App\Models\Subject;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\Exercise;

use JWTAuth;
use Illuminate\Support\Facades\Log;

class ExerciseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ]);

        $token = JWTAuth::fromUser($this->user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        Board::factory()->count(1)->create();
        Grade::factory()->count(1)->create();
        Subject::factory()->count(1)->create();
        Chapter::factory()->count(1)->create();
        Exercise::factory()->count(1)->create(["name" => 'trig1', "duration" => "02:00:00", "noOfQuestions" => 40, "chapter_id" => Chapter::all()->random()->id]);
    }
    public function test_user_exercises_without_query_params()

    {
        Log::info(Chapter::all());
        $id = Chapter::all()->random()->id;
        $this->json('GET', 'api/v1/chapters/' . $id . '/exercises')->assertStatus(200)->assertJson(['exercises' => [
            [
                'id' => 1,
                'name' => "trig1",
                'duration' => "02:00:00",
                'noOfQuestions' => 40,
            ]
        ]]);
    }
}
