<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use League\Fractal\TransformerAbstract;

class QuestionBankTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = ['questionData'];

    protected array $availableIncludes = ['actions'];

    public function __construct(public GeneralQuiz $generalQuiz, public $params = [])
    {
    }

    public function transform(GeneralQuizQuestionBank $questionBank)
    {
        $question = $questionBank->generalQuiz()
            ->where('general_quiz_id', $this->generalQuiz->id)->first();

        $this->added_from_bank_questions =
            $question && $question->pivot && $question->pivot->added_from_bank == 1;

        return [
            "id" => (int)$questionBank->id,
            'question_type' => (string)$questionBank->slug,
            'direction' => (string)$questionBank->subject?->direction,
            'grade' => $questionBank->grade,
            "added_from_bank_questions" => $this->added_from_bank_questions,
            "public_status" => $questionBank->public_status != QuestionsPublicStatusesEnums::PRIVATE,
        ];
    }

    public function includeQuestionData(GeneralQuizQuestionBank $questionBank)
    {
        return $this->item(
            $questionBank->question ?? $questionBank->questions,
            new QuestionTransformer($this->generalQuiz),
            ResourceTypesEnums::HOMEWORK_QUESTION_DATA
        );
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {
        $actions = [];

        if (!$this->generalQuiz->studentsAnswered->count()) {
            $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.course-homework.instructor.delete.course_homework_question',
                [
                    'courseHomework' => $this->generalQuiz->id,
                    'question' => $questionBank->id
                ]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_QUESTION
        ];

            if (!$this->added_from_bank_questions) {
                $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.post.create_course_homework_question',
                    ['courseHomework' => $this->generalQuiz->id]
                ),
                'label' => trans('app.Edit HomeWork questions'),
                'method' => 'POST',
                'key' => APIActionsEnums::EDIT_HOMEWORK_QUESTIONS
            ];
            }
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
