<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\EssayTransformers\EssayQuestionAnswerTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayQuestion;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class EssayQuestionTransformer extends TransformerAbstract
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

    public function transform(EssayQuestion $question)
    {
        $data = [
            'id' => (int)$question->id,
            'question' => (string)$question->text,
            'question_feedback' => (string)$question->question_feedback,
            'media' => (object)questionMedia($question),
            'score' => $question->questionBank->grade,
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
                    UserEnums::EDUCATIONAL_SUPERVISOR,
                    UserEnums::ACADEMIC_COORDINATOR,
                    UserEnums::SCHOOL_INSTRUCTOR,
                    UserEnums::INSTRUCTOR_TYPE,
                    UserEnums::PARENT_TYPE,
                    UserEnums::SCHOOL_ADMIN
                ]
            )
        ) {
            $data['perfect_answers'] = (string)$question->perfect_answers;
        }

        return $data;
    }

    public function includeAnswer(EssayQuestion $question)
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
                    new EssayQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::STUDENT_Homework_QUESTION_ANSWER
                );
            }
        }
    }
}
