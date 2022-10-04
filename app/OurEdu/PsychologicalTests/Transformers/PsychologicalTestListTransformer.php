<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;

class PsychologicalTestListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions'
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User;
    }

    public function transform(PsychologicalTest $test)
    {
        $transformedData = [
            'id' => (int) $test->id,
            'name' => (string) $test->name,
            'instructions' => (string) $test->instructions,
            'picture' => (string) $test->picture ? url(imageProfileApi($test->picture, 'large')) : '',
            'is_active' => (boolean) $test->is_active,
            'created_at' => (string) $test->created_at,
        ];

        return $transformedData;
    }

    public function includeActions(PsychologicalTest $test)
    {
        $actions = [];

        $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.psychological_tests.get.view.psychological_test', ['id' => $test->id]),
                    'label' => trans('app.View'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::VIEW_PSYCHOLOGICAL_TEST
                ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
