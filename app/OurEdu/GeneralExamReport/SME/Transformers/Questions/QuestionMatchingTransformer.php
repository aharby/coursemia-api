<?php


namespace App\OurEdu\GeneralExamReport\SME\Transformers\Questions;


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
    public function transform($generalExamQuestion)
    {
        $matchingData = $generalExamQuestion->questionable()->get()->first();

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

