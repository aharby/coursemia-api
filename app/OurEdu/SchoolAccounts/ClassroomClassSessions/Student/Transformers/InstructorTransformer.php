<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class InstructorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

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
        return $transformedData;
    }
}
