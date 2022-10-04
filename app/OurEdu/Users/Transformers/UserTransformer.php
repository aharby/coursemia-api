<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\ParentTransformer;
use App\OurEdu\Ratings\Transformers\RatingTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Users\Transformers\StudentTeacherTransformer;
use function foo\func;

class UserTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'student',
        'parent',
        'ratings',
        'instructor',
        'contentAuthor',
        'studentTeacher',
        'actions'
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(User $user)
    {

        $transformedData = [
            'id' => (int)$user->id,
            'first_name' => (string)$user->first_name,
            'last_name' => (string)$user->last_name,
            'language' => (string)$user->language,
            'mobile' => (string)$user->mobile,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
            'user-type' => (string)$user->type,
            'user_type' => (string)$user->type,
            'email' => (string)$user->email,
            'country_id' => $user->country_id,
            'name' => $user->name,
        ];

        if($user->type == UserEnums::SCHOOL_INSTRUCTOR){
            $transformedData['branch_id'] = $user->branch_id;
        }
        /*
         * Instructor_type => teaches courses
         * School_instructor => teaches school_sessions and so on
         */
        if (in_array($user->type, [UserEnums::INSTRUCTOR_TYPE, UserEnums::SCHOOL_INSTRUCTOR])) {
            $transformedData['ratings_count'] = $user->ratings()->count();
            $transformedData['averege_rating'] = $user->avgRating();
            $transformedData['rating_percentage'] = $user->ratingPercent();

            if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
                $transformedData['reviews'] = (int)$user->ratings()->count();
                $transformedData['students'] = (int)$user->coursesStudents()->count();
                $transformedData['courses'] = (int)$user->courses()->count();
            }
        }
        if ($user->type == UserEnums::STUDENT_TYPE) {
            $classRoomId = $user->student->classroom_id ?? null;
            $transformedData['classroom_id'] = $classRoomId;
            $transformedData['student_id'] = $user->student->id;
            $transformedData['is_school_student'] = !empty($classRoomId) ? true : false;
            $transformedData['student_school_img'] =getSchoolLogo();
            $transformedData['back_ground_slug'] = $user->student->gradeClass->gradeColor->slug ?? '';
        }

        return $transformedData;
    }

    // to get the student's data for the student user
    public function includeStudent(User $user)
    {
        if ($user->student()->exists()) {
            $student = $user->student;
            if ($student) {
                return $this->item($student, new StudentTransformer($this->params), ResourceTypesEnums::STUDENT);
            }
        }
    }

    // to get the parent's data for the parent user
    public function includeParent(User $user)
    {
        if ($user->type == UserEnums::PARENT_TYPE) {
            $params['no_data'] = true;
            $params = array_merge($this->params, $params);
            return $this->item($user, new ParentTransformer($params), ResourceTypesEnums::PARENT);
        }
    }

    // to get the instructor's data for the parent user
    public function includeInstructor(User $user)
    {
        if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
            $params['no_data'] = true;
            $params = array_merge($this->params, $params);
            $instructor = $user->instructor;
            if ($instructor) {
                return $this->item($instructor, new InstructorTransformer($this->params), ResourceTypesEnums::INSTRUCTOR);
            }
        }
    }

    // to get the content author's data for the parent user
    public function includeContentAuthor(User $user)
    {
        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $params['no_data'] = true;
            $params = array_merge($this->params, $params);
            $author = $user->contentAuthor;
            if ($author) {
                return $this->item($author, new ContentAuthorTransformer($this->params), ResourceTypesEnums::CONTENT_AUTHOR);
            }
        }
    }

    // to get the StudentTeacher's data for the user object
    public function includeStudentTeacher(User $user)
    {
        if ($user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $params['no_data'] = true;
            $params = array_merge($this->params, $params);
            if ($user) {
                return $this->item($user, new StudentTeacherTransformer($this->params), ResourceTypesEnums::STUDENT_TEACHER);
            }
        }
    }

    public function includeActions(User $user)
    {
        $actions = [];

        if ($authUser = Auth::guard('api')->user()) {

            if ($authUser->type == UserEnums::PARENT_TYPE && $authUser->students()->count() > 0) {
                foreach ($authUser->students as $studentUser) {
                    if($studentUser->student){
                        $actions[] = [
                            'endpoint_url' => buildScopeRoute('api.parent.payments.submitTransaction', ['student_id' => $studentUser->student->id]),
                            'label' => trans('app.Add money to wallet'),
                            'method' => 'POST',
                            'key' => APIActionsEnums::ADD_MONEY_TO_WALLET
                        ];
                    }
                }
            }

            // case parent or teacher
            if (in_array($authUser->type, [UserEnums::PARENT_TYPE, UserEnums::STUDENT_TEACHER_TYPE])) {
                // add another student AKA child
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.search', ['type' => UserEnums::STUDENT_TYPE]),
                    'label' => trans('invitations.Search Student'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::SEARCH_STUDENT
                ];
            }

            if (in_array($authUser->type, [UserEnums::STUDENT_TYPE])) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.search', ['type' => UserEnums::PARENT_TYPE]),
                    'label' => trans('invitations.Search Parent'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::SEARCH_PARENT
                ];

                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.search', ['type' => UserEnums::STUDENT_TEACHER_TYPE]),
                    'label' => trans('invitations.Search Teacher'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::SEARCH_TEACHER
                ];
            }
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeRatings(User $user)
    {
        $ratings = $user->ratings()->latest()->with('user', 'instructor')->jsonPaginate();

        if ($ratings->count()) {
            return $this->collection($ratings, new RatingTransformer(), ResourceTypesEnums::RATING);
        }
    }
}
