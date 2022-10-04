<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy;

use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyCompleteQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyQuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyQuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyQuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyQuestionMultipleChoiceTransformer;
use App\OurEdu\Exams\Student\Transformers\Dummy\Questions\DummyQuestionTrueFalseTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;

class DummyQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ExamQuestion $question)
    {
        $transformerData = [
            'id' => Str::uuid(),
            'slug' => $question->slug,
            'question_type' => (string) $question->question_type,
            'subject_id' => random_int(1 , 11),
            'subject_format_subject_id' => random_int(1 , 11),
//            'is_correct_answer' => (bool)$question->is_correct_answer,
        ];

        return $transformerData;
    }


    public function includeQuestionData(ExamQuestion $question)
    {
        $params = [];
//        $params = [
//            'is_answer' => true,
//        ];
        switch ($question->slug) {

            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    new TrueFalseData(),
                    new DummyQuestionTrueFalseTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->item(
                    new MultipleChoiceData(),
                    new DummyQuestionMultipleChoiceTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    new DragDropData(),
                    new DummyQuestionDragDropTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    new MatchingData(),
                    new DummyQuestionMatchingTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    new MultiMatchingData(),
                    new DummyQuestionMultiMatchingTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    new CompleteData(),
                    new DummyCompleteQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;
        }
    }
}
