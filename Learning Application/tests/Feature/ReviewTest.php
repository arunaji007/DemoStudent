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
use Illuminate\Support\Carbon;

class ReviewTest extends TestCase
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
        Content::factory()->count(1)->create();
        Review::factory()->create(['user_id' => ($this->user['id'])]);
    }
    public function test_user_review_view()
    {

        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $updated_date = Carbon::now();
        $this->json('GET', 'api/v1/contents/' . $id . '/review')->assertStatus(200)->assertJsonStructure(['reviews' => [
            'id',
            'notes',
            'like',
            'lastRead',
            'lastWatched'
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'updated_at' => $updated_date]);
    }
    public function test_user_review_update_notes()
    {
        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $data = ['notes' => 'Sas'];
        $this->json('PUT', 'api/v1/contents/' . $id . '/review', $data)->assertStatus(201)->assertJsonStructure(['reviews' => [
            'id',
            'user_id',
            'notes',
            'like',
            'lastWatched',
            'lastRead',
            'created_at',
            'updated_at',
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'notes' => $data['notes']]);
    }
    public function test_user_review_update_like()
    {
        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $data = ['like' => 1];
        $this->json('PUT', 'api/v1/contents/' . $id . '/review', $data)->assertStatus(201)->assertJsonStructure(['reviews' => [
            'id',
            'user_id',
            'notes',
            'like',
            'lastWatched',
            'lastRead',
            'created_at',
            'updated_at',
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'like' => $data['like']]);
    }
}
