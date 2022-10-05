<?php

namespace App\Modules\Users\Transformers;

use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use App\Modules\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;
use App\Modules\Subjects\Student\Transformers\ListSubjectsTransformer;

class ListChildrenTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    public function transform(User $user)
    {
        $student = $user;
        $transformedData = [
            'id' => (integer) $student->id,
            'student_id'=>(integer) $user->student->id,
            'name' => (string)$user->name,
            'branch_id'=>(int)$user->branch_id,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
        ];
        return $transformedData;
    }

    public function includeSubjects($user)
    {
        $student = $user->student;
        $subjects = $student->subjects;
        return $this->collection($subjects, new ListSubjectsTransformer(), ResourceTypesEnums::SUBJECT);
    }

    public function includeActions($user)
    {
        $student = $user->student;

        $actions = [];

        if ($authUser = Auth::guard('api')->user()) {
            if ($authUser->type == UserEnums::PARENT_TYPE) {

                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.profile.removeRelation', ['id' => $student->user_id]),
                    'label' => trans('invitations.Remove Student'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::REMOVE_STUDENT
                ];

                // commented until its business finished
//                $actions[] = [
//                    'endpoint_url' => buildScopeRoute('api.profile.view-child-profile', ['childId' => $user->id]),
//                    'label' => trans('profile.View Child Profile'),
//                    'method' => 'GET',
//                    'key' => APIActionsEnums::VIEW_CHILD_PROFILE
//                ];

                // actions to list subjects/courses/packages
//                if (isset($user->student)) {
                    $actions[] = [
                            'endpoint_url' => buildScopeRoute('api.student.subjects.get.list-subjects-by-parent', ['studentId' => $student->id]),
                            'label' => trans('profile.View Child Subjects'),
                            'method' => 'GET',
                            'key' => APIActionsEnums::VIEW_CHILD_SUBJECTS
                        ];

                    // quizzes performance
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.quizzes.getStudentQuizzesPerformance', ['studentId' => $student->id]),
                        'label' => trans('quiz.List Quizzes'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::LIST_QUIZZES
                    ];

                    $actions[] = [
                            'endpoint_url' => buildScopeRoute('api.student.courses.listCoursesForStudent', ['student' => $student->id]),
                            'label' => trans('profile.View Child Courses'),
                            'method' => 'GET',
                            'key' => APIActionsEnums::VIEW_CHILD_COURSES
                        ];

                    $actions[] = [
                            'endpoint_url' => buildScopeRoute('api.student.subjectPackages.listSubjectPackagesForStudent', ['studentId' => $student->id]),
                            'label' => trans('profile.View Subject Packages'),
                            'method' => 'GET',
                            'key' => APIActionsEnums::VIEW_CHILD_SUBJECT_PACKAGES
                        ];
//                }
            }

            if ($authUser->type == UserEnums::STUDENT_TEACHER_TYPE) {
                // commented until its business finished
//                $actions[] = [
//                    'endpoint_url' => buildScopeRoute('api.profile.view-child-profile', ['childId' => $user->id]),
//                    'label' => trans('profile.View Child Profile'),
//                    'method' => 'GET',
//                    'key' => APIActionsEnums::VIEW_CHILD_PROFILE
//                ];

                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.profile.removeRelation', ['id' => $student->user_id]),
                    'label' => trans('invitations.Remove Student'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::REMOVE_STUDENT
                ];

                // actions to list subjects/courses
//                if (isset($user->student)) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.subjects.get.list-subjects-by-parent', ['studentId' => $student->id]),
                        'label' => trans('profile.View Child Subjects'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::VIEW_CHILD_SUBJECTS
                    ];
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.courses.listCoursesForStudent', ['student' => $student]),
                        'label' => trans('profile.View Child Courses'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::VIEW_CHILD_COURSES
                    ];
//                }
            }

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.parent.learningPerformance.get.getStudentAllSubjectsPerformance', ['studentId' => $student->id]),
                'label' => trans('course.Get Activity'),
                'method' => 'GET',
                'key' => APIActionsEnums::GET_GENERAL_ACTIVITY
            ];


            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
