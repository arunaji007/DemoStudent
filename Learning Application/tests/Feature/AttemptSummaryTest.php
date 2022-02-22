<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Board;
use App\Models\Subject;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\Review;
use App\Models\Exercise;
use App\Models\Attempt;
use App\Models\Answer;
use App\Models\Question;
use App\Models\AttemptSummary;
use JWTAuth;

class AttemptSummaryTest extends TestCase
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
        Question::factory()->count(1)->create();
        $id = Question::all()->random()->id;
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        Answer::factory()->create(['correct' => true, 'question_id' => $id]);
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        Attempt::factory()->count(1)->create();
        AttemptSummary::factory()->create(["attempt_id" => Attempt::all()->random()->id, "question_id" => $id]);
        Question::factory()->count(1)->create();
        $id = Question::all()->random()->id;
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        Answer::factory()->create(['correct' => true, 'question_id' => $id]);
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);
        Answer::factory()->create(['correct' => false, 'question_id' => $id]);

        AttemptSummary::factory()->create(["attempt_id" => Attempt::all()->random()->id, "question_id" => $id]);
    }


    public function test_user_update_attempt_summary()
    {
        $eid = Exercise::all()->random()->id;
        $aid =
            Attempt::all()->random()->id;
        $data = ["answer" => '', "mark" => 1, "attempt_id" => $aid, "question_id" => Question::all()->random()->id, "answer_id" => Answer::all()->random()->id];
        $this->json('PUT',  'api/v1/exercises/' . $eid . '/attempts' . '/' . $aid . '/attempt-summaries', $data)->assertStatus(200);
    }
}
