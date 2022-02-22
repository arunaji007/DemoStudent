<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Board;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class SubjectTest extends TestCase
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
        Board::factory()->count(1)->create();
        Grade::factory()->count(1)->create();
        $this->user = User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07",
            "board_id" => Board::all()->random()->id,
            "grade_id" => Grade::all()->random()->id,
        ]);
        $token = JWTAuth::fromUser($this->user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        Subject::factory()->count(10)->create();
    }
    public function test_user_subject_without_query_params()
    {
        #    Log::info(json_encode(Subject::all()));
        $c = $this->json(
            'GET',
            'api/v1/users/myself/subjects'
        )->assertStatus(200)
            ->assertJsonStructure(["subjects" => [[
                "id",
                "name",
                "grade_id",
                "created_at",
                "updated_at",
                "contents_count",
                "subjects_percentage",
                "exercise_percentage",
            ],],]);
    }

    public function test_user_subject_with_query_params_limit()
    {
        $limit = 2;
        $this->json(
            'GET',
            'api/v1/users/myself/subjects',
            ["limit" => $limit],
        )->assertStatus(200)->assertJsonStructure(["subjects" => [[
            "id",
            "name",
            "grade_id",
            "created_at",
            "updated_at",
            "contents_count",
            "subjects_percentage",
            "exercise_percentage",
        ]]]);
    }

    public function test_user_subject_with_query_param_subject()
    {
        Subject::factory()->create(['name' => "science"]);
        $this->json(
            'GET',
            'api/v1/users/myself/subjects',
            ["chapter" => 's'],
        )->assertStatus(200)->assertJsonStructure(["subjects" => [[
            "id",
            "name",
            "grade_id",
            "created_at",
            "updated_at",
            "contents_count",
            "subjects_percentage",
            "exercise_percentage",
        ]]]);
    }
}
