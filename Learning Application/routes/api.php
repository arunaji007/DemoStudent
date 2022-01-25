<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\PracticeController;
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
    Route::get(
        '/v1/boards',
        [StudentController::class, 'getBoards']
    );
    // Route::post('/v1/boards', [StudentController::class, 'createBoard']);
    // Route::delete('/v1/boards/{board_id}', [StudentController::class, 'deleteBoard']);
    Route::get(
        '/v1/boards/{board_id}/grades',
        [StudentController::class, 'getGrades']
    );
    // Route::post('/v1/boards/{board_id}/grades',   [StudentController::class, 'createGrade']);
    // Route::delete('/v1/boards/grades/{grade_id}', [StudentController::class, 'deleteGrade']);
    Route::get(
        '/v1/user/myself',
        [AuthController::class, 'getUser']
    );
    Route::put(
        '/v1/users/myself',
        [StudentController::class, 'updateUser']
    );
    // Route::post('/v1/grades/{grade_id}/subjects', [SubjectController::class, 'createSubject']);
    Route::get(
        '/v1/users/myself/subjects',
        [StudentController::class, 'getSubjects']
    );
    // Route::delete('/v1/grades/{grade_id}/subjects/{subject_id}', [StudentController::class, 'deleteGrade']);
    Route::get(
        '/v1/subjects/{subject_id}/chapters',
        [StudentController::class, 'getChapters']
    );
    Route::get(
        '/v1/chapters/{chapter_id}/contents',
        [StudentController::class, 'getContents']
    );
    Route::get(
        '/v1/contents/{content_id}/view',
        [StudentController::class, 'viewContent']
    );
    Route::put(
        '/v1/contents/{content_id}/comment',
        [StudentController::class, 'postReviews']
    );
    Route::get(
        '/v1/users/myself/contents',
        [StudentController::class, 'getUserContents']
    );
    Route::get(
        '/v1/chapters/{chapter_id}/exercises',
        [PracticeController::class, 'getExercises']
    );
    Route::get(
        '/v1/exercises/{exercise_id}/questions',
        [PracticeController::class, 'getQuestions']
    );
    Route::post(
        '/v1/exercise/{exercise_id}/attempts',
        [PracticeController::class, 'createAttempt']
    );
    Route::put(
        '/v1/exercises/{exercise_id}/attempts/{attempt_id}',
        [PracticeController::class, 'updateAttempt']
    );
    Route::get(
        '/v1/chapters/{chapter_id}/attempts',
        [PracticeController::class, 'getAttempts']
    );
    Route::delete(
        '/v1/exercises/{exercise_id}/attempts/{attempt_id}',
        [PracticeController::class, 'deleteAttempt']
    );
    Route::put(
        '/v1/exercises/{exercise_id}/attempts/{attempt_id}/attempt-summaries/',
        [PracticeController::class, 'updateSummary']
    );
    Route::get(
        '/v1/exercises/{exercise_id}/attempts/{attempt_id}/attempt-summaries/',
        [PracticeController::class, 'getAttemptSummary']
    );
    Route::get(
        '/v1/exercises/{exercise_id}/attempt-summaries/',
        [PracticeController::class, 'getExercisePercentage']
    );
     
});

Route::post('/v1/signup', [AuthController::class, 'signup']);
Route::post('/v1/login', [AuthController::class, 'login']);

// 
Route::any('{any}', function () {
    return response()->json([
        'status'    => false,
        'message'   => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
