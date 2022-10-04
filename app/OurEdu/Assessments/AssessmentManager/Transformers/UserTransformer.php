<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use function foo\func;

class UserTransformer extends TransformerAbstract
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
            'mobile' => (string)$user->mobile,
            'user_type' => (string)$user->type,
            'email' => (string)$user->email,
            'name' => $user->name,
        ];
        return $transformedData;
    }
}
