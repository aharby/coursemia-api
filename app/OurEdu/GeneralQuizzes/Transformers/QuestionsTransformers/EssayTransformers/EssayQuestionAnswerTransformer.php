<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\EssayTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class EssayQuestionAnswerTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    /**
     * @var array
     */
    private $params;

    /**
     * MultipleChoiceQuestionAnswerTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuizStudentAnswer $answer)
    {
        $data = [
            'id' => (int)$answer->id,
            'answer_text' => (string)$answer->answer_text,
        ];
        if (in_array(auth()->user()->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::INSTRUCTOR_TYPE])) {
            $data['score'] = (float)$answer->score;
            $data['is_reviewed'] = (bool)$answer->is_reviewed;
        }

        return $data;
    }

    public function includeActions(GeneralQuizStudentAnswer $answer)
    {
        $actions = [];
        if (!$answer->is_reviewed && auth()->user()->type !== UserEnums::STUDENT_TYPE) {
            $endpointUrl = buildScopeRoute(
                'api.general-quizzes.homework.instructor.put.reviewEssay',
                ['homework' => $answer->general_quiz_id, 'answer' => $answer->id]
            );
            if (!empty($this->params) && $this->params['course_homework']) {
                $endpointUrl = buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.put.review_essay_question',
                    ['courseHomework' => $answer->general_quiz_id, 'answer' => $answer->id]
                );
            }
            $actions[] = [
                'endpoint_url'=>$endpointUrl,
                'label' => trans('general_quizzes.review essay'),
                'method' => 'PUT',
                'key' => APIActionsEnums::REVIEW_ESSAY
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
