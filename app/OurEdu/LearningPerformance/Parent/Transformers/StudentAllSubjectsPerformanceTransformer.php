<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\CoursesListTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\ListSessionTypesTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\ListVCRSessionsTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\PackagesListTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\SubjectsListTransformer;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Models\Transaction;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentAllSubjectsPerformanceTransformer extends TransformerAbstract
{
    private $student;

    protected array $defaultIncludes = [
        'activityLog',
        'availableSubjects',
        'availableCourses',
        'availablePackages',
        'studentUser',
        'transactions',
        'studentQuizzes',
        'course_sessions',
        'live_sessions',
        'vcr_schedule_session'
    ];

    public function __construct(LearningPerformance $learningPerformance)
    {
        $this->student = $learningPerformance->student;
    }

    public function transform(LearningPerformance $learningPerformance)
    {
        return [
            'id' => Str::uuid() ,
            'activity_pagination' =>  $learningPerformance->student->events()
                ->when(request('date_from'), function ($q)  {
                    $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->whereIn('event_properties->subject_attributes->subject_id',
                    $learningPerformance->student->subjects()->pluck('subject_id')->toArray())
                ->latest()->paginate(10, ['id'], 'activity-page'),

            'subject_pagination' =>  Subject::where('country_id', $this->student->user->country_id)
                ->where('educational_system_id', $this->student->educational_system_id)
                ->where('academical_years_id', $this->student->academical_year_id)
                ->where('grade_class_id',  $this->student->class_id)
                ->paginate(10, ['id'], 'subject-page'),

            'transaction_pagination' =>  PaymentTransaction::query()
                ->where('receiver_id' , $learningPerformance->student->user_id)
                ->where('payment_method' ,PaymentEnums::WALLET)
                ->where('payment_transaction_type' , TransactionTypesEnums::WITHDRAWAL)
                ->when(request('date_from'), function ($q)  {
                    $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->paginate(10, ['id'], 'transaction-page'),
        ];
    }

    public function includeActivityLog(LearningPerformance $learningPerformance)
    {

        $activities = $learningPerformance->student->events()
            ->when(request('date_from'), function ($q)  {
                $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->whereIn('event_properties->subject_attributes->subject_id',
                $learningPerformance->student->subjects()->pluck('subject_id')->toArray())

                ->latest()
            ->paginate(10, ['*'], 'activity-page');
        if (count($activities)) {
            return $this->collection($activities, new ActivitiesLogTransformer(), ResourceTypesEnums::ACTIVITY_LOG);
        }
    }

    public function includeAvailableSubjects()
    {
        $subjects = Subject::where('country_id', $this->student->user->country_id)
            ->where('educational_system_id', $this->student->educational_system_id)
            ->where('academical_years_id', $this->student->academical_year_id)
            ->where('grade_class_id',  $this->student->class_id)
            ->paginate(10, ['*'], 'subject-page');

        if (count($subjects)) {
            return $this->collection($subjects, new SubjectsListTransformer($this->student), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeAvailablePackages()
    {
        $packages = Package::where('country_id', $this->student->user->country_id)
            ->where('educational_system_id', $this->student->educational_system_id)
            ->where('academical_years_id', $this->student->academical_year_id)
            ->where('grade_class_id', $this->student->class_id)
            ->get();

        if (count($packages)) {
            return $this->collection($packages, new PackagesListTransformer($this->student), ResourceTypesEnums::SUBJECT_PACKAGE);
        }
    }

    public function includeAvailableCourses()
    {
        $subjects = Subject::where('academical_years_id', $this->student->academical_year_id)
            ->where('educational_system_id', $this->student->educational_system_id)
            ->where('country_id', $this->student->user->country_id)
            ->where('grade_class_id', $this->student->class_id)
            ->pluck('id')->toArray();

        $courses = Course::latest()
            ->where(function ($query) use ($subjects) {
                $query->whereIn('subject_id', $subjects);
                $query->orWhere('subject_id', null);
            })->get();

        if (count($subjects)) {
            return $this->collection($courses, new CoursesListTransformer($this->student), ResourceTypesEnums::COURSE);
        }
    }

    public function includeStudentQuizzes(LearningPerformance $learningPerformance){
        $quizzes = AllQuizStudent::where('student_id' , $learningPerformance->student->id)->get();
        if (count($quizzes)) {
            return $this->collection($quizzes, new StudentQuizTransformer(), ResourceTypesEnums::QUIZ);
        }
    }


    public function includeStudentUser(LearningPerformance $learningPerformance)
    {
        if(isset($learningPerformance->student->user_id)){
            $user= User::find($learningPerformance->student->user_id);
            return $this->item($user, new UserTransformer(), ResourceTypesEnums::USER);

        }

    }


    public function includeTransactions(LearningPerformance $learningPerformance){
        $transactions = PaymentTransaction::query()
            ->where('receiver_id' , $learningPerformance->student->user_id)
            ->where('payment_method' ,PaymentEnums::WALLET)
            ->where('payment_transaction_type' , TransactionTypesEnums::WITHDRAWAL)
            ->where('status' ,PaymentEnums::COMPLETED)
            ->when(request('date_from'), function ($q)  {
                $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
            })->paginate(10, ['*'], 'transaction-page');

        if (count($transactions)) {
            return $this->collection($transactions, new TransactionTransformer(), ResourceTypesEnums::TRANSACTION);
        }
    }

    public function includeCourseSessions(LearningPerformance $learningPerformance)
    {
        $data = [
            'type' => VCRSessionsTypeEnum::COURSE_SESSION,
            'count' => $learningPerformance->course_sessions_count ?? 0,
            'student' => $learningPerformance->student
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

