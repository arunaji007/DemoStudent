<?php

namespace App\Http\Controllers;

use Tzsk\Otp\Facades\Otp;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Token;

use Symfony\Component\HttpFoundation\Response;
//...

class AuthController extends Controller
{
    //
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'dob' => 'required'
        ]);

        $user = [
            'name' => $request->input(key: 'name'),
            'email' => $request->input(key: 'email'),
            'dob' =>  $request->input(key: 'dob'),
            'mobile_no' => $request->input(key: 'mobile_no')
        ];

        $userexists = User::where('mobile_no', $request->mobile_no)->first();
        Log::info($userexists);
        if (!empty($userexists))
            return response(['message' => "MobileNumber or User already registered"], status: Response::HTTP_CONFLICT);

        User::create($user);
        return response([
            'message' => "Successfully registered"
        ], status: Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required',
        ]);

        $userexists = User::where('mobile_no', $request->mobile_no)->first();

        Log::info($userexists);
        if (empty($userexists))
            return response(['message' => "Given MobileNumber or User not found"], status: Response::HTTP_NOT_FOUND);

        return response(['message' => 'Successfully otp sent'], status: Response::HTTP_OK);
    }

    public function get_user(Request $request)
    {
        Log::info("message");
        $user = JWTAuth::authenticate($request->token);
        Log::info("message" . $user);
        return response([
            'message' => "Usersdata", "User" => $user
        ], status: Response::HTTP_OK);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required',
        ]);
        $otp = Otp::expiry(1)->generate($request->mobile_no); //1*30 seconds exipry
        Log::info("otp = " . $otp);
        return response([
            'message' => "OtpSent"
        ], status: Response::HTTP_OK);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required',
            'otp' => 'required',
        ]);

        $userOtp = $request->otp;
        $usermobile_no = $request->mobile_no;
        $valid = Otp::match($userOtp, $usermobile_no);

        if (!$valid)
            return response(['message' => "Invalid otp or Otp expired"], status: Response::HTTP_UNAUTHORIZED);

        $userid = User::where('mobile_no', '=',         $request->mobile_no)->first('id');

        $tokenString = JWTAuth::fromUser($userid);

        $user = User::where('mobile_no', '=',         $request->mobile_no)->first();

        return response(['message' => "OtpVerified", "token" => $tokenString, 'user' => $user], status: Response::HTTP_OK);
    }

    
}
