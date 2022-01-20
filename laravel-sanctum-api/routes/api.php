<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

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
    Route::post('/v1/boards', [StudentController::class, 'createBoard']);
    Route::delete('/v1/boards/{board_id}', [StudentController::class, 'deleteBoard']);
    Route::get('/v1/boards/{board_id}/grades',   [StudentController::class, 'getGrades']);
    Route::post('/v1/boards/{board_id}/grades',   [StudentController::class, 'createGrade']);
    Route::delete('/v1/boards/grades/{grade_id}', [StudentController::class, 'deleteGrade']);
    Route::put('/v1/users/myself', [StudentController::class, 'updateUser']);
    // Route::post('/v1/grades/{grade_id}/subjects', [SubjectController::class, 'createSubject']);
    Route::get('/v1/users/myself/subjects', [StudentController::class, 'getSubjects']);
    // Route::delete('/v1/grades/{grade_id}/subjects/{subject_id}', [StudentController::class, 'deleteGrade']);
    Route::get('/v1/subjects/{subject_id}/chapters', [StudentController::class, 'getChapters']);
});

Route::post('/v1/signup', [AuthController::class, 'signup']);
Route::post('/v1/login', [AuthController::class, 'login']);

// Route::get('/v1/get_user', [AuthController::class, 'get_user']);
