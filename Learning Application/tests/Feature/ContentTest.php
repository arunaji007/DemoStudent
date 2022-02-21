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
use JWTAuth;
use Illuminate\Support\Facades\Log;

class ContentTest extends TestCase
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
        Log::info($this->user);
        $token = JWTAuth::fromUser($this->user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        Subject::factory()->count(2)->create();
        Chapter::factory()->count(2)->create();
        Content::factory()->count(10)->create();
    }
    public function test_user_content_without_query_params()
    {

        $id = Chapter::all()->random()->id;
        Log::info($id);
        $this->json('GET', 'api/v1/chapters/' . $id . '/contents')->assertStatus(200)->assertJsonStructure(["contents" => [[
            "id",
            "name",
            "path"
        ]]]);
    }
    public function test_user_content_with_query_params_limit()
    {
        $limit = 2;
        $id = Chapter::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/chapters/' . $id . '/contents',
            ["limit" => $limit]
        )->assertStatus(200)->assertJsonStructure(["contents" => [[
            "id",
            "name",
            "path"
        ]]]);
    }
    public function test_user_content_with_query_params_chapter()
    {
        $id = Chapter::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/chapters/' . $id . '/contents',
            ["chapter" => 's']
        )->assertStatus(200)->assertJsonStructure(["contents" => [[
            "id",
            "name",
            "path"
        ]]]);
    }
}
