<?php


namespace App\OurEdu\QuestionReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use League\Fractal\TransformerAbstract;

class QuestionMatchingTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param MatchingData $matchingData
     * @return array
     */
    public function transform($questionReport)
    {
        $matchingData = $questionReport->questionable()->get()->first();

        $questions = [];
        $options = [];
        foreach ($matchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text
            ];
        }
        foreach ($matchingData->options as $option) {

            $optionsData = [
                'id' => $option->id,
                'option' => $option->option,
            ];

            $optionsData['question_id'] = $option->res_matching_question_id;
            $options[] = $optionsData;
        }
        return [
            'id' => $matchingData->id,
            'description' => $matchingData->description,
            'questions' => $questions,
            'options' => $options
        ];
    }

    public function includeActions($questionReport)
    {
        $actions = [];
        if (!$questionReport->is_ignored) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.sme.question.report.post.ignore.Question',
                    ['questionId' => $questionReport->id]
                ),
                'label' => trans('question_reports.ignore question'),
                'method' => 'POST',
                'key' => APIActionsEnums::REPORT_QUESTION
            ];
        }
        if (!$questionReport->is_reported) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.sme.question.report.post.report.Question',
                    ['questionId' => $questionReport->id]
                ),
                'label' => trans('question_reports.Report question'),
                'method' => 'POST',
                'key' => APIActionsEnums::IGNORE_QUESTION
            ];
        }
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }


}

