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
        )->assertStatus(200)->assertJson(["user" => [
            'id' => 1,
            'name' => "JohnDavid",
        ]]);
    }

    public function test_user_update_user_data()
    {
        Board::factory()->create();
        Grade::factory()->create();
        $bid = Board::all()->random()->id;
        $gid = Grade::all()->random()->id;
        $userData = [
            "name" => "Rajesh",
            "board_id" => $bid,
            "grade_id" => $gid,
        ];
        $c = $this->json(
            'PUT',
            'api/v1/users/myself/',
            $userData
        )->assertStatus(200)->assertJson(["user" => [
            'id' => 1,
            'name' => "Rajesh",
            'email' => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07",
            "board_id" => $userData['board_id'],
            "grade_id" => $userData['grade_id'],
        ],]);
        Log::info($bid);
        Log::info($gid);
        #  $this->assertDatabaseHas('users', ['mobile_no' => 9815671112, 'name' => "Rajesh", "board_id" => $bid, "grade_id" => $gid]);
    }
}
