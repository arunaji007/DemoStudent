<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Answer;
use App\Models\AttemptSummary;  
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Review;
use App\Models\Content;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PracticeController extends Controller
{
    //
    public function __construct()
    {
        $this->user =
            JWTAuth::parseToken()->authenticate();
    }

    public function getExercises(Request $request)
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
        if (!$request->exercise) {
            $exercises = Exercise::where('chapter_id', $validator->validated('chapter_id'))->limit($limit)->offset($offset)->get(['id', 'name', 'duration', 'noOfQuestions']);
            return response(['exercises' => $exercises], status: Response::HTTP_OK);
        }

        $exercises =
            Exercise::where('chapter_id', $validator->validated('chapter_id'))->where('name', 'LIKE', '%' . $request->exercise . '%')->get(['id', 'name', 'duration', 'noOfQuestions']);
        Log::info($request->exercise);
        return response(['exercises' => $exercises], status: Response::HTTP_OK);
    }

    public function getQuestions(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "exercise_id" => "required | integer | exists:exercises,id",
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $questions = Question::with(['answers' => function ($query) {
            $query->inRandomOrder();
        }])->where('exercise_id', $validator->validated('exercise_id'))->inRandomOrder()->paginate(1);
        return response(["questions" => $questions], status: Response::HTTP_OK);
    }

    public function updateAttempt(Request $request)
    {
        $validator = Validator::make(
            array_merge($request->all(), $request->route()->parameters()),
            [
                "exercise_id" => "required | integer | exists:exercises,id",
                "attempt_id" =>  "required | integer | exists:attempts,id",
                "score" => ["integer", "min:0", "max:100"],
                "duration" => ["date_format:H:i:s"]
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        Log::info($validator->validated()['attempt_id']);
        $attempt = Attempt::where('id', $validator->validated()['attempt_id'])->update($request->all());
        $attempt_update =  Attempt::where('id', $validator->validated()['attempt_id'])->get();
        return response(['attempts' => $attempt_update], status: Response::HTTP_OK);
    }

    public function createAttempt(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "exercise_id"
                => "required | integer | exists:exercises,id",
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $data = [
            "user_id" => $this->user['id'],
            "exercise_id" => $request->exercise_id,
            "score" => 0,
            "duration" => '00:00:00',
        ];
        $attempt = Attempt::create($data);
        return response(["attempt" => $attempt], status: Response::HTTP_OK);
    }

    public function deleteAttempt(Request $request)
    {

        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "exercise_id"
                => "required | integer | exists:exercises,id",
                "attempt_id" => "required | integer | exists:attempts,id",
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $attempts = Attempt::where('id',  $validator->validated('attempt_id'));
        $attempt_summary = AttemptSummary::where('attempt_id', $validator->validated('attempt_id'));
        $attempt_summary->delete();
        $attempts->delete();
        return response(['message' => 'deleted attempt'], status: Response::HTTP_OK);
    }

    public function getAttempts(Request $request)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                "chapter_id"
                => "required | integer | exists:chapters,id",
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }
        $attempt = DB::table('attempts')
            ->join('exercises', 'attempts.exercise_id', 'exercises.id')
            ->where([['user_id', $this->user['id']], ['exercises.chapter_id', $request->chapter_id]])
            ->groupBy('attempts.exercise_id')
            ->get([
                'exercise_id',
                'exercises.chapter_id',
                DB::raw('MAX(score) as high_score'),
                DB::raw('COUNT(exercise_id) as attempt_count')
            ]);
        if ($attempt) {
            return response(["attempts" => $attempt], status: Response::HTTP_CONFLICT);
        }
    }

    public function updateSummary(Request $request)
    {
        $validator = Validator::make(
            array_merge($request->all(), $request->route()->parameters()),
            [
                "exercise_id" => "required | integer | exists:exercises,id",
                "attempt_id" =>  "required | integer | exists:attempts,id",
                "question_id" =>
                "required | integer | exists:questions,id",
                "answer_id"
                => "required | integer | exists:answers,id",
                "mark" => "integer|min:1|max:5",
                "answer_type" => "integer|min:1|max:3",
                "answer" => "alpha"
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $summaries = $validator->validated();
        $data = Arr::except($summaries, ['exercise_id']); //update all except exercise id
        AttemptSummary::upsert($data, ['attempt_id', 'question_id'], array_keys($data));
        $summary_update =  AttemptSummary::where('attempt_id', $request->attempt_id)->get();
        return response(['attempts_summary' => $summary_update], status: Response::HTTP_OK);
    }

    public function getAttemptSummary(Request $request)
    {
        $validator = Validator::make(
            ($request->route()->parameters()),
            [
                "exercise_id" => "required | integer | exists:exercises,id",
                "attempt_id" =>  "required | integer | exists:attempts,id",
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], status: Response::HTTP_BAD_REQUEST);
        }

        $attempt_summary = Attempt::with('attempt_summaries')->where('id', $validator->validated()['attempt_id'])->first();
        $attempt = AttemptSummary::groupBy('answer_type')->where('attempt_id', $request->attempt_id)->selectRAW('count(*) as total,answer_type')->orderBy('answer_type')->get();
        $sum = 0;
        $ans = 0;
        for ($i = 0; $i < count($attempt); $i++) {
            if ($i == 0) {
                $attempt_summary['correct_answered'] = $attempt[$i]['answer_type'];
                $sum
                    += $attempt[$i]['answer_type'];
                $ans += $attempt[$i]['answer_type'];
            }
            if ($i == 1) {
                $attempt_summary['wrong_answered'] = $attempt[$i]['answer_type'];
                $sum
                    += $attempt[$i]['answer_type'];
                $ans += $attempt[$i]['answer_type'];
            }
            if ($i == 2) {
                $attempt_summary['un_answered'] = $attempt[$i]['answer_type'];
                $sum
                    += $attempt[$i]['answer_type'];
            }
            if ($i == 3) {
                $attempt_summary['evaluating'] = $attempt[$i]['answer_type'];
            }
        }
        $time = date_parse($attempt_summary['duration']);
        $seconds = $time['hour'] * 3600 + $time['minute'] * 60 + $time['second'];
        $attempt_summary['answer_per_second'] = $ans / (int) $seconds;
        if ($attempt_summary['correct_answered']) {
            $attempt_summary['accuracy']  =
                $attempt_summary['correct_answered'] / $sum;
        }
        return response(["attempt_summary" => $attempt_summary], status: Response::HTTP_OK);
    }
}
