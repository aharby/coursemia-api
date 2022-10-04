<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\Exams\Enums\PublishGeneralExamEnum;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class GeneralExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'preparedQuestions',
        'questions',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'uuid' => $exam->uuid,
            'name' => $exam->name,
            'class' => $exam->subject->gradeClass->title ??'',
            'difficulty_level' => $exam->difficultyLevel->title ?? '',
            'difficulty_level_id' => $exam->difficultyLevel->id ?? null,
            'subject_id' => $exam->subject_id,
            'date' => $exam->date,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
            'is_published' => (bool)$exam->published_at,
            'published_at' => (string)$exam->published_at,
            'created_at' => (string)$exam->created_at,
            'prepared_questions_ids' =>  $exam->preparedQuestions()->pluck('id')->toArray(),
            'subject_format_subjects' => json_decode($exam->subject_format_subjects),

            'prepared_questions_pagination' =>  $exam->preparedQuestions()->paginate(10, ['id'], 'question-page'),
            'questions_pagination' =>  $exam->questions()->paginate(10, ['id'], 'question-page'),

        ];

//        $array= (new Paginator($exam->preparedQuestions, 10,[
//            'path' =>request()->url(),
//            'query' => request()->query(),
//        ]))->toArray();
//        dd($array);
        if ((bool)$exam->published_at) {
            $dynamicLinkUrl = new PublishGeneralExamEnum($exam->id);
            $dynamicLinkUrl = $dynamicLinkUrl->getTypeLink(UserEnums::STUDENT_TYPE);
            $transformerDatat['url'] = $dynamicLinkUrl;

            $transformerDatat['dynamic_link_url'] = $dynamicLinkUrl;
        }

        return $transformerDatat;
    }


    public function includeActions(GeneralExam $exam)
    {
        $actions = [];

        if (!$exam->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams.update', ['exam' => $exam]),
                'label' => trans('app.Edit'),
                'method' => 'POST',
                'key' => APIActionsEnums::EDIT_GENERAL_EXAM
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams.updateQuestions', ['exam' => $exam]),
                'label' => trans('app.Update questions'),
                'method' => 'POST',
                'key' => APIActionsEnums::EDIT_GENERAL_EXAM_QUESTIONS
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams.getSubjectSections', ['exam' => $exam->id, 'subject' => $exam->subject]),
                'label' => trans('app.View Exam Subject Sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_SECTIONS
            ];

            if (now()->lte(Carbon::parse($exam->date . ' ' . $exam->start_time))) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.sme.general_exams.publish', ['exam' => $exam]),
                    'label' => trans('app.Publish'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::PUBLISH_GENERAL_EXAM
                ];
            }
        }

        if (now()->lte(Carbon::parse($exam->date . ' ' . $exam->start_time))) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams.delete', ['exam' => $exam]),
                'label' => trans('app.Delete'),
                'method' => 'GET',
                'key' => APIActionsEnums::DELETE_GENERAL_EXAM
            ];
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includePreparedQuestions(GeneralExam $exam)
    {
        if ($exam->preparedQuestions()->count() > 0) {
            $questions = $exam->preparedQuestions()->paginate(10, ['*'], 'question-page');
            return $this->collection($questions, new PreparedGeneralExamQuestionTransformer($exam,[]), ResourceTypesEnums::PREPARED_GENERAL_EXAM_QUESTION);
        }
    }

    public function includeQuestions(GeneralExam $exam)
    {
        if ($exam->questions()->count() > 0) {
            $questions = $exam->questions()->paginate(10, ['*'], 'question-page');

            return $this->collection($questions, new GeneralExamQuestionSMETransformer($exam,[]), ResourceTypesEnums::GENERAL_EXAM_QUESTION);
        }
    }
}
