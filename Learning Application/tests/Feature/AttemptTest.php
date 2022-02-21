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
use JWTAuth;

class AttemptTest extends TestCase
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
        Attempt::factory()->create();
    }
    public function test_user_create_attempt()
    {
        $id = Exercise::all()->random()->id;
        $content = $this->json('POST', 'api/v1/exercise/' . $id . '/attempts')->assertStatus(200)
            ->assertJsonStructure(["attempt" => [
                "user_id",
                "exercise_id",
                "score",
                "duration",
                "updated_at",
                "created_at",
                "id",
            ]])->decodeResponseJson();


        $this->assertDatabaseHas('Attempts', ['id' => $content["attempt"]['id']]);
    }
    public function test_user_delete_attempt()
    {
        $id = Exercise::all()->random()->id;
        Attempt::factory()->create(["user_id" => $this->user['id']]);
        $aid = Attempt::all()->random()->id;
        $this->json('delete', 'api/v1/exercises/' . $id . '/' . 'attempts/' . $aid)->assertStatus(200)->assertJsonStructure(["message"]);
    }

    public function test_user_get_attempts()
    {
        $id = Chapter::all()->random()->id;
        $this->json('GET', 'api/v1/chapters/' . $id . '/attempts')->assertStatus(200)
            ->assertJsonStructure(["attempt" => [
                "exercise_id",
                "chapter_id",
                "high_score",
                "attempt_count",
            ]]);
    }

    public function test_user_update_attempt()
    {
        Attempt::factory()->create();
        $id = Exercise::all()->random()->id;
        $aid = Attempt::all()->random()->id;
        $data = ["score" => 89, "duration" => "01:00:09"];
        $this->json('PUT', 'api/v1/exercises/' . $id . '/attempts' . '/' . $aid, $data)->assertStatus(200)
            ->assertJsonStructure(["attempts" => [
                [
                    "id",
                    "score",
                    "duration",
                    "user_id",
                    "exercise_id",
                    "deleted_at",
                    "created_at",
                    "updated_at",
                ]
            ]]);

        $this->assertDatabaseHas('attempts', ["id" => $aid, "score" => $data['score'], "duration" => $data["duration"]]);
    }
}
