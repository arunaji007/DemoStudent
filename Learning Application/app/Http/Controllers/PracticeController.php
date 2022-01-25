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

class PracticeController extends Controller
{
    //
    protected $user;

    public function __construct()
    {
        $this->user =
            JWTAuth::parseToken()->authenticate();
    }

    public function getExercises(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 4;
        if (!$request->exercise) {
            $exercises = Exercise::where('chapter_id', $request->chapter_id)->limit($limit)->offset($offset)->get(['id', 'name', 'duration', 'noOfQuestions']);
            if (!$exercises) {
                return response(
                    ["message" => 'Exercises not Available'],
                    status: Response::HTTP_NOT_FOUND
                );
            }
            return response(['message' => 'exercises Data', 'exercises' => $exercises], status: Response::HTTP_OK);
        }

        $exercises =
            Exercise::where('chapter_id', $request->chapter_id)->where('name', 'LIKE', '%' . $request->exercise . '%')->get(['id', 'name', 'duration', 'noOfQuestions']);
        Log::info($request->exercise);
        return response(['message' => 'exercises data', 'exercises' => $exercises], status: Response::HTTP_OK);
    }

    public function getQuestions(Request $request)
    {

        $questions = Question::with(['answers' => function ($query) {
            $query->inRandomOrder();
        }])->inRandomOrder()->paginate(1);
        Log::info($questions);
        if (!$questions) {
            return response(
                ["message" => 'Questions not Available'],
                status: Response::HTTP_NOT_FOUND
            );
        }
        return response(["questions" => $questions], status: Response::HTTP_OK);
    }

    public function updateAttempt(Request $request)
    {
        if ($request->getContent()) {
            $attempts = json_decode($request->getContent(), true);
            $attempt = Attempt::where('id', $request->attempt_id)->update($attempts);
            if ($attempt) {
                $attempt_update =  Attempt::where('id', $request->attempt_id)->first();
                return response(['message' => 'Attempts updated', 'attempts' => $attempt_update], status: Response::HTTP_OK);
            }
            return response(['message' => 'Attempts Not Updated'], status: Response::HTTP_NOT_FOUND);
        }
    }

    public function deleteAttempt(Request $request)
    {
        $attempts = Attempt::where('id', $request->attempt_id)->get();
        Log::info($attempts);

        if ($attempts->isEmpty()) {
            return response(['message' => 'Attempt not available'], status: Response::HTTP_CONFLICT);
        }
        $attempts = Attempt::where('id', $request->attempt_id);
        $attempt_summary = AttemptSummary::where('attempt_id', $request->attempt_id);
        $attempt_summary->delete();
        $attempts->delete();
        return response(['message' => 'deleted attempt'], status: Response::HTTP_OK);
    }
    public function createAttempt(Request $request)
    {
        if (!$request->getContent()) {
            $data = [
                "user_id" => $this->user['id'],
                "exercise_id" => $request->exercise_id,
                "score" => 0,
                "duration" => '00:00:00',
            ];
            $attempt = Attempt::create($data);
            if ($attempt) {
                return response(["message" => "attempt created", "attempt_id" => $attempt['id']], status: Response::HTTP_OK);
            }
        }
        return response(["message" => "attempt NOT created"], status: Response::HTTP_CONFLICT);
    }

    public function getAttempts(Request $request)
    {

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

        // ->groupBy('exercise_id')
        // ->get([
        //     'exercise_id',
        //     DB::raw('MAX(score) as high_score'),
        //     DB::raw('COUNT(exercise_id) as count')
        // ]);
        #$attempt = Attempt::where();
        if ($attempt) {
            return response(["message" => "attempt data",  "attempts" => $attempt], status: Response::HTTP_CONFLICT);
        }
    }

    public function updateSummary(Request $request)
    {
        $question  = AttemptSummary::where([['attempt_id', $request->attempt_id], ['question_id', $request->question_id]])->get();
        Log::info($request->question_id);
        Log::info($question);
        if ($question->isEmpty()) {
            $summary =
                json_decode($request->getContent(), true);
            $summary['attempt_id'] = $request->attempt_id;
            $attempt_summary = AttemptSummary::create($summary);
            if ($attempt_summary) {
                return response(["message" => "attempt summary created"], status: Response::HTTP_OK);
            }
        } else {
            if ($request->attempt_id) {
                $summary = json_decode($request->getContent(), true);
                $summary = AttemptSummary::where([
                    ['attempt_id', $request->attempt_id],
                    ['question_id', $summary['question_id']]
                ])->update($summary);
                if ($summary) {
                    $summary_update =  AttemptSummary::where('attempt_id', $request->attempt_id)->first();
                    return response(['message' => 'Summary updated', 'attempts_summary' => $summary_update], status: Response::HTTP_OK);
                }
            }

            return response(['message' => 'Attempts Summary Not Updated'], status: Response::HTTP_NOT_FOUND);
        }
    }

    public function getAttemptSummary(Request $request)
    {
        try {
            $attempt_summary = Attempt::with('attempt_summaries')->where('id', $request->attempt_id)->first();
            if (!$attempt_summary) {
                return response(["message" => "Attempt not available"], status: Response::HTTP_NOT_FOUND);
            }
            $exercise_question = Exercise::where('id', $attempt_summary['exercise_id'])->first('noOfQuestions');
            $time = date_parse($attempt_summary['duration']);
            $seconds = $time['hour'] * 3600 + $time['minute'] * 60 + $time['second'];
            $attempt_summary['answer_per_second'] = $exercise_question['noOfQuestions'] / (int) $seconds;

            $attemptAns = AttemptSummary::join('attempts', 'id', 'attempt_summaries.attempt_id')->where('answer_type', 1)->count('id');
            $attemptWrong = AttemptSummary::join('attempts', 'id', 'attempt_summaries.attempt_id')->where('answer_type', 2)->count('id');
            $attemptUnans = AttemptSummary::join('attempts', 'id', 'attempt_summaries.attempt_id')->where('answer_type', 3)->count('id');
            $attempteval = AttemptSummary::join('attempts', 'id', 'attempt_summaries.attempt_id')->where('answer_type', 4)->count('id');
            $attemptacc = AttemptSummary::join('attempts', 'id', 'attempt_summaries.attempt_id')->whereIn('answer_type', [1, 2, 4])->count('id');


            $attempt_summary['correct_answered'] = $attemptAns;
            $attempt_summary['wrong_answered'] = $attemptWrong;
            $attempt_summary['un_answered'] = $attemptUnans;
            $attempt_summary['evalauting'] = $attempteval;
            $attempt_summary['accuracy']  =
                $attemptAns / $attemptacc;
            return response(["message" => "Attempt Data", "attempt_summary" => $attempt_summary], status: Response::HTTP_OK);
        } catch (Exception $e) {
            return Response(["error" => $e,], stauts: Response::HTTP_CONFLICT);
        }
    }
}
