<?php


namespace App\OurEdu\GeneralExams\SME\Transformers\Questions;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class TrueFalseDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [


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
    public function transform(TrueFalseData $trueFalseData)
    {
        $questions = [];
        $question = $trueFalseData->questions()->findOrFail($this->params['questionId']);
        $questionArray = [
            'id' => $question->id,
            'question_type' => LearningResourcesEnums::TRUE_FALSE,
            'text' => $question->text,
            'question_feedback' => $question->question_feedback,
            'is_true' => (bool)$question->is_true,
        ];


        if (isset($trueFalseData->TrueFalseType)&&$trueFalseData->TrueFalseType->slug == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT) {
            $options = [];
            foreach ($question->options as $option) {
                $options[] = [
                    'id' => $option->id,
                    'option' => $option->option,
                    'is_correct' => (bool)$option->is_correct_answer
                ];
            }

            $questionArray['options'] = $options;

        }

        $questionArray['media'] = (object) questionMedia($question);

        $questions[] = $questionArray;


        return [
            'id' => Str::uuid(),
            'description' => $trueFalseData->description,
            'true_false_type' => $trueFalseData->TrueFalseType->slug??'',
            'questions' => $questions
        ];
    }
}
