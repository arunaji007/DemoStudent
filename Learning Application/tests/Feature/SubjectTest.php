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
        $this->user = User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ]);
        $token = JWTAuth::fromUser($this->user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
    }
    public function test_user_subject_without_query_params()
    {
        Board::factory()->create();
        Grade::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        Subject::factory()->create();
        $this->json(
            'GET',
            'api/v1/users/myself/subjects',
        )->assertStatus(200)->assertJsonStructure(["message" => "subjects data", "subjects" => [
            'id',
            'name',
        ]]);
    }
    public function test_user_subject_with_query_params()
    {
        $limit = 2;
        $this->json(
            'GET',
            'api/v1/users/myself/subjects',
            ["limit" => $limit],
        )->assertStatus(200)->assertJsonStructure(["message" => "subjects data", "subjects" => [[
            'id',
            'name',
        ]]]);
    }

    public function test_user_subject_with_query_param_subject()
    {
        $this->json(
            'GET',
            'api/v1/users/myself/subjects',
            ["chapter" => 's'],
        )->assertStatus(200)->assertJsonStructure(["message" => "subjects data", "subjects" => [[
            'id',
            'name',
        ]]]);
    }
}
