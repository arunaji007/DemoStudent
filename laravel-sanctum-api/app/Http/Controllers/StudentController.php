<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    //
    protected $user;
    public function __construct()
    {
        $this->user =
            JWTAuth::parseToken()->authenticate();
    }

    public function getBoards()
    {
        $boards = Board::get();

        return response(["boards" => $boards], status: Response::HTTP_OK);
    }
    public function getGrades(Request $request)
    {
        $request->validate([
            "boardId" => "required",
        ]);
        $grades = Grade::where('boardId', $request->boardId)->get();
        Log::info($this->user);
        return response(["grades" => $grades], status: Response::HTTP_OK);
    }
    public function updateUser(Request $request)
    {
        $user = User::where('mobile_no', $this->user['mobile_no']);
        $user->update($request->all());
        return response(["message" => 'Updated'], status: Response::HTTP_OK);
    }
}
