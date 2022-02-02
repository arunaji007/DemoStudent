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

class UserDataTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
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
    public function test_user_data()
    {
        $this->json(
            'GET',
            'api/v1/users/myself'
        )->assertStatus(200)->assertJsonStructure(["user" => [
            'id',
            'name',
        ]]);
    }

    public function test_user_update_user_data()
    {
        Board::factory()->create();
        Grade::factory()->create();
        Grade::factory()->create();
        Grade::factory()->create();
        $bid = Board::all()->random()->id;
        $gid = Grade::all()->random()->id;
        $userData = [
            "name" => "Rajesh",
            "board_id" => $bid,
            "grade_id" => $gid,
        ];
        $this->json(
            'PUT',
            'api/v1/users/myself/',
            $userData
        )->assertStatus(200)->assertJsonStructure(["user" => [
            'id',
            'name',
            'email',
            "mobile_no",
            "dob",
            "created_at",
            "updated_at",
            "board_id",
            "grade_id",
        ],]);

        #  $this->assertDatabaseHas('users', ['mobile_no' => 9815671112, 'name' => "Rajesh", "board_id" => $bid, "grade_id" => $gid]);
    }
}
