<?php

namespace App\OurEdu\QuestionReport\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $params;

    protected array $defaultIncludes = [
        'actions',
        'questionReports'

    ];

    protected array $availableIncludes = [
        'subjectFormatSubjects',

    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
        ];
    }

    public function includeSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->questionReportSubjectFormatSubject();

        $subjectFormatSubjects = $subjectFormatSubjects->orderBy('list_order_key')->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer(),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeQuestionReports($subjectFormatSubject) {
        $questionReports = $subjectFormatSubject->questionReport()->notIgnored()->notReported()->get();
        if (count($questionReports)){
            return $this->collection($questionReports, new QuestionReportTransformer(), ResourceTypesEnums::QUESTIONS_REPORT);
        }
    }
    public function includeActions($subjectFormatSubject)
    {
        $actions = [];
        if ($subjectFormatSubject->questionReportSubjectFormatSubject()->count() && !isset($this->params['details'])){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.question.report.get.sections', ['section' => $subjectFormatSubject->id] ),
                'label' => trans('subject.View Section Details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }
}
