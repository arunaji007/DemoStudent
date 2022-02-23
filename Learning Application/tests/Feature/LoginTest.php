<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Support\RefreshFlow;
use Illuminate\Support\Facades\Log;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function test_user_exists_or_not()
    {
        User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ]);
        $userData = [
            "mobile_no" => 9815671111,
        ];
        $this->json('POST', 'api/v1/login', $userData, ['Accept' => 'application/json'])
            ->assertStatus(404)
            ->assertJson(['message' => "Given MobileNumber not found"]);
    }
    public function test_user_login_otp_sent()
    {
        User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671111,
            "dob" => "2007/07/07"
        ]);
        $userData = [
            "mobile_no" => 9815671111,
        ];
        $this->json('POST', 'api/v1/login', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson(['message' => "OTP sent"]);
    }
}
