<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tzsk\Otp\Facades\Otp;
use App\Models\User;

class VerifyOTPTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function test_user_verify_otp_unsuccessful()
    {
        User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671111,
            "dob" => "2007/07/07"
        ]);
        $userData = [
            "mobile_no" => 9815671111,
            "otp" => 101011,
        ];
        $otp = Otp::expiry(1)->generate(env('OTP_SECRET'));
        $this->json('POST', 'api/v1/verify-otp', $userData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson(['message' => "Invalid Otp"]);
    }

    public function test_user_verify_otp_successful()
    {
        User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671111,
            "dob" => "2007/07/07"
        ]);
        $otp = Otp::expiry(1)->generate(env('OTP_SECRET'));
        $userData = [
            "mobile_no" => 9815671111,
            "otp" => $otp,
        ];
        $this->json('POST', 'api/v1/verify-otp', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
                "message",
                "user" => [
                    'id',
                    'name',
                    'email',
                    "mobile_no",
                    "dob",
                    "created_at",
                    "updated_at",
                    "board_id",
                    "grade_id",
                ],
            ]);
    }
}
