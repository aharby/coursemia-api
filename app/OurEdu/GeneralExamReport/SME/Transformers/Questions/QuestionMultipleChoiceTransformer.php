<?php


namespace App\OurEdu\GeneralExamReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use League\Fractal\TransformerAbstract;

class QuestionMultipleChoiceTransformer extends TransformerAbstract
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

    /**
     * @param MultipleChoiceData $multipleChoiceData
     * @return array
     */
    public function transform($generalExamQuestion)
    {
        $question = $generalExamQuestion->questionable()->get()->first();


        $questions = [];

        $options = [];
        foreach ($question->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->answer,
            ];

            $optionsData['is_correct_answer'] = (bool)$option->is_correct_answer;
            $options[] = $optionsData;
        }
        $questions = [
            'id' => $question->id,
            'question' => $question->question,
            'options' => $options,
        ];

        return $questions;
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

