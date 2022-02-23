<?php

namespace Tests\Feature;

use App\Models\Answer;
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
use App\Models\Question;
use JWTAuth;
use Illuminate\Support\Facades\Log;

class QuestionTest extends TestCase
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
        Exercise::factory()->count(1)->create();
        Question::factory()->count(1)->create([
            "content" => "Question1",  "type" => 1,
            "maxMark" => 1,
            "exercise_id" => 1,
        ]);
        $id = Question::all()->random()->id;
        Answer::factory()->create(['content' => 'A', 'solution' => 's1', 'correct' => 0, 'question_id' => $id]);
        Answer::factory()->create(['content' => 'B', 'solution' => 's1', 'correct' => 0, 'question_id' => $id]);
        Answer::factory()->create(['content' => 'C', 'solution' => 's1', 'correct' => 0, 'question_id' => $id]);
        Answer::factory()->create(['content' => 'D', 'solution' => 's1', 'correct' => 1, 'question_id' => $id]);
        // Question::factory()->count(1)->create();
        // $id = Question::all()->random()->id;
        // Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        // Answer::factory()->create(['correct' => true, 'question_id' => $id]);
        // Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        // Answer::factory()->create(['correct' => false, 'question_id' => $id]);
    }

    public function test_user_question_with_pagination()
    {
        $eid = Exercise::all()->random()->id;
        $c = $this->json('GET', 'api/v1/exercises/' . $eid . '/questions')->assertStatus(200)->assertJson(["questions" => [
            "current_page" => 1,
            "data" => [
                [
                    "id" => 1,
                    "content" => "Question1",
                    "type" => 1,
                    "maxMark" => 1,
                    "exercise_id" => 1,
                ],
            ],
        ]])->decodeResponseJson();
        Log::info(json_encode($c));
    }

    public function test_user_question_with_unknown_page()
    {
        $eid = Exercise::all()->random()->id;

        $c = $this->json('GET', 'api/v1/exercises/' . $eid . '/questions', ["page" => 2])->assertStatus(200)->assertJson(["questions" => [
            "current_page" => 2,
            "data" => [],
        ]])->decodeResponseJson();
        Log::info(json_encode($c));
    }
}
