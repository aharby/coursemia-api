<?php

namespace App\Modules\Users\Transformers;

use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Auth\Enum\LoginEnum;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class UserAuthTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];
    protected array $availableIncludes = [
        'student',
        'parent',
        'actions',
        'instructor'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(User $user)
    {
        if (isset($this->params['no_data'])) {
            $transformedData = [
                'id' => (int)$user->id,
                'name' => (string)$user->email,
                'profile_picture' => (string)dummyPicture(),
            ];
            return $transformedData;
        }


        if (isset($this->params['invitations_search'])) {
            $transformedData = [
                'id' => (int)$user->id,
                'name' => (string)$user->name,
                'language' => (string)$user->language,
                'mobile' => (string)$user->mobile,
                'profile_picture' => (string)imageProfileApi($user->profile_picture),
                'type' => (string)$user->type,
                'user_type' => (string)$user->type,
                'email' => (string)$user->email,
                'country_id' => $user->country_id,
                'is_active' => (boolean)$user->is_active,
                'confirmed' => (boolean)$user->confirmed,
                'suspended' => (boolean)is_null($user->suspended_at) ? false : true,
            ];
            return $transformedData;
        }

        $loginRedirect = new LoginEnum();
        $redirect = $loginRedirect->getTypeLink($user->type);
        $shouldChangePassword = false;
        if (isset($this->params['shouldChangePassword'])) {
            $shouldChangePassword = true;
        }

        $transformedData = [
            'id' => (int)$user->id,
            'name' => (string)$user->name,
            'language' => (string)$user->language,
            'mobile' => (string)$user->mobile,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
            'type' => (string)$user->type,
            'user_type' => (string)$user->type,
            'email' => (string)$user->email,
            'country_id' => $user->country_id,
            'branch_id'=> $user->branch_id,
            'is_active' => (boolean)$user->is_active,
            'confirmed' => (boolean)$user->confirmed,
            'suspended' => (boolean)is_null($user->suspended_at) ? false : true,
            'redirect_url' => $redirect,
            'should_change_password' => $shouldChangePassword,
        ];

        if ($user->type == UserEnums::STUDENT_TYPE) {
            $classRoomId = $user->student->classroom_id ?? null;

            $transformedData['classroom_id'] = $classRoomId;
            $transformedData['is_school_student'] = !empty($classRoomId) ? true : false;
            $transformedData['student_school_img'] =(string)getSchoolLogo();
            $transformedData['back_ground_slug'] =$user->back_ground_slug;

        }
        if($user->type == UserEnums::PARENT_TYPE){
            $student = $user->students()->whereHas('student',function($query){
                $query->whereNotNull('classroom_id');
            })->first();
            $transformedData['is_school_student_parent'] = false;
            if($student){
                $transformedData['is_school_student_parent'] = true;
            }
        }

        if ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR){
            $transformedData['school_image'] = (string) getSchoolLogo();
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
            $params= array_merge($this->params, $params);
            return $this->item($user, new ParentTransformer($params), ResourceTypesEnums::PARENT);
        }
    }

    public function includeInstructor(User $user)
    {
        if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
            $params['no_data'] = true;
            $instructor = $user->instructor;
            $params= array_merge($this->params, $params);
            return $this->item($instructor, new InstructorTransformer($params), ResourceTypesEnums::INSTRUCTOR);
        }
    }

    public function includeActions(User $user)
    {
        $actions = [];

        // user type or the request user type
        $type = $user->type == ResourceTypesEnums::UNREGISTERED_USER ? request('type') : $user->type;

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.invitations.invite', ['email' => $user->email, 'type' => $type]),
            'label' => trans('invitations.Invite'),
            'method' => 'POST',
            'key' => APIActionsEnums::SEND_INVITATION
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
