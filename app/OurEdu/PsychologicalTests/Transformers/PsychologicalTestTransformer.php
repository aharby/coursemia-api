<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalOptionTransformer;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalResultTransformer;

class PsychologicalTestTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'options',
        'activeOptions',
        'result',
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
                'endpoint_url' => buildScopeRoute('api.student.psychological_tests.post.start', ['id' => $test->id]),
                'label' => trans('app.Start'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_PSYCHOLOGICAL_TEST
            ];


        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeOptions(PsychologicalTest $test)
    {
        if ($test->options) {
            return $this->collection($test->options, new PsychologicalOptionTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_OPTION);
        }
    }

    public function includeResult(PsychologicalTest $test)
    {
        if ($result = $test->results()->latest()->where('user_id', $this->user->id)->first()) {
            return $this->item($result, new PsychologicalResultTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_RESULT);
        }
    }

    public function includeActiveOptions(PsychologicalTest $test)
    {
        if ($test->ActiveOptions) {
            return $this->collection($test->ActiveOptions, new PsychologicalOptionTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_OPTION);
        }
    }
}
