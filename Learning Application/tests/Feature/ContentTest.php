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


        $token = JWTAuth::fromUser($this->user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        Subject::factory()->count(1)->create();
        Chapter::factory()->count(1)->create();
        Content::factory()->count(1)->create(['name' => 'trig1', "path" => "www.content"]);
        Review::factory()->create(['user_id' => ($this->user['id']), "content_id" => Content::all()->random()->id, "notes" => "hi", "like" => 1, "lastRead" => 10, "lastWatched" => "00:20:00"]);
    }
    public function test_user_content_without_query_params()
    {

        Log::info(Content::all());
        $id = Chapter::all()->random()->id;

        $this->json('GET', 'api/v1/chapters/' . $id . '/contents')->assertStatus(200)->assertJson(["contents" => [[
            "id" => 1,
            "name" => 'trig1',
            "path" => "www.content",
        ]]]);
    }
    public function test_user_content_with_query_params_limit()
    {
        Log::info(Chapter::all());
        Log::info(Content::all());
        $id = Chapter::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/chapters/' . $id . '/contents',
            ["limit" => 1]
        )->assertStatus(200)->assertJson(["contents" => [[
            "id" => 1,
            "name" => 'trig1',
            "path" => "www.content",
        ]]]);
    }
    public function test_user_content_with_query_params_content()
    {
        $id = Chapter::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/chapters/' . $id . '/contents',
            ["chapter" => 't
            .']
        )->assertStatus(200)->assertJson(["contents" => [[
            "id" => 1,
            "name" => 'trig1',
            "path" => "www.content",
        ]]]);
    }
    public function test_user_contents()
    {
        Log::info(Content::all());
        Log::info(Review::all());
        $this->json(
            'GET',
            '/api/v1/users/myself/contents'
        )->assertStatus(200)->assertJson(["contents" => [[
            "id" => 1,
            "name" => "trig1",
            "path" => "www.content",
        ]]]);
    }
}
