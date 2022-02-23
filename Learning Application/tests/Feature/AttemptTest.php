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
        Attempt::factory()->create([
            "score" => 10,
            "duration" => "00:10:00",
            "user_id" => $this->user['id'],
            "exercise_id" => Exercise::all()->random()->id,
        ]);
    }
    public function test_user_create_attempt()
    {
        $id = Exercise::all()->random()->id;
        $content = $this->json('POST', 'api/v1/exercise/' . $id . '/attempts')->assertStatus(201)
            ->assertJson(["attempt" => [
                "score" => 0,
                "duration" => "00:00:00",
                "id" => 2,
            ]])->decodeResponseJson();

        $this->assertDatabaseHas('Attempts', ['id' => $content["attempt"]['id']]);
    }
    public function test_user_delete_attempt()
    {
        $id = Exercise::all()->random()->id;
        Attempt::factory()->create(["user_id" => $this->user['id']]);
        $aid = Attempt::all()->random()->id;
        $this->json('delete', 'api/v1/exercises/' . $id . '/' . 'attempts/' . $aid)->assertStatus(200)->assertJson(["message" => "deleted attempt"]);
    }

    public function test_user_get_attempts()
    {
        $id = Chapter::all()->random()->id;
        $this->json('GET', 'api/v1/chapters/' . $id . '/attempts')->assertStatus(200)
            ->assertJson(["attempt" => [[
                "exercise_id" => 1,
                "chapter_id" => 1,
                "high_score" => 10,
                "attempt_count" => 1,
            ]]]);
    }

    public function test_user_update_attempt()
    {
        Attempt::factory()->create();
        $id = Exercise::all()->random()->id;
        $aid = Attempt::all()->random()->id;
        $data = ["score" => 89, "duration" => "01:00:09"];
        $this->json('PUT', 'api/v1/exercises/' . $id . '/attempts' . '/' . $aid, $data)->assertStatus(200)
            ->assertJson(["attempts" => [
                [
                    "id" => 1,
                    "score" => 89,
                    "duration" => "01:00:09",
                ]
            ]]);

        $this->assertDatabaseHas('attempts', ["id" => $aid, "score" => $data['score'], "duration" => $data["duration"]]);
    }
}
