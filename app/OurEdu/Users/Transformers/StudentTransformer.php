<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Transformers\CourseListTransformer;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Transformers\SchoolAccountBranchTransformer;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\SubjectPackages\Student\Transformers\ListPackagesTransformer;
use App\OurEdu\Users\Enums\AvailableEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Profile\Transformers\ActivitiesTransformer;
use App\OurEdu\Profile\Transformers\SentInvitationTransformer;
use App\OurEdu\Courses\Transformers\LiveSessionListTransformer;
use App\OurEdu\Profile\Transformers\ReceivedInvitationTransformer;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;

class StudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'activities',
        'subjects',
        'parents',
        'studentTeachers',
        'liveSessions',
        'courses',
        'packages',
        'availableSubject',
        'availableLiveSession',
        'availableCourses',
        'availablePackages',
        'studentTeachersReceivedInvitations',
        'studentTeachersSentInvitation',
        'parentsReceivedInvitations',
        'parentSentInvitation',
        'schoolAccountBranch'
    ];
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
        if (isset($this->params['no_action'])) {
            $this->defaultIncludes = [];
        }
    }

    public function transform(Student $student)
    {
        $user = $student->user;
        $transformedData = [
            'id' => $student->id,
            'birth_date' => $student->birth_date,
            'educational_system_id' => $student->educational_system_id,
            'school_id' => $student->school_id,
            'class_id' => $student->class_id,
            'classroom_id' => (int)$student->classroom_id,
            'academical_year_id' => $student->academical_year_id,
            'wallet_amount' => $student->wallet_amount,
            'country_currency' => $user->country->currency_code,
            'name' => $user->name,
            'email' => $user->email
        ];
        return $transformedData;
    }

    public function includeSubjects($student)
    {
        if ($student->subjects()->count()) {
            if (isset($this->params['subjects_limit'])) {
                // return all
                return $this->collection(
                    $student->subjects()->get(),
                    new ListSubjectsTransformer(),
                    ResourceTypesEnums::SUBJECT
                );
            }
            // paginate only 3 subjects
            return $this->collection(
                $student->subjects()->paginate(AvailableEnum::SUBJECT_LIMIT, ['*'], 'subjects_page'),
                new ListSubjectsTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }
    }

    public function includeCourses($student)
    {
        if ($student->courses()->count()) {
            if (isset($this->params['courses_limit'])) {
                // return all
                return $this->collection(
                    $student->courses()->paginate(env('PAGE_LIMIT', 20), ['*'], 'courses_page'),
                    new CourseListTransformer(),
                    ResourceTypesEnums::COURSE
                );
            }
            // paginate only 3 courses
            return $this->collection(
                $student->courses()->paginate(AvailableEnum::COURSE_LIMIT, ['*'], 'courses_page'),
                new CourseListTransformer(),
                ResourceTypesEnums::COURSE
            );
        }
    }

    public function includePackages($student)
    {
        if ($student->packages()->count()) {
            return $this->collection(
                $student->packages()->paginate(env('PAGE_LIMIT', 20), ['*'], 'packages_page'),
                new ListPackagesTransformer(),
                ResourceTypesEnums::SUBJECT_PACKAGE
            );
        }
    }

    public function includeActivities(Student $student)
    {
        return $this->item($student, new ActivitiesTransformer(), ResourceTypesEnums::ACTIVITIES);
    }

    // to get the parents's data for the student user
    public function includeParents(Student $student)
    {
        $parents = $student->user->parents;
        if ($parents && count($parents) > 0) {
            return $this->collection($parents, new ListParentsTransformer(), ResourceTypesEnums::USER);
        }
    }

    // to get the parents's data for the student user
    public function includeStudentTeachers(Student $student)
    {
        $teachers = $student->user->teachers;
        if ($teachers && count($teachers) > 0) {
            return $this->collection($teachers, new ListTeacherTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeActions(Student $student)
    {
        $actions = [];
        if (auth()->check()) {
            if (auth()->user()->type == UserEnums::PARENT_TYPE) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.profile.removeRelation', ['id' => $student->user->id]),
                    'label' => trans('invitations.Remove Student'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::REMOVE_STUDENT
                ];
            }
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.subjects.get.index'),
            'label' => trans('subjects.Get Subjects'),
            'method' => 'GET',
            'key' => APIActionsEnums::AVAILABLE_SUBJECTS
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.courses.listCourses'),
            'label' => trans('course.Get available courses'),
            'method' => 'GET',
            'key' => APIActionsEnums::AVAILABLE_COURSES
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.liveSessions.list'),
            'label' => trans('course.Get available Live sessions'),
            'method' => 'GET',
            'key' => APIActionsEnums::AVAILABLE_LIVE_SESSION
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.subjectPackages.get.available.packages'),
            'label' => trans('course.Get available subject packages'),
            'method' => 'GET',
            'key' => APIActionsEnums::AVAILABLE_SUBJECT_PACKAGE
        ];


        if ($student->courses()->count()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.courses.listCourses') . '?subscribed=true',
                'label' => trans('courses.Get Subscribed Courses'),
                'method' => 'GET',
                'key' => APIActionsEnums::SUBSCRIBED_COURSES
            ];
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.courses.qudrat.listCourses'),
                'label' => trans('courses.Get UnSubscribed Courses'),
                'method' => 'GET',
                'key' => APIActionsEnums::UNSUBSCRIBED_COURSES
            ];
        }
        if ($student->subjects()->count()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.get.qudratIndex') . '?subscribed=true',
                'label' => trans('subjects.Get Subscribed Subjects'),
                'method' => 'GET',
                'key' => APIActionsEnums::SUBSCRIBED_SUBJECTS
            ];
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.get.qudratIndex') . '?subscribed=false',
                'label' => trans('subjects.Get UnSubscribed Subjects'),
                'method' => 'GET',
                'key' => APIActionsEnums::UNSUBSCRIBED_SUBJECTS
            ];
        }


//        $actions[] = [
//            'endpoint_url' => buildScopeRoute('api.student.subjects.get.list-subjects'),
//            'label' => trans('invitations.Get my subjects'),
//            'method' => 'GET',
//            'key' => APIActionsEnums::MY_SUBJECTS
//        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeStudentTeachersSentInvitation()
    {
        // returning the invitations except the ACCEPTED one's (because they already children to this user)
        $invitations = Auth::guard('api')->user()->sentInvitations()
            ->whereIn('status', InvitationEnums::getSenderAvailableStatuses())
            ->where('type', 'student_student_teacher')->get();

        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new SentInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeParentSentInvitation()
    {
        // returning the invitations except the ACCEPTED one's (because they already children to this user)
        $invitations = Auth::guard('api')->user()->sentInvitations()
            ->whereIn('status', InvitationEnums::getSenderAvailableStatuses())
            ->where('type', 'student_parent')->get();


        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new SentInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeStudentTeachersReceivedInvitations()
    {
        $invitations = Auth::guard('api')->user()->receivedInvitations()
            ->whereHas('sender', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereIn('status', InvitationEnums::getReceiverAvailableStatuses())
            ->where('type', 'student_teacher_student')->get();

        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new ReceivedInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeParentsReceivedInvitations()
    {
        $invitations = Auth::guard('api')->user()->receivedInvitations()
            ->whereHas('sender', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereIn('status', InvitationEnums::getReceiverAvailableStatuses())
            ->where('type', 'parent_student')->get();

        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new ReceivedInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeLiveSessions(Student $student)
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->pluck('id')->toArray();

        $studentSubjects = $student->subjects()->pluck('subjects.id')->toArray();

        $relatedSubjects = array_merge($subjects, $studentSubjects);

        $liveSessions = LiveSession::latest()
            ->where('is_active', true)
            ->whereIn('subject_id', $relatedSubjects)
            ->with('sessions', 'instructor', 'subject');

        if ($liveSessions->count()) {
            if (isset($this->params['sessions_limit'])) {
                // return all
                return $this->collection(
                    $liveSessions->paginate(env('PAGE_LIMIT', 20), ['*'], 'live_sessions'),
                    new LiveSessionListTransformer(),
                    ResourceTypesEnums::LIVE_SESSION
                );
            }
            // paginate only 3 sessions
            return $this->collection(
                $liveSessions->paginate(3, ['*'], 'live_sessions'),
                new LiveSessionListTransformer(),
                ResourceTypesEnums::LIVE_SESSION
            );
        }
    }

    public function includeAvailableSubject(Student $student)
    {
        $subjects = Subject::where('country_id', $student->user->country_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('grade_class_id', $student->class_id)
            ->where('is_active', 1)->get();
        return $this->collection($subjects, new ListSubjectsTransformer(), ResourceTypesEnums::SUBJECT);
    }

    public function includeAvailablePackages(Student $student)
    {
        $packages = Package::where('country_id', $student->user->country_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('grade_class_id', $student->class_id)
            ->where('is_active', 1)->get();
        return $this->collection($packages, new ListPackagesTransformer(), ResourceTypesEnums::SUBJECT_PACKAGE);
    }

    public function includeAvailableLiveSession(Student $student)
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', $student->user->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();
        $liveSessions = LiveSession::latest()
            ->where(function ($query) use ($subjects) {
                $query->whereIn('subject_id', $subjects);
                $query->orWhere('subject_id', null);
            })
            ->where('is_active', 1)
            ->whereHas('session', function ($q) {
                $q->whereDate('date', '>=', date('Y-m-d'))
                    ->wherebetween('start_time', [
                        now()->format('H:i:s'),
                        now()->addMinutes(CourseSessionEnums::AVAILABILITY_TIME)->format('H:i:s')
                    ]);
            })
            ->with('session', 'instructor', 'subject')
            ->limit(AvailableEnum::LIVE_SESSION_LIMIT)
            ->get();
        return $this->collection($liveSessions, new LiveSessionListTransformer(), ResourceTypesEnums::LIVE_SESSION);
    }

    public function includeAvailableCourses(Student $student)
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', $student->user->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();
        $courses = Course::latest()
            ->where(function ($query) use ($subjects) {
                $query->whereIn('subject_id', $subjects);
                $query->orWhere('subject_id', null);
            })
            ->where('is_active', 1)
            ->limit(AvailableEnum::COURSE_LIMIT)
            ->get();
        return $this->collection($courses, new CourseListTransformer(), ResourceTypesEnums::COURSE);
    }

    public function includeSchoolAccountBranch()
    {
        $studentSchoolBranchId = auth()->user()->branch_id;
        $schoolAccountBranchStudent = SchoolAccountBranch::whereId($studentSchoolBranchId)->get();
        if ($schoolAccountBranchStudent) {
            return $this->collection(
                $schoolAccountBranchStudent,
                new SchoolAccountBranchTransformer(),
                ResourceTypesEnums::SCHOOL_ACCOUNT_BRANCH
            );
        }
    }
}
