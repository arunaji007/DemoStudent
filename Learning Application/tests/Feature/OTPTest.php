<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class OTPTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function test_user_otp_sent()
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
        $this->json('POST', 'api/v1/send-otp', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson(['message' => "OTP sent"]);
    }

    // public function user_not_sent_otp()
    // {
    //     User::factory()->create([
    //         "name" => "JohnDavid",
    //         "email" => "john12345@gmail.com",
    //         "mobile_no" => 9815671111,
    //         "dob" => "2007/07/07"
    //     ]);
    //     $userData = [
    //         "mobile_no" => 9815671111,
    //     ];
    //     $this->json('POST', 'api/v1/send-otp', $userData, ['Accept' => 'application/json'])
    //         ->assertStatus(500)
    //         ->assertJson(['message' => "OTP not generated"]);
    // }
}
