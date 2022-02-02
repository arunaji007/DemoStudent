<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function test_user_required_fields_for_registration()
    {

        $this->json('POST', 'api/v1/signup', ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "dob" => ["The dob field is required."],
                    "mobile_no" => ["The mobile no field is required."],
                ]
            ]);
    }

    public function test_user_name_is_alpha()
    {

        $userData = [
            "name" => "JowhnDoere1",
            "email" => "doee2e2@gmail.com",
            "mobile_no" => "9015678910",
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name must only contain letters."
                    ],
                ]
            ]);
    }

    public function test_user_name_min_5()
    {

        $userData = [
            "name" => "Jow",
            "email" => "doee2e2@gmail.com",
            "mobile_no" => "9015678910",
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name must be at least 5 characters."
                    ],
                ]
            ]);
    }
    public function test_user_mobile_no_digits_10()
    {

        $userData = [
            "name" => "Jow",
            "email" => "doee2e2@gmail.com",
            "mobile_no" => "901567890",
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "mobile_no" => [
                        "The mobile no must be 10 digits."
                    ],
                ]
            ]);
    }

    public function test_user_email_is_rfc_dns()
    {

        $userData = [
            "name" => "Jow",
            "email" => "doee2e2@gmail",
            "mobile_no" => "901567890",
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email must be a valid email address."
                    ],
                ]
            ]);
    }
    public function test_user_dob_is_in_format()
    {

        $userData = [
            "name" => "Jow",
            "email" => "'doee2e2@gmail'",
            "mobile_no" => "901567890",
            "dob" => "2007/07/0100"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "dob" => [
                        "The dob is not a valid date."
                    ],
                ]
            ]);
    }
    public function test_user_successful_registration()
    {
        $userData = [
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson(['message' => "User Registered Successfully"]);
        $this->assertDatabaseHas('users', ['mobile_no' => 9815671112, 'email' => "john12345@gmail.com",]);
    }

    public function test_user_already_user_exists()
    {
        User::factory()->create([
            "name" => "JohnDavid",
            "email" => "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ]);
        $userData = [
            "name" => "JowhnwwDoere",
            "email" =>
            "john12345@gmail.com",
            "mobile_no" => 9815671112,
            "dob" => "2007/07/07"
        ];
        $this->json('POST', 'api/v1/signup', $userData, ['Accept' => 'application/json'])
            ->assertStatus(409)
            ->assertJson(['message' => "MobileNumber already registered"]);
    }

}
