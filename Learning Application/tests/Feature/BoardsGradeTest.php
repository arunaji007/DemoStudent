<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tzsk\Otp\Facades\Otp;
use App\Models\User;
use App\Models\Grade;
use App\Models\Board;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class BoardsGradeTest extends TestCase
{
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


    public function test_user_gets_boards()
    {
        Board::factory()->create();
        Board::factory()->create();
        $this->json(
            'GET',
            'api/v1/boards'
        )->assertStatus(200)->assertJsonStructure([
            "boards" => [[
                "id",
                "name",
                "shortName",
                "image",
                "created_at",
                "updated_at",
            ]]
        ]);
    }
    public function test_user_gets_zero_boards()
    {
        $this->json(
            'GET',
            'api/v1/boards'
        )->assertStatus(200)->assertJsonStructure([]);
    }


    public function test_user_gets_grades()
    {
        Board::factory()->create();
        Board::factory()->create();
        Grade::factory()->create();
        Grade::factory()->create();
        Grade::factory()->create();
        # Log::info(Board::get());
        $id = Board::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/boards/' . $id . '/grades'
        )->assertStatus(200)->assertJsonStructure([
            "grades" => [[
                "id",
                "name",
                "board_id",
                "created_at",
                "updated_at",
            ]]
        ]);
    }

    public function test_user_gets_zero_grades()
    {
        Board::factory()->create();
        Board::factory()->create();
        $id = Board::all()->random()->id;
        $this->json(
            'GET',
            'api/v1/boards/' . $id . '/grades'
        )->assertStatus(200)->assertJsonStructure([]);
    }
}
