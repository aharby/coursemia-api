<?php


namespace App\OurEdu\Quizzes\Student\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;
class PeriodicTestQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = Auth::guard('api')->user()->student;
    }

    public function transform(QuizQuestion $periodicTestQuestion)
    {
        $transformedData = [
            'id' => (int) $periodicTestQuestion->id,
            'quiz_id' => (string) $periodicTestQuestion->quiz_id,
            'question_type' => (string) $periodicTestQuestion->question_type,
            'question_text' => (string) $periodicTestQuestion->question_text,
            'question_grade' => (integer) $periodicTestQuestion->question_grade,
            'time_to_solve' => (integer) $periodicTestQuestion->time_to_solve,
            'direction'=>$periodicTestQuestion->quiz->subject->direction,
        ];
        $selectedOptions = array_unique(QuizQuestionAnswer::query()
            ->where('student_id',$this->student->id)
            ->where('question_id',$periodicTestQuestion->id)
            ->where('quiz_id',$periodicTestQuestion->quiz_id)
            ->pluck('option_id')->toArray());
        $options = [];
        foreach ($periodicTestQuestion->options as $option) {
            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                    'isSelected'=>in_array($option->id,$selectedOptions)?true:false
                ];
            $options[] = $optionData;
        }
        $transformedData['options'] = $options;
        return $transformedData;
    }

    public function includeActions(QuizQuestion $quizQuestion)
    {
        $actions = [];

        if (isset($this->params['next'])) {
            $actions[] = [
                'endpoint_url' => $this->params['next'],
                'label' => trans('exam.Next Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::NEXT_QUESTION
            ];
        }

        if (isset($this->params['previous'])) {
            $actions[] = [
                'endpoint_url' => $this->params['previous'],
                'label' => trans('exam.Previous Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::PREVIOUS_QUESTION
            ];
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.periodic-test.get.finish-quiz',
                ['periodicTestId' => $quizQuestion->quiz_id]),
            'label' => trans('exam.Finish Quiz'),
            'method' => 'POST',
            'key' => APIActionsEnums::FINISH_PERIODIC_TEST
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.periodic-test.post.answer',
                ['periodicTestId' => $quizQuestion->quiz_id]),
            'label' => trans('exam.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
