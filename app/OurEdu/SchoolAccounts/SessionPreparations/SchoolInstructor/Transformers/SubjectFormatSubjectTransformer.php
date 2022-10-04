<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['actions' , 'children'];

    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'has_sub_sections' => (boolean)$subjectFormatSubject->childSubjectFormatSubject()->count() > 0 ? true : false,
            'has_parent' => $subjectFormatSubject->parentSubjectFormatSubject ? true : false,
            'parent_id' => $subjectFormatSubject->parentSubjectFormatSubject?->id ?? false,

        ];
    }

    public function includeActions(SubjectFormatSubject $subjectFormatSubject)
    {
        $parent = $subjectFormatSubject->parentSubjectFormatSubject;
        if($parent){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.sessionPreparation.view.sections.subsections',
                    [
                        'section' => $parent,
                    ]),
                'label' => trans('app.parent section'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_PARENT_SUBJECT_SECTIONS
            ];
        }else{
            $parent = $subjectFormatSubject->subject;
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.sessionPreparation.view.subject.sections',
                    [
                        'subject' => $parent,
                    ]),
                'label' => trans('app.Subject'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT
            ];
        }


        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);

    }

    public function includeChildren(SubjectFormatSubject $subjectFormatSubject)
    {
        $this->setDefaultIncludes(['actions' , 'children']);

        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('list_order_key', 'ASC')->get();
        return $this->collection($subjectFormatSubjects , $this, ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }
}
