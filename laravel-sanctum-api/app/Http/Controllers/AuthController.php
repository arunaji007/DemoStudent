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
        $user = [
            'name' => $request->input(key: 'name'),
            'email' => $request->input(key: 'email'),
            'dob' =>  $request->input(key: 'dob'),
            'mobile_no' => $request->input(key: 'mobile_no')
        ];
        User::create($user);
        return response([
            'message' => "Successfully registered"
        ], status: Response::HTTP_OK);
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

        $userData = User::first('mobile_no', $request->mobile_no)->get('email');

        $otp = Otp::expiry(1)->generate($request->mobile_no);
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

        $enterOtp = $request->otp;
        $userData = User::first('mobile_no', '=',         $request->mobile_no)->get(['otp', 'expiry_seconds', 'updated_at']);

        $userOtp = $userData[0]->otp;
        //$expiry = $userData[0]->expiry_seconds;
        if ($userOtp != $enterOtp)
            return response(['message' => "Invalid otp or Otp expired"], status: Response::HTTP_UNAUTHORIZED);

        $userid = User::where('mobile_no', '=',         $request->mobile_no)->get(['id'])[0]->id;
        $user = User::find($userid);
        $tokenString = JWTAuth::fromUser($user);
        $user = User::where('mobile_no', '=',         $request->mobile_no)->get();
        return response(['message' => "OtpVerified", "token" => $tokenString, 'user' => $user], status: Response::HTTP_OK);
    }

    public function send(Request $request)
    {
        $otp = Otp::expiry(1)->generate($request->mobile_no);
        return "otp sent" . $otp;
    }
    public function verify(Request $request)
    {
        $valid = Otp::match($request->otp, $request->mobile_no);
        if ($valid == 1)
            return "otp verified" . $valid;
        else
            return "otp expired";
    }
}
