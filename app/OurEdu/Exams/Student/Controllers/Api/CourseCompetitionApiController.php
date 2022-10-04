<?php

namespace App\OurEdu\Exams\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Events\CompetitionEvents\StudentJoinedCompetition;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Student\Middleware\Api\IsStudentAllowedMiddleware;
use App\OurEdu\Exams\Student\Transformers\CourseCompetition\CourseCompetitionTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CourseCompetitionApiController extends BaseApiController
{
    public function __construct()
    {
       $this->middleware('type:student');
       $this->middleware(IsStudentAllowedMiddleware::class);

    }

    public function joinCompetition(Request $request, Exam $exam)
    {
        try {
            if ($exam->is_finished) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition is already finished');
                $return['title'] = 'The competition is already finished';
                return formatErrorValidation($return);
            }

            if (Carbon::now() > Carbon::parse($exam->start_time)->addMinutes(1)) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition is already started');
                $return['title'] = 'The competition is already started';
                return formatErrorValidation($return);
            }

            if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($exam->finished_time))) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition not started Yet');
                $return['title'] = 'The competition not started Yet';
                return formatErrorValidation($return);
            }

            if (Carbon::now() > Carbon::parse($exam->start_time)->addMinutes(1)) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition is already started');
                $return['title'] = 'The competition is already started';
                return formatErrorValidation($return);
            }

            $student = Auth::user()->student;

            $examRepo = new ExamRepository($exam);
            if (!$examRepo->checkIfStudentInCompetition($student->id)) {
                $examRepo->joinCompetition($student->id);
            }

            $meta = ['message' => trans('api.Joined successfully')];

            $competitionData = [
                'exam_id' => $exam->id,
                'exam_title' => $exam->title,
                'difficulty_level' => $exam->difficulty_level,
                'questions_number' => $exam->questions_number,
            ];
            StudentJoinedCompetition::dispatch(
                $competitionData,
                Auth::user()->toArray(),
                [
                    'subject_id' => $exam->subject_id
                ]
            );
            return $this->transformDataModInclude(
                $exam,
                'actions',
                new CourseCompetitionTransformer(),
                ResourceTypesEnums::COURSE_COMPETITION,
                $meta
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
