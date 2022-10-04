<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class ListSubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $params;
    private $exam;
    private $preparedQuestionsQuery;

    protected array $defaultIncludes = [
        'actions',
//        'resourceSubjectFormatSubjects',
        'subjectFormatSubjects'
    ];

    protected array $availableIncludes = [
    ];

    public function __construct(GeneralExam $exam,$params = [])
    {
        $this->params = $params;
        $this->exam = $exam;
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {

        $childrenIds = getSectionChilds($subjectFormatSubject);
        $this->preparedQuestionsQuery = PreparedGeneralExamQuestion::whereIn('subject_format_subject_id' , $childrenIds)
            ->where('difficulty_level_id',$this->exam->difficulty_level_id);
        // ->when(request('difficulty_level_id') , function ($query) {
        //     $query->where('difficulty_level_id' , request('difficulty_level_id') );
        // });

        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'questions_count' => (int) $this->preparedQuestionsQuery->count(),
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
        ];
    }

    public function includeSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('id', 'asc')->get();
        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new ListSubjectFormatSubjectTransformer( $this->exam,$this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

//    public function includeResourceSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
//    {
//        $resources = $subjectFormatSubject->resourceSubjectFormatSubject()->orderBy('id', 'asc')->get();
//        if (count($resources) > 0) {
//            return $this->collection(
//                $resources,
//                new ResourceSubjectFormatSubjectTransformer($this->params),
//                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
//            );
//        }
//    }

    public function includeActions($subjectFormatSubject)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.general_exams.getSectionQuestions', ['exam' => $this->exam->id,'section' => $subjectFormatSubject]) . '?' . request()->getQueryString(),
            'label' => trans('general_exams.Section Questions'),
            'method' => 'GET',
            'key' => APIActionsEnums::AVAILABLE_SECTION_QUESTIONS
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
