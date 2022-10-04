<?php


namespace App\OurEdu\QuestionReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use League\Fractal\TransformerAbstract;

class QuestionDragDropTransformer extends TransformerAbstract
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
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform($questionReport)
    {

        $dragDropData = $questionReport->questionable()->get()->first();

        $questions = [];
        foreach ($dragDropData->questions as $question) {

            $questionsData = [
                'id' => $question->id,
                'question' => $question->question,
            ];
            $questionsData['correct_option_id'] = $question->correct_option_id;
            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($dragDropData->options as $option) {


            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
            ];

        }
        return [
            'id' => $dragDropData->id,
            'description' => $dragDropData->description,
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

