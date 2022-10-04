<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;

class PsychologicalQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'test'
    ];

    protected $user;
    protected $params;

    public function __construct($user = null, $params = [])
    {
        $this->user = $user ?? new User;
        $this->params = $params;
    }

    public function transform(PsychologicalQuestion $question)
    {
        $transformedData = [
            'id' => (int) $question->id,
            'name' => (string) $question->name,
            'is_active' => (boolean) $question->is_active,
        ];

        return $transformedData;
    }

    public function includeActions(PsychologicalQuestion $question)
    {
        $actions = [];

        if (isset($this->params['answer_endpoint'])) {
            $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.psychological_tests.post.answerQuestion', ['id' => $question->psychological_test_id, 'page' => request('page') ?? 1]),
                    'label' => trans('app.Answer'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::ANSWER_PSYCHOLOGICAL_QUESTION
                ];
        }

        if (isset($this->params['next_page']) && $this->params['next_page'] != null) {
            $actions[] = [
                    'endpoint_url' => $this->params['next_page'],
                    'label' => trans('app.Next'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::NEXT_PSYCHOLOGICAL_QUESTION
                ];
        }

        if (isset($this->params['finish_test']) && $this->params['finish_test'] != null) {
            $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.psychological_tests.post.finish', ['id' => $question->psychological_test_id]),
                        'label' => trans('app.Finish'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::FINISH_PSYCHOLOGICAL_TEST
                    ];
        }

        if (! count($actions)) {
            return;
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeTest(PsychologicalQuestion $question)
    {
        if ($question->test()->exists()) {
            return $this->item($question->test, new PsychologicalTestTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_TEST);
        }
    }
}
