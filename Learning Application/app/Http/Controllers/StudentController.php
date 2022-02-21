<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
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
use App\Http\Controllers\Route;

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
        return response(["boards" => $boards], status: Response::HTTP_OK);
    }

    public function getGrades(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "board_id" => "required | integer | exists:boards,id",
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $grades = Grade::where('board_id', $validator->validated('board_id'))->get();
        return response(["grades" => $grades], status: Response::HTTP_OK);
    }

    public function getUser(Request $request)
    {
        return response(
            ["message" => "user data", "user" => $this->user],
            status: Response::HTTP_OK
        );
    }

    public function updateUser(Request $request)
    {
        $beforeDate = now()->subYears(5)->toDateString();
        $validator = Validator::make($request->all(), [
            'name' => ['alpha', 'min:5'],
            'email' => ['email:rfc,dns'],
            'dob' => ['date', 'before:' . $beforeDate],
            "board_id" => "required | integer | exists:boards,id",
            "grade_id" => "required | integer | exists:grades,id",
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $validatedData  = $validator->validated();

        $user =  User::find($this->user['id']);
        $user->update($validatedData);
        #$user  = $this->user->update($data);// not updating foreign keys

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

        $review_content = Subject::withCount(
            ['reviews' => function ($q) {
                $q->where('user_id', $this->user['id']);
            }]
        )->whereIn('id', $arr)->get();

        $attempt_content =
            Subject::withCount(['attempts' => function ($q) {
                $q->where('attempts.user_id', $this->user['id']);
            }])->whereIn('id', $arr)->distinct('exercise_id')->get();
        for ($i = 0; $i < count($review_content); $i++) {
            if ($subject_content[$i]['contents_count'] > 0) {
                $subject_content[$i]['subjects_percentage'] =
                    (int)$review_content[$i]['reviews_count'] / (int)$subject_content[$i]['contents_count'];
            } else {
                $subject_content[$i]['subjects_percentage'] = 0;
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
            $subjects = StudentController::subjecthelper($limit, $offset);
            return response(['subjects' => $subjects], status: Response::HTTP_OK);
        }
        $subjects =
            Subject::where('grade_id', $this->user['grade_id'])->where('name', 'LIKE', ('%' . $request->subject . '%'))->limit($limit)->offset($offset)->get(['id', 'name']);

        return response(['subjects' => $subjects], status: Response::HTTP_OK);
    }

    public function getChapters(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "subject_id" => "required | integer | exists:subjects,id",
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;
        if (!$request->chapter) {
            $chapters = Chapter::where('subject_id', $validator->validated('subject_id'))->limit($limit)->offset($offset)->get(['id', 'name', 'chapter_id']);
            return response(['chapters' => $chapters], status: Response::HTTP_OK);
        }

        $chapters =
            Chapter::where('subject_id', $validator->validated('subject_id'))->where('name', 'LIKE', '%' . $request->chapter . '%')->limit($limit)->offset($offset)->get(['id', 'name', 'chapter_id']);
        return response(['chapters' => $chapters], status: Response::HTTP_OK);
    }

    public function getUserContents(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;

        $contents = Review::join('contents', 'reviews.content_id', 'contents.id')->where('user_id', $this->user['id'])->orderBy('reviews.updated_at', 'desc')->limit($limit)->offset($offset)->get(['contents.id', 'contents.name', 'path', 'reviews.updated_at']);

        return response(["contents" => $contents], status: Response::HTTP_OK);
    }

    public function getContents(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "chapter_id" => "required | integer | exists:chapters,id",
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;

        if (!$request->content) { //query param
            $contents = Content::where('chapter_id', $validator->validated('chapter_id'))->limit($limit)->offset($offset)->get(['id', 'name', 'path']);
            return response(['contents' => $contents], status: Response::HTTP_OK);
        }

        $contents = Content::where('chapter_id', $validator->validated('chapter_id'))->where('name', 'LIKE', ('%' . $request->content . '%'))->limit($limit)->offset($offset)->get(['id', 'name', 'path']);
        return response(['contents' => $contents], status: Response::HTTP_OK);
    }

    public function viewContent(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "content_id" => "required | integer | exists:contents,id",
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $updated_date = Carbon::now();
        Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->update(['updated_at' => $updated_date]);
        $reviews = Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->get(['id', 'notes', 'like', 'lastRead', 'lastWatched'])->first();
        return response(['reviews' => $reviews], status: Response::HTTP_OK);
    }

    public function updateReviews(Request $request) //put
    {
        $validator = Validator::make(array_merge($request->route()->parameters(), $request->all()), [
            'notes' => 'alpha_num',
            'like' => 'integer',
            "content_id" => "required | integer | exists:contents,id",
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->update($validator->validated());
        $review_update = Review::where('content_id', $request->content_id)->where('user_id', $this->user['id'])->first();
        return response(['reviews' => $review_update], status: Response::HTTP_CREATED);
    }
}
