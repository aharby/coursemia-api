<?php


namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\Options\Option;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Exams\Student\Transformers\Questions\CompleteQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\HotspotQuestionTransformer;

class QuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'questionData',
        'exam',
    ];

    protected array $availableIncludes = [
//        'actions'
    ];

    protected $questionsArray;
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;

        // in case of finish exam remove exam relation
        if(Route::currentRouteName() == 'api.student.exams.post.finishExam'){
            $this->defaultIncludes = [
                'actions',
                'questionData',
            ];
        }
    }

    public function transform(ExamQuestion $question)
    {
        $transformerData = [
            'id' => (int)$question->id,
            'slug' => $question->slug,
            'subject_id' => (int)$question->subject_id,
            'question_type' => (string) $question->question_type,
            'subject_format_subject_id' => (int)$question->subject_format_subject_id,
//            'is_correct_answer' => (bool)$question->is_correct_answer,
            'direction'=>$question->exam->subject->direction,
        ];

        if ($question->exam->is_finished)
        {
            $transformerData['student_time_to_solve'] = (int)$question->student_time_to_solve;
        }

        //show learning outcome (for parent / student_teacher / instructor)
        if (Auth::guard('api')->user()->type !== UserEnums::STUDENT_TYPE) {

            $resource = $question->questionable()->get()->first()->resourceSubjectFormatSubject ?? null;
            $parentData = $question->questionable()->get()->first()->parentData ?? null;
            if (is_null($resource) && $parentData){
                $resource = $question->questionable()->get()->first()->parentData->resourceSubjectFormatSubject;
            }
            if($resource){
                $acceptCriteria = $resource->accept_criteria;
                $learningOutcome = json_decode($acceptCriteria)->learning_outcome  ?? 0;
                $transformerData['learning_outcome'] = Option::find($learningOutcome)->title ?? "";
            } else {
                $transformerData['learning_outcome'] = "";
            }
        }

        return $transformerData;
    }

    public function includeActions($question)
    {
        $actions = [];

        if (isset($this->params['is_exam'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.report.post.create', ['subjectId' => $question->subject_id,'reportType'=>ReportEnum::EXAM_QUESTION_TYPE,'id'=>$question->id]),
                'label' => trans('exam.Report Question'),
                'method' => 'POST',
                'key' => APIActionsEnums::REPORT
            ];
        }

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

        $page = request()->input('page') ?? 1;

        if (!$question->exam->is_finished) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.student.exams.post.answer',
                    ['examId' => $question->exam_id, 'page' => $page]
                ),
                'label' => trans('exam.Post answer'),
                'method' => 'POST',
                'key' => APIActionsEnums::POST_ANSWER
            ];
        }

        if (!isset($this->params['disable_actions'])) {
            if (count($actions) > 0) {
                return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
            }
        }
    }

    public function includeExam($question)
    {
        if (!isset($this->params['disable_exam'])) {
            if ($question->exam) {
                return $this->item($question->exam, new ExamTransformer(), 'question_exam');
            }
        }
    }

    public function includeQuestionData(ExamQuestion $question)
    {
        $this->params["is_answer"] = $question->is_answered;

        $data = $question->questionable()->get()->first();
        if ($data){
            $data->time_to_solve = $question->time_to_solve;
            $this->params['student'] = $question->exam->student;
            $this->params['examQuestion'] = $question;
            switch ($question->slug) {

                case LearningResourcesEnums::TRUE_FALSE:
                    return $this->item(
                        $data,
                        new QuestionTrueFalseTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::MULTI_CHOICE:
                    return $this->item(
                        $data,
                        new QuestionMultipleChoiceTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::DRAG_DROP:
                    return $this->item(
                        $data,
                        new QuestionDragDropTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::MATCHING:

                    return $this->item(
                        $data,
                        new QuestionMatchingTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::MULTIPLE_MATCHING:
                    return $this->item(
                        $data,
                        new QuestionMultiMatchingTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::COMPLETE:
                    return $this->item(
                        $data,
                        new CompleteQuestionTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;

                case LearningResourcesEnums::HOTSPOT:
                    return $this->item(
                        $data,
                        new HotspotQuestionTransformer($this->params),
                        ResourceTypesEnums::QUESTION_EXAM_DATA
                    );

                    break;
            }
        }
    }
}
