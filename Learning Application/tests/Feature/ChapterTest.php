<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Board;
use App\Models\Subject;
use App\Models\Chapter;
use JWTAuth;

class ChapterTest extends TestCase
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
        Chapter::factory()->count(1)->create(["name" => "trig", "subject_id" => Subject::all()->random()->id, "noOfExercises" => 2]);
    }
    public function test_user_chapter_without_query_params()
    {

        $id = Subject::all()->random()->id;
        Log::info($id);
        $this->json('GET', 'api/v1/subjects/' . $id . '/chapters')->assertStatus(200)
            ->assertJson(['chapters' =>
            [
                [
                    'id' => 1,
                    'name' => "trig",
                    'subject_id' => 1,
                    'noOfExercises' => 2,
                ]
            ]]);
    }
    public function test_user_chapter_with_query_params_limit()
    {
        $limit = 1;
        $id = Subject::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/subjects/' . $id . '/chapters',
            ["limit" => $limit]
        )->assertStatus(200)
            ->assertJson(['chapters' =>
            [
                [
                    'id' => 2,
                    'name' => "trig",
                    'subject_id' => 2,
                    'noOfExercises' => 2,
                ]
            ]]);
    }
    public function test_user_chapter_with_query_params_chapter()
    {
        Log::info(Chapter::all());
        $id = Subject::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/subjects/' . $id . '/chapters',
            ["chapter" => 't']
        )->assertStatus(200)
            ->assertJson(['chapters' =>
            [
                [
                    'id' => 3,
                    'name' => "trig",
                    'subject_id' => 3,
                    'noOfExercises' => 2,
                ]
            ]]);
    }
}
