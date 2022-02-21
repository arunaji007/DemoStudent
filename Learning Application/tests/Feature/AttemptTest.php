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
    }
    public function test_user_create_attempt()
    {
        $id = Exercise::all()->random()->id;
        $this->json('POST', 'api/v1/exercise/' . $id . '/attempts')->assertStatus(200)
            ->assertJsonStructure(["attempt" => [
                "user_id",
                "exercise_id",
                "score",
                "duration",
                "updated_at",
                "created_at",
                "id",
            ]]);
    }
    public function test_user_delete_attempt()
    {
        $id = Exercise::all()->random()->id;
        Attempt::factory()->create(["user_id" => $this->user['id']]);
        $aid = Attempt::all()->random()->id;
        $this->json('delete', 'api/v1/exercises/' . $id . '/' . 'attempts/' . $aid)->assertStatus(200)->assertJsonStructure(["message"]);
    }
}
