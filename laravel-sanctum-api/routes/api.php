<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::resource('products', ProductController::class);

// Route::get('/products/search/{name}',[ProductController::class,'search']);

Route::post('/v1/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/v1/verify-otp', [AuthController::class, 'verifyOtp']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/v1/boards', [StudentController::class, 'getBoards']);
    Route::get('/v1/boards/{boardId}/grades', [StudentController::class, 'getGrades']);
    Route::put('/v1/users/myself', [StudentController::class, 'updateUser']);
});

Route::post('/v1/signup', [AuthController::class, 'signup']);
Route::post('/v1/login', [AuthController::class, 'login']);

// Route::get('/v1/get_user', [AuthController::class, 'get_user']);
