<?php


namespace App\OurEdu\GeneralExams\SME\Transformers\Questions;

use Illuminate\Support\Str;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;

class DragDropDataTransformer extends TransformerAbstract
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
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform(DragDropData $dragDropData)
    {
        $questions = [];
        foreach ($dragDropData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'question' => $question->question,
                'question_type'  =>  LearningResourcesEnums::DRAG_DROP,
                'answers' => $question->correct_option_id,
                'media'=> (object) questionMedia($question)
            ];
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
            'question_feedback' => $dragDropData->question_feedback,
            'questions' => $questions,
            'options' => $options

        ];
    }
}
