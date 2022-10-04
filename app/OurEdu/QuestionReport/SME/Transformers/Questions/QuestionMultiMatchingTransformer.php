<?php


namespace App\OurEdu\QuestionReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use League\Fractal\TransformerAbstract;

class QuestionMultiMatchingTransformer extends TransformerAbstract
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
     * @param MultiMatchingData $multiMatchingData
     * @return array
     */
    public function transform($questionReport)
    {

        $multiMatchingData = $questionReport->questionable()->get()->first();

        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
            ];

        }

        foreach ($multiMatchingData->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->option
            ];

            $optionsData['questions'] = $option->questions()->pluck('res_multi_matching_questions.id')->toArray();

            $options[] = $optionsData;
        }
        return [
            'id' => $multiMatchingData->id,
            'description' => $multiMatchingData->description,
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

