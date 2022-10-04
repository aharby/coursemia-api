<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\ListSessionTypesTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectTime;
use App\OurEdu\Users\Transformers\StudentTransformer;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
class StudentSubjectPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'examsPerformance',
        'activityLog',
        'studentUser',
        'times',
        'studentQuizzes',
        'course_sessions',
        'live_sessions',
        'vcr_schedule_session'

    ];


    public function transform(LearningPerformance $learningPerformance)
    {
        return [
            'id' => Str::uuid(),
            'student_order' => (int) $learningPerformance->student_order,
            'number_of_taken_exams' => (int) $learningPerformance->student->exams()
                ->where('subject_id', $learningPerformance->subject->id)
                ->where('type', ExamTypes::EXAM)->count(),
            'success_rate' =>  $learningPerformance->success_rate,
            'time' => getStudentSubjectTimeInHours($learningPerformance->subject ,  $learningPerformance->student)  . " " . trans('subject.Hours'),
            'solving_speed_percentage_order' =>  $learningPerformance->solving_speed_percentage_order, // according to all students
            'subject_progress_percentage_order' =>  $learningPerformance->subject_progress_percentage_order, // according to all students
            'exams_count_order' =>  $learningPerformance->exams_count_order, // according to all students
            'exams_pagination' =>  $learningPerformance->subject->exams()->where('type', ExamTypes::EXAM)
                ->where('student_id', $learningPerformance->student->id)
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->paginate(10, ['id'], 'exams-page'),
            'activity_pagination' =>  $learningPerformance->student->events()
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->where('event_properties->subject_attributes->subject_id', $learningPerformance->subject->id)
                ->latest()->paginate(10, ['id'], 'activity-page'),
            'times-pagination' => SubjectTime::where('subject_id' , $learningPerformance->subject->id)
                ->where('student_id' , $learningPerformance->student->id)
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->latest()->paginate(10, ['id'], 'time-page'),
        ];
    }


    public function includeExamsPerformance(LearningPerformance $learningPerformance)
    {
        $exams = $learningPerformance->subject->exams()
                ->where('type', ExamTypes::EXAM)
                ->where('student_id', $learningPerformance->student->id)
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->paginate(10, ['*'], 'exams-page');

        if (count($exams) > 0) {
            return $this->collection($exams, new ExamsPerformanceTransformer(), ResourceTypesEnums::Exam);
        }
    }

    public function includeActivityLog(LearningPerformance $learningPerformance)
    {
        $activities = $learningPerformance->student->events()
            ->when(request('date_from'), function ($q)  {
                $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->where('event_properties->subject_attributes->subject_id', $learningPerformance->subject->id)
            ->latest()->paginate(10, ['*'], 'activity-page');
        if (count($activities) > 0) {
            return $this->collection($activities, new ActivitiesLogTransformer(), ResourceTypesEnums::ACTIVITY_LOG);
        }
    }

    public function includeStudentUser(LearningPerformance $learningPerformance)
    {
        if(isset($learningPerformance->student->user_id)){
            $user= User::find($learningPerformance->student->user_id);
            return $this->item($user, new UserTransformer(), ResourceTypesEnums::USER);

        }

    }

    public function includeTimes(LearningPerformance $learningPerformance){
        $times = SubjectTime::where('subject_id' , $learningPerformance->subject->id)
            ->where('student_id' , $learningPerformance->student->id)
            ->when(request('date_from'), function ($q)  {
                $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->latest()->paginate(10, ['*'], 'time-page');
        if (count($times) > 0) {
            return $this->collection($times, new TimeTransformer(), ResourceTypesEnums::SUBJECT_TIME);
        }
    }

    public function includeStudentQuizzes(LearningPerformance $learningPerformance){
        $quizzes = AllQuizStudent::where('student_id' , $learningPerformance->student->id)->get();
        if (count($quizzes)) {
            return $this->collection($quizzes, new StudentQuizTransformer(), ResourceTypesEnums::QUIZ);
        }
    }

    public function includeCourseSessions(LearningPerformance $learningPerformance)
    {
        $data = [
            'type' => VCRSessionsTypeEnum::COURSE_SESSION,
            'count' => $learningPerformance->course_sessions_count ?? 0,
            'student' => $learningPerformance->student,
            'courses' => $learningPerformance->courses,
        ];
        return $this->item($data, new ListSessionTypesTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }

    public function includeLiveSessions(LearningPerformance $learningPerformance)
    {
        $data = [
            'type' => VCRSessionsTypeEnum::LIVE_SESSION,
            'count' => $learningPerformance->live_sessions_count ?? 0,
            'student' => $learningPerformance->student
        ];

        return $this->item($data, new ListSessionTypesTransformer(), ResourceTypesEnums::LIVE_SESSION);
    }

    public function includevcrscheduleSession(LearningPerformance $learningPerformance)
    {
        $data = [
            'type' => VCRSessionsTypeEnum::VCR_SCHEDULE_SESSION,
            'count' => $learningPerformance->requested_sessions_count ?? 0,
            'student' => $learningPerformance->student
        ];

        return $this->item($data, new ListSessionTypesTransformer(), ResourceTypesEnums::VCR_SCHEDULE_SESSION);
    }
}

