<?php


namespace App\OurEdu\QuestionReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use League\Fractal\TransformerAbstract;

class QuestionTrueFalseTransformer extends TransformerAbstract
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
     * @param TrueFalseData $trueFalseData
     * @return array
     */
    public function transform($questionReport)
    {

        $question = $questionReport->questionable()->get()->first();

        $options = [];
        foreach ($question->options as $option) {

            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                ];
            $optionData['is_correct'] = (bool)$option->is_correct_answer;

            $options[] = $optionData;
        }
        $questions = [
            'id' => $question->id,
            'text' => $question->text,
            'options' => $options,
        ];

        $questions['is_true'] = (bool)$question->is_true;

        return $questions;
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

