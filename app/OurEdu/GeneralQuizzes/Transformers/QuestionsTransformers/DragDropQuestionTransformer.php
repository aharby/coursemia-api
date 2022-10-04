<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers\DragDropQuestionAnswerTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers\DragDropQuestionDetailsTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers\DragDropQuestionOptionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers\DragDropQuestionQuestionDetailsTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use League\Fractal\TransformerAbstract;

class DragDropQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionDetails',
        'options',
        'answer',
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * TrueFalseQuestionTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(DragDropData $questionData)
    {
        $questionSlug = $questionData->questionBank->slug;

        return [
            'id' => (int)$questionData->id,
            'description' => (string)$questionData->description,
            'question_feedback' => (string)$questionData->question_feedback,
            'question_slug' => (string)$questionSlug,
            'type' => (string)QuestionsTypesEnums::getLabel($questionSlug),
        ];
    }

    public function includeQuestionDetails(DragDropData $questionData)
    {
        $questions = $questionData->questions;
        return $this->collection(
            $questions,
            new DragDropQuestionDetailsTransformer($this->params),
            ResourceTypesEnums::QUESTION
        );
    }

    public function includeOptions(DragDropData $questionData)
    {
        $options = $questionData->options()->inRandomOrder()->get();
        return $this->collection(
            $options,
            new DragDropQuestionOptionTransformer(),
            ResourceTypesEnums::QUESTION_OPTIONS
        );
    }

    public function includeAnswer(DragDropData $questionData)
    {
        // student param must be an instance of User model
        if (isset($this->params['student']) && isset($this->params['generalQuiz'])) {
            $answer = $questionData->generalQuizStudentAnswers()
                ->where('general_quiz_id', $this->params['generalQuiz']->id)
                ->where("student_id", "=", $this->params['student']->id)
                ->first();

            if ($answer) {
                return $this->item(
                    $answer,
                    new DragDropQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::STUDENT_Homework_QUESTION_ANSWER
                );
            }
        }
    }
}
