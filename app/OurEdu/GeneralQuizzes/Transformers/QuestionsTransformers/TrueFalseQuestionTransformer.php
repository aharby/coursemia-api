<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\TrueFalseTransformers\TrueFalseQuestionAnswerTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class TrueFalseQuestionTransformer extends TransformerAbstract
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

    public function transform(TrueFalseQuestion $question)
    {
        $transformData = [
            'id' => (int)$question->id,
            'question' => (string)$question->text,
            'question_feedback' => (string)$question->question_feedback,
            'media' => (object)questionMedia($question),
            'audio' => (object)questionAudio($question),
            'video' => (object)questionVideo($question),
            'audio_link' => $question->audio_link ?? null,
            'video_link' => $question->video_link ?? null,
        ];
        if (
            in_array(
                auth()->user()->type,
                [
                    UserEnums::SCHOOL_SUPERVISOR,
                    UserEnums::SCHOOL_LEADER,
                    UserEnums::ACADEMIC_COORDINATOR,
                    UserEnums::EDUCATIONAL_SUPERVISOR,
                    UserEnums::SCHOOL_INSTRUCTOR,
                    UserEnums::INSTRUCTOR_TYPE,
                    UserEnums::PARENT_TYPE,
                    UserEnums::SCHOOL_ADMIN,
                    UserEnums::INSTRUCTOR_TYPE
                ]
            )
        ) {
            $transformData['is_true'] = (bool)$question->is_true;
        }

        return $transformData;
    }

    public function includeAnswer(TrueFalseQuestion $question)
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
                    new TrueFalseQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::STUDENT_Homework_QUESTION_ANSWER
                );
            }
        }
    }
}
