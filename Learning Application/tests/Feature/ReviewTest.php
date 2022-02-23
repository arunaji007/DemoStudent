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
        Review::factory()->create(['user_id' => ($this->user['id']), "content_id" => Content::all()->random()->id, "notes" => "hi", "like" => 1, "lastRead" => 10, "lastWatched" => "00:00:00"]);
    }
    public function test_user_review_view()
    {

        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $updated_date = Carbon::now();
        $this->json('GET', 'api/v1/contents/' . $id . '/view')->assertStatus(200)->assertJson(['reviews' => [
            'id' => 1,
            'notes' => "hi",
            'like' => 1,
            'lastRead' => 10,
            'lastWatched' => "00:00:00",
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'updated_at' => $updated_date]);
    }
    public function test_user_review_update_notes()
    {
        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $data = ['notes' => 'Sas'];
        $this->json('PUT', 'api/v1/contents/' . $id . '/review', $data)->assertStatus(201)->assertJson(['reviews' => [
            'id' => 1,
            'notes' => 'Sas',
            'like' => 1,
            'lastWatched'
            => "00:00:00",
            'lastRead'
            => 10,
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'notes' => $data['notes']]);
    }
    public function test_user_review_update_like()
    {
        $id = Content::all()->random()->id;
        $user_id = $this->user['id'];
        $data = ['like' => 2];
        $this->json('PUT', 'api/v1/contents/' . $id . '/review', $data)->assertStatus(201)->assertJson(['reviews' => [
            'id' => 1,
            'notes' => 'hi',
            'like' => 2,
            'lastWatched'
            => "00:00:00",
            'lastRead'
            => 10,
        ]]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user_id, 'content_id' => $id, 'like' => $data['like']]);
    }
}
