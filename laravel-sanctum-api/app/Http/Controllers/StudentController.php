<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Review;
use App\Models\Content;
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
            return response(['message' => 'Boards Not Found'], status: Response::HTTP_NOT_FOUND);
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
        $grades = Grade::where('board_id', ($request->board_id))->get();
        Log::info(gettype($request->board_id));
        if (!$grades)
            return response(['message' => 'Grades Not Found'], status: Response::HTTP_NOT_FOUND);
        return response(["grades" => $grades], status: Response::HTTP_OK);
    }

    public function createGrade(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $gradeexists = Grade::where('name', $request->name)->where('board_id', $request->board_id)->first();
        if ($gradeexists)
            return response(['message' => 'Grade already exists'], status: Response::HTTP_CONFLICT);

        $grade = [
            'name' => $request->input(key: 'name'),
            'board_id' => $request->board_id
        ];
        $grades = Grade::create($grade);
        if ($grades)
            return response(['message' => 'created new grade'], status: Response::HTTP_OK);

        return response(['message' => 'Error in creating new grade'], status: Response::HTTP_CONFLICT);
    }

    public function deleteGrade(Request $request)
    {
        $grades = Grade::where('id', $request->grade_id)->where('board_id', $request->board_id);

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
            "board_id" => "board_id",
            "grade_id" => "grade_id"
        ];
        $itemss = $request->collect();
        $array = [];

        foreach ($itemss as $key => $value) {
            $array[$keys[$key]] = ($value);
        }

        $user = User::where('mobile_no', $this->user['mobile_no']);
        if (!$user) {
            return response(
                ["message" => 'User not Available'],
                status: Response::HTTP_NOT_FOUND
            );
        }
        $user->update($array);
        $user = User::where('mobile_no', $this->user['mobile_no'])->first();
        return response(["message" => 'Updated', "user" => $user], status: Response::HTTP_OK);
    }
    public function subjecthelper($limit, $offset)
    {
        $subject_content = Subject::withCount('contents')->where('grade_id', $this->user['grade_id'])->limit($limit)->offset($offset)->get();
        $subject_exercise_content = Subject::withCount('exercises')->where('grade_id', $this->user['grade_id'])->limit($limit)->offset($offset)->get();
        $arr = [];
        for ($i = 0; $i < count($subject_content); $i++) {
            $arr[$i] = $subject_content[$i]['id'];
        }

        $review_content = Subject::withCount('reviews')->whereIn('id', $arr)->get();
        $attempt_content =
            Subject::withCount('attempts')->whereIn('id', $arr)->get();
        for ($i = 0; $i < count($review_content); $i++) {
            if ($subject_content[$i]['contents_count'] > 0) {
                $subject_content[$i]['percentage'] =
                    (int)$review_content[$i]['reviews_count'] / (int)$subject_content[$i]['contents_count'];
            } else {
                $subject_content[$i]['percentage'] = 0;
            }
        }
        for ($i = 0; $i < count($attempt_content); $i++) {
            if ($subject_exercise_content[$i]['exercises_count'] > 0) {
                $subject_content[$i]['exercise_percentage'] =
                    (int)$attempt_content[$i]['attempts_count'] / (int)$subject_exercise_content[$i]['exercises_count'];
            } else {
                $subject_content[$i]['exercise_percentage'] = 0;
            }
        }

        return $subject_content;
    }
    public function getSubjects(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;
        if (!$request->subject) {
            $subjects = StudentController::subjecthelper($limit, $offset, $request);

            Log::info($subjects);
            if (!$subjects) {
                return response(
                    ["message" => 'Subjects not Available'],
                    status: Response::HTTP_NOT_FOUND
                );
            }
            return response(['message' => 'Subjects Data', 'subjects' => $subjects], status: Response::HTTP_OK);
        }
        $subjects =
            Subject::where('grade_id', $this->user['grade_id'])->where('name', 'LIKE', ('%' . $request->subject . '%'))->limit($limit)->offset($offset)->get(['id', 'name']);
        Log::info($subjects);
        return response(['message' => 'Subjects Data', 'subjects' => $subjects], status: Response::HTTP_OK);
    }

    public function getChapters(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;
        if (!$request->chapter) {
            $chapters = Chapter::where('subject_id', $request->subject_id)->limit($limit)->offset($offset)->get(['id', 'name']);
            if (!$chapters) {
                return response(
                    ["message" => 'Chapters Not Available'],
                    status: Response::HTTP_NOT_FOUND
                );
            }
            return response(['message' => 'Chapters Data', 'chapters' => $chapters], status: Response::HTTP_OK);
        }


        $chapters =
            Chapter::where('subject_id', $request->subject_id)->where('name', 'LIKE', '%' . $request->chapter . '%')->limit($limit)->offset($offset)->get(['id', 'name']);
        Log::info($chapters);
        return response(['message' => 'Chapters Data', 'chapters' => $chapters], status: Response::HTTP_OK);
    }

    public function getUserContents(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;

        $contents = Review::join('contents', 'reviews.content_id', 'contents.id')->where('user_id', $this->user['id'])->orderBy('reviews.updated_at', 'desc')->limit($limit)->offset($offset)->get(['contents.id', 'contents.name', 'path', 'reviews.updated_at']);
        if ($contents) {
            return response(
                ["message" => 'Contents not Available', 'contents' => $contents],
                status: Response::HTTP_NOT_FOUND
            );
        }
        return response(['message' => 'No Contents'], status: Response::HTTP_CONFLICT);
    }

    public function getContents(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;

        if (!$request->content) {
            $contents = Content::where('chapter_id', $request->chapter_id)->limit($limit)->offset($offset)->get(['id', 'name', 'path']);
            if (!$contents) {
                return response(
                    ["message" => 'Contents not Available'],
                    status: Response::HTTP_NOT_FOUND
                );
            }
            return response(['message' => 'contents Data', 'contents' => $contents], status: Response::HTTP_OK);
        }

        $contents = Content::where('chapter_id', $request->chapter_id)->where('name', 'LIKE', ('%' . $request->content . '%'))->limit($limit)->offset($offset)->get(['id', 'name', 'path']);
        Log::info($contents);
        return response(['message' => 'contents Data', 'contents' => $contents], status: Response::HTTP_OK);
    }

    public function viewContent(Request $request)
    {
        $reviews = Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->get(['id', 'notes', 'like', 'lastRead', 'lastWatched'])->first();
        $updated_date = Carbon::now();
        Log::info(Carbon::now());
        Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->update(['updated_at' => $updated_date]);
        if (!$reviews) {
            return response(
                ["message" => 'No reviews Available '],
                status: Response::HTTP_NOT_FOUND
            );
        }

        return response(['message' => 'reviews data', 'reviews' => $reviews], status: Response::HTTP_OK);
    }

    public function postReviews(Request $request) //put
    {
        if (!$request->getContent())
            return response(["message" => 'Error in posting or upadting reviews'], status: Response::HTTP_CONFLICT);

        $reviews = json_decode($request->getContent(), true);
        $review = Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->update($reviews);
        if ($review) {
            $review_update = Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->first();
            return response(['message' => 'Reviews updated', 'reviews' => $review_update], status: Response::HTTP_OK);
        }

        return response(['message' => 'Reviews Not Updated'], status: Response::HTTP_NOT_FOUND);
    }
}
