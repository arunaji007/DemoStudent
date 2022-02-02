<?php

namespace App\Http\Controllers;

use Tzsk\Otp\Facades\Otp;

use App\Models\User;
use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
//...
use Illuminate\Support\Facades\Validator;
use stdClass;

class AuthController extends Controller
{
    //
    public function signup(Request $request)
    {
        $beforeDate = now()->subYears(5)->toDateString();
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'alpha', 'min:5'],
            'mobile_no' => ['required', 'digits:10'],
            'email' => ['required', 'email:rfc,dns'],
            'dob' => ['required', 'date', 'before:' . $beforeDate],
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $validatedData  = $validator->validated();

        $userexists = User::where('mobile_no', $validatedData['mobile_no'])->first();
        if (!empty($userexists))
            return response(['message' => "MobileNumber already registered"], status: Response::HTTP_CONFLICT);

        User::create($validatedData);
        $success = AuthController::sendOtp($request);
        return response([
            'message' => "User Registered Successfully"
        ], status: Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mobile_no' => ['required', 'digits:10'],
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $userexists = User::where('mobile_no', $validator->validated(['mobile_no']))->first();

        if (empty($userexists))
            return response(['message' => "Given MobileNumber not found"], status: Response::HTTP_NOT_FOUND);

        AuthController::sendOtp($request);
        return response([
            'message' => "OTP sent"
        ], status: Response::HTTP_OK);
    }

    public static function sendOtp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mobile_no' => ['required', 'digits:10'],
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $otp = Otp::expiry(1)->generate(env('OTP_SECRET'));
        Log::info("otp = " . $otp);
        if ($otp) {
            return response([
                'message' => "OTP sent"
            ], status: Response::HTTP_OK);
        } else {
            return response([
                'message' => "OTP not generated"
            ], status: 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'digits:6'],
            'mobile_no' => ['required', 'digits:10']
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $validatedData = $validator->validated();

        $valid = Otp::check($validatedData['otp'], env('OTP_SECRET'));
        Log::info($valid);
        if (!$valid)
            return response(['message' => "Invalid Otp"], status: Response::HTTP_UNAUTHORIZED);
        //doubt
        $user = User::where('mobile_no', $validatedData['mobile_no'])->first();
        //doubt
        $userid = User::where('mobile_no', $validatedData['mobile_no'])->first('id');
        $tokenString = JWTAuth::fromUser($userid);

        return response(['message' => "OtpVerified", "token" => $tokenString, 'user' => $user], status: Response::HTTP_OK);
    }
}
