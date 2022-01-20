<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;
use App\Models\Subject;
use App\Models\Chapter;
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
        Log::info($boards);
        if (!$boards)
            return response(['message' => 'Error in getting boards'], status: Response::HTTP_CONFLICT);
        return response(["boards" => $boards], status: Response::HTTP_OK);
    }

    public function createBoard(Request $request)
    {
        $this->validate($request, [
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
        $grades = Grade::where('boardId', ($request->board_id))->get();
        Log::info(gettype($request->board_id));
        if (!$grades)
            return response(['message' => 'Error in getting Grade'], status: Response::HTTP_CONFLICT);
        return response(["grades" => $grades], status: Response::HTTP_OK);
    }

    public function createGrade(Request $request)
    {
        $this->validate($request, [
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
            return response(['message' => 'Error in deleting grade'], status: Response::HTTP_CONFLICT);
        $grades->delete();
        return response(['message' => 'deleted grade'], status: Response::HTTP_OK);
    }

    public function updateUser(Request $request)
    {
        if ($request->input('mobile_no')) {
            return response(["message" => 'Mobile cannot be Updated'], status: Response::HTTP_UNAUTHORIZED);
        }

        if (!$request->getContent())
            return response(["message" => 'Null Cannot be updated'], status: Response::HTTP_CONFLICT);

        $keys = [
            "name" => "name",
            "email" => "email",
            "dob" => "dob",
            "board_id" => "boardId",
            "grade_id" => "gradeId"
        ];
        $itemss = $request->collect();
        $array = [];

        foreach ($itemss as $key => $value) {
            $array[$keys[$key]] = ($value);
        }

        $user = User::where('mobile_no', $this->user['mobile_no']);
        $user->update($array);
        $user = User::where('mobile_no', $this->user['mobile_no'])->first();
        return response(["message" => 'Updated', "user" => $user], status: Response::HTTP_OK);
    }

    public function getSubjects(Request $request)
    {
        $subject = null;
        if ($request->subject) {
            $subject = 1;
        }
        if ($subject == null) {
            $subjects = Subject::where('gradeId', $this->user['gradeId'])->get(['id', 'name']);
            if (!$subjects) {
                return response(["message" => 'Error in getting subjects'], status: Response::HTTP_CONFLICT);
            }
            return response(['message' => 'Subjects Data', 'subjects' => $subjects], status: Response::HTTP_OK);
        }


        $subjects =
            Subject::where('gradeId', $this->user['gradeId'])->where('name', 'regexp', "^" . $request->subject)->get(['id', 'name']);

        return response(['message' => 'Subjects Data', 'subjects' => $subjects], status: Response::HTTP_OK);
    }

    public function getchapters(Request $request)
    {
        Log::info($request->all());
        $chapter = null;
        if ($request->chapter) {
            $chapter = 1;
        }
        if ($chapter == null) {
            $chapters = Chapter::where('subjectId', $request->subject_id)->get(['id', 'name']);
            if (!$chapters) {
                return response(["message" => 'Error in getting chapters'], status: Response::HTTP_CONFLICT);
            }
            return response(['message' => 'Chapters Data', 'chapters' => $chapters], status: Response::HTTP_OK);
        }


        $chapters =
            Subject::where('subjectId', $request->subjectId)->where('name', 'regexp', "^" . $request->chapter)->get(['id', 'name']);

        return response(['message' => 'Chapters Data', 'chapters' => $chapters], status: Response::HTTP_OK);
    }
}
