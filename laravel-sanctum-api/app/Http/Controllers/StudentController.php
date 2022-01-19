<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        if ($boards)
            return response(['message' => 'Error in getting boards'], status: Response::HTTP_CONFLICT);
        return response(["boards" => $boards], status: Response::HTTP_OK);
    }

    public function createBoard(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'short_name' => 'required',
            'image' => 'required',
        ]);
        $board = [
            'name' => $request->input(key: 'name'),
            'shortName' => $request->input(key: 'short_name'),
            'image' =>  $request->input(key: 'image')
        ];
        $boardexists = Board::where('name', $request->name)->first();
        if ($boardexists)
            return response(['message' => 'Board already exists'], status: Response::HTTP_CONFLICT);
        $boards = Board::create($board);
        Log::info($request->all());
        if ($boards)
            return response(['message' => 'created new board'], status: Response::HTTP_OK);
        return response(['message' => 'Error in creating new board'], status: Response::HTTP_CONFLICT);
    }

    public function deleteBoard(Request $request)
    {
        $board = Board::find($request->board_id);
        if (!$board)
            return response(['message' => 'Error in deleting board'], status: Response::HTTP_CONFLICT);
        $board->delete();
        return response(['message' => 'deleted board'], status: Response::HTTP_OK);
    }

    public function getGrades(Request $request)
    {
        $grades = Grade::where('boardId', $request->board_id)->get();
        if ($grades)
            return response(['message' => 'Error in getting Grade'], status: Response::HTTP_CONFLICT);
        return response(["grades" => $grades], status: Response::HTTP_OK);
    }

    public function createGrade(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $gradeexists = Grade::where('name', $request->name)->where('boardId', $request->board_id)->first();
        if ($gradeexists)
            return response(['message' => 'Grade already exists'], status: Response::HTTP_CONFLICT);

        $grade = [
            'name' => $request->input(key: 'name'),
            'boardId' => $request->board_id
        ];
        $grades = Grade::create($grade);
        if ($grades)
            return response(['message' => 'created new grade'], status: Response::HTTP_OK);

        return response(['message' => 'Error in creating new grade'], status: Response::HTTP_CONFLICT);
    }

    public function deleteGrade(Request $request)
    {
        $grades = Grade::where('id', $request->grade_id)->where('boardId', $request->board_id);

        #Log::info($request);

        if (!$grades)
            return response(['message' => 'Error in deleting board'], status: Response::HTTP_CONFLICT);
        $grades->delete();
        return response(['message' => 'deleted grade'], status: Response::HTTP_OK);
    }

    public function updateUser(Request $request)
    {
        if ($request->input('mobile_no')) {
            return response(["message" => 'Mobile cannot be Updated'], status: Response::HTTP_UNAUTHORIZED);
        }
        
        $keys = [
            "name" => "name",
            "email" => "email",
            "dob" => "dob",
            "board_id" => "boardId",
            "grade_id" => "gradeId"
        ];
        $items = $request->all();
        $array = [];
        foreach ($items as $key => $value) {
            $array[$keys[$key]] = ($value);
        }

        $user = User::where('mobile_no', $this->user['mobile_no']);
        $user->update($array);
        return response(["message" => 'Updated', "user" => $user], status: Response::HTTP_OK);
    }
}
