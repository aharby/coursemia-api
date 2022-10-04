<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'student' ,
    ];

    public function __construct()
    {
    }

    public function transform($user)
    {

        $transformerData = [
            'id' => (int) $user->id,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'language' =>(string) $user->language,
            'mobile' => (string) $user->mobile,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
            'user-type' => (string) $user->type,
            'user_type' => (string) $user->type,
            'email' => (string) $user->email,
            'country_id' => $user->country_id,
        ];
        return $transformerData;
    }

    public function includeStudent($user) {

        if ($user->student()->exists()) {
            $student = $user->student;
            if ($student) {
                return $this->item($student, new StudentTransformer(), ResourceTypesEnums::STUDENT);
            }
        }
    }


}
