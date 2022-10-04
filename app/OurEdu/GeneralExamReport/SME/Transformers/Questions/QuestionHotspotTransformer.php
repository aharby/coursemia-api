<?php


namespace App\OurEdu\GeneralExamReport\SME\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use League\Fractal\TransformerAbstract;

class QuestionHotspotTransformer extends TransformerAbstract
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
    public function transform($generalExamQuestion)
    {

        $question = $generalExamQuestion->questionable()->get()->first();

        $options = [];
        foreach ($question->options as $answer) {

            $optionData =
                [
                    'id' => $answer->id,
                    'option' => $answer->option,
                ];
//            $optionData['is_correct'] = (bool)$answer->is_correct_answer;

            $options[] = $optionData;
        }
        $questions = [
            'id' => $question->id,
            'text' => $question->text,
            'options' => $options,
        ];

        $optionData['answer'] = $question->answer->answer ?? '';

        return $questions;
    }


}

