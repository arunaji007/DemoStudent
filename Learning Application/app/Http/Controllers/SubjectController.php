<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Board;
use App\Models\Grade;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;


class SubjectController extends Controller
{
    //
    protected $user;
    public function __construct()
    {
        $this->user =
            JWTAuth::parseToken()->authenticate();
    }
    public function createSubject(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $subject = [
            'name' => $request->name,
            'gradeId' =>  $request->grade_id
        ];

        $subjectexists = Subject::where('name', $request->name)->where('gradeId', $request->grade_id)->first();

        if ($subjectexists)
            return response(['message' => 'Subject already exists'], status: Response::HTTP_CONFLICT);

        $subjects = Subject::create($subject);

        if ($subjects)
            return response(['message' => 'created new grade'], status: Response::HTTP_OK);

        return response(['message' => 'Error in creating new subject'], status: Response::HTTP_CONFLICT);
    }
    public function getSubjects(Request $request)
    {
        $subjects = Subject::where('gradeId', ($request->grade_id))->get();
        if (count($subjects) < 0)
            return response(['message' => 'Error in getting Grade'], status: Response::HTTP_CONFLICT);
        return response(["grades" => $subjects], status: Response::HTTP_OK);
    }
    public function deleteSubject(Request $request)
    {
        $subjects = Subject::where('id', $request->subject_id)->where('gradeId', $request->grade_id);

        if (!$subjects)
            return response(['message' => 'Error in deleting subject'], status: Response::HTTP_CONFLICT);

        $subjects->delete();
        return response(['message' => 'deleted subject'], status: Response::HTTP_OK);
    }
}
