<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions;

use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionHotspotTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\CompetitionQuestionCompleteTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMultipleChoiceTransformer;

class CompetitionQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
        'actions'
    ];
    protected array $availableIncludes = [
        'competition',
    ];
    protected $questionsArray;
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ExamQuestion $question)
    {
        $transformerData = [
            'id' => (int)$question->id,
            'slug' => $question->slug,
            'subject_id' => (int)$question->subject_id,
            'question_type' => (string) $question->question_type,
            'subject_format_subject_id' => (int)$question->subject_format_subject_id,
            'time_to_solve' => (int)$question->time_to_solve ??  env('TIME_TO_SOLVE_QUESTION',30),
            'direction'=>$question->exam->subject->direction,
        ];

        if (isset($this->params['is_answer'])) {
            $isCorrectAnswer = ($question->competitionQuestionStudents()
                    ->where('student_id', $student = auth()->user()->student->id)->first())->is_correct_answer ?? 0;

            $transformerData['is_correct_answer'] = (bool)$isCorrectAnswer;
        }

        if ($question->exam->is_finished)
        {
            $transformerData['student_time_to_solve'] = (int)$question->student_time_to_solve;
        }

        return $transformerData;
    }

    public function includeActions($question)
    {
        $actions = [];

        if (isset($this->params['actions'])) {
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

            if (isset($this->params['finish_competition'])) {
                $actions[] = [

                    'endpoint_url' => buildScopeRoute('api.student.competitions.post.finishCompetition', ['competitionId' => $question->exam_id]),
                    'label' => trans('exam.Finish competition'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::FINISH_COMPETITION
                ];
            }
            if (!$question->exam?->is_finished){
                $page = request()->input('page') ?? 1;
                $actions[] = [
                    'endpoint_url' => buildScopeRoute(
                        'api.student.competitions.post.answer',
                        ['competitionId' => $question->exam_id, 'page' => $page]
                    ),
                    'label' => trans('exam.Post answer'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::POST_ANSWER
                ];
            }
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeQuestionData(ExamQuestion $question)
    {
        $data = $question->questionable()->get()->first();
        $this->params['exam_id']=$question->exam_id;
        $this->params['student'] = auth()->user()->student;
        $this->params['examQuestion'] = $question;
        $isAnswered  = false;
        $answers = null;
        if($answers = $question->competitionQuestionStudents()->where('student_id','=', auth()->user()->student->id)->first()
            and $question->answers()->where('student_id','=', auth()->user()->student->id)->first())  {
                $isAnswered  = true;
        }

        $this->params["answers"]  = $answers;
        $this->params["is_answered"] = $isAnswered;

        switch ($question->slug) {
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $data,
                    new CompetitionQuestionTrueFalseTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::MULTI_CHOICE:

                return $this->item(
                    $data,
                    new CompetitionQuestionMultipleChoiceTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $data,
                    new CompetitionQuestionDragDropTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    $data,
                    new CompetitionQuestionMatchingTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $data,
                    new CompetitionQuestionMultiMatchingTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $data,
                    new CompetitionQuestionCompleteTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::HOTSPOT:
                return $this->item(
                    $data,
                    new CompetitionQuestionHotspotTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

        }
    }

    public function includeCompetition(ExamQuestion $question)
    {
        return $this->item($question->exam, new CompetitionTransformer($this->params), ResourceTypesEnums::COMPETITION);
    }
}
