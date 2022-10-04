<?php


namespace App\OurEdu\Exams\Student\Transformers\InstructorCompetitions;

use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionCompleteTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionHotspotTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionMultipleChoiceTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions\InstructorCompetitionQuestionTrueFalseTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;

class InstructorCompetitionQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
        'actions',
    ];
    protected array $availableIncludes = [
        'competition',
    ];
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

        ];

        if ($question->exam->is_finished)
        {
            $transformerData['student_time_to_solve'] = (int)$question->student_time_to_solve;
        }
        if (isset($this->params['is_answer'])) {
            $isCorrectAnswer = ($question->instructorCompetitionQuestionStudents()
                    ->where('student_id', $student = auth()->user()->student->id)->first())->is_correct_answer ?? 0;
            $transformerData['is_correct_answer'] = (bool)$isCorrectAnswer;
        }
        return $transformerData;
    }

    public function includeActions($question)
    {
        $actions = [];

        if (isset($this->params['enable_actions'])) {
            if (isset($this->params['next'])) {
                $actions[] = [
                    'endpoint_url' => $this->params['next'],
                    'label' => trans('exam.Next Question'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::NEXT_QUESTION
                ];
            }
            $page = request()->input('page') ?? 1;

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.student.instructorCompetitions.post.answer',
                    ['competitionId' => $question->exam_id, 'page' => $page]
                ),
                'label' => trans('exam.Post answer'),
                'method' => 'POST',
                'key' => APIActionsEnums::POST_ANSWER
            ];
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
        if($answers = $question->instructorCompetitionQuestionStudents()->where('student_id','=', auth()->user()->student->id)->first()
            and $question->answers()->where('student_id','=', auth()->user()->student->id)->first())  {
            $isAnswered  = true;
        }

        $this->params["answers"]  = $answers;
        $this->params["is_answered"] = $isAnswered;

        switch ($question->slug) {

            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionTrueFalseTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::MULTI_CHOICE:

                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionMultipleChoiceTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionDragDropTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionMatchingTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionMultiMatchingTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionCompleteTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::HOTSPOT:
                return $this->item(
                    $data,
                    new InstructorCompetitionQuestionHotspotTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;


        }
    }

    public function includeCompetition(ExamQuestion $question)
    {
        return $this->item($question->exam, new InstructorCompetitionTransformer($this->params), ResourceTypesEnums::COMPETITION);
    }
}
