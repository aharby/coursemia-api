<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\MultipleChoiceTransformers\MultipleChoiceQuestionAnswerTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use League\Fractal\TransformerAbstract;

class MultipleChoiceQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'options',
        'answer'
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * MultipleChoiceQuestionTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(MultipleChoiceQuestion $question)
    {
        return [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            "url" => (string)$question->url,
            'question_feedback' => (string)$question->question_feedback,
            'media' => (object)questionMedia($question),
            'audio' => (object)questionAudio($question),
            'video' => (object)questionVideo($question),
            'audio_link' => $question->audio_link ?? null,
            'video_link' => $question->video_link ?? null,
        ];
    }

    public function includeOptions(MultipleChoiceQuestion $question)
    {
        $options = $question->options;

        return $this->collection(
            $options,
            new MultipleChoiceQuestionOptionsTransformer(),
            ResourceTypesEnums::QUESTION_OPTIONS
        );
    }

    public function includeAnswer(MultipleChoiceQuestion $question)
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
                    new MultipleChoiceQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::STUDENT_Homework_QUESTION_ANSWER
                );
            }
        }
    }
}
