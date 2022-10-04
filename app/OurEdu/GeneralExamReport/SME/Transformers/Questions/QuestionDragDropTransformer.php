<?php


namespace App\OurEdu\GeneralExamReport\SME\Transformers\Questions;


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
    public function transform($generalExamQuestion)
    {

        $dragDropData = $generalExamQuestion->questionable()->get()->first();

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


    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public function includeActions($generalExamQuestion)
    {
        if (!$generalExamQuestion->is_ignored) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams_reports.ignore', ['question' => $generalExamQuestion->report_id]),
                'label' => trans('subject.Ignore'),
                'method' => 'POST',
                'key' => APIActionsEnums::IGNORE_QUESTION
            ];
        }
        if (!$generalExamQuestion->is_reported) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams_reports.report', ['question' => $generalExamQuestion->report_id]),
                'label' => trans('subject.Report'),
                'method' => 'POST',
                'key' => APIActionsEnums::REPORT_QUESTION
            ];
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }


}

