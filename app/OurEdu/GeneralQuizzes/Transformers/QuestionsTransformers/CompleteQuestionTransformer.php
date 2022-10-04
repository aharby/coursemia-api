<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\CompleteTransformers\CompleteQuestionAnswerTransformer;

class CompleteQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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

    public function transform(CompleteQuestion $question)
    {
        $data = [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            'question_feedback' => (string)$question->question_feedback,
            'media' => (object)questionMedia($question)
        ];

        if (!isset($this->params['student'])) {
            $answers = [];

            foreach ($question->acceptedAnswers as $answer) {
                $answers[] = [
                    'id' => $answer->id,
                    'answer' => $answer->answer
                ];
            }

            $data['answer'] = (string)$question->answer->answer ?? null;
            $data['accepted_answers'] = (array)$answers;
        }

        return $data;
    }

    public function includeAnswer(CompleteQuestion $question)
    {
        // student param must be an instance of User model
        if (isset($this->params['student']) && isset($this->params['generalQuiz'])) {
            $answer = $question->generalQuizStudentAnswers()
                ->where('general_quiz_id', $this->params['generalQuiz']->id)
                ->where("student_id", "=", $this->params['student']->id)
                ->first();

            if ($answer) {
                return $this->item(
                    $answer,
                    new CompleteQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::STUDENT_Homework_QUESTION_ANSWER
                );
            }
        }
    }
}
