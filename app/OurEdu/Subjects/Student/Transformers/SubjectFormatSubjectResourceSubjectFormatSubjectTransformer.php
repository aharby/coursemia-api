<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects' ,
        'resourceSubjectFormatSubject',
        'breadcrumbs'
    ];

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'description' => (string)$subjectFormatSubject->description,
            'list_order_key' => $subjectFormatSubject->list_order_key,
            'direction'=>$subjectFormatSubject->subject->direction,
            'has_children' => $subjectFormatSubject->childSubjectFormatSubject->count() > 0 ? true :false,
            'has_resources' => $subjectFormatSubject->resourceSubjectFormatSubject->count() > 0 ? true : false,
            'progress' => calculateSectionProgress($subjectFormatSubject),
        ];
    }

    /**
     * @param $subject
     * @return \League\Fractal\Resource\Collection
     */
    public function includeActions($subjectFormatSubject)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.report.post.create', ['subjectId' => $subjectFormatSubject->subject->id,'reportType'=>ReportEnum::SECTION_TYPE,'id'=>$subjectFormatSubject->id]),
            'label' => trans('subject.Report'),
            'method' => 'POST',
            'key' => APIActionsEnums::REPORT
        ];
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSubjectFormatSubjects($subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject
            ->childSubjectFormatSubject()
            ->orderBy('list_order_key', 'ASC')
            ->doesntHave('activeReportTasks')
            ->doesntHave('activeTasks')
            ->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer(),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeResourceSubjectFormatSubject($subjectFormatSubject)
    {
        $resourceSubjectFormatSubject = $subjectFormatSubject->resourceSubjectFormatSubject()
            ->orderBy('list_order_key', 'ASC')
            ->whereIn('resource_slug', LearningResourcesEnums::getNotQuestionResources())
            ->doesntHave('activeReportTasks')
            ->doesntHave('activeTasks')
            ->get();

        if (count($resourceSubjectFormatSubject) > 0) {
            return $this->collection(
                $resourceSubjectFormatSubject,
                new ResourceSubjectFormatSubjectTransformer,
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }


    public function includeBreadcrumbs($subjectFormatSubject)
    {
        $parentSctionsIds = getBreadcrumbsIds($subjectFormatSubject,[]);

        return $this->collection(
            $parentSctionsIds,
                new BreadcrumbsTransformer(),
                ResourceTypesEnums::BREADCRUMB
            );
    }
}
