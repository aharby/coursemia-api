<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class BreadcrumbsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    private $subject;
    private $section;

    public function transform($data)
    {

        // first index is subject id
        if($data['index'] == 0 ){
            $this->setCurrentSubject(Subject::find($data['id']));
            $transformedData = [
                'id' => $this->subject->id,
                'order' => $data['index'] + 1 ,
                'name' =>  $this->subject->name,
                'origin_type' => 'subject',
            ];
        }else{
            //else all other indexes are of parent sections
            $this->setCurrentSection(SubjectFormatSubject::find($data['id']));
            $transformedData = [
                'id' => $this->section->id,
                'order' => $data['index'] + 1 ,
                'name' =>  $this->section->title,
                'origin_type' => 'section',
            ];
        }

        return $transformedData;

    }

    public function includeActions($data)
    {
        $actions = [];
        if($data['index'] == 0 ) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject', ['subjectId' => $this->subject->id]),
                'label' => trans('subject.subject'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT
            ];
        }else{
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.viewSubjectFormatSubjectDetails', ['sectionId' => $this->section->id]),
                'label' => trans('subject.View Section Sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
            ];
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function setCurrentSubject(Subject $subject){
        $this->subject = $subject;
    }

    public function setCurrentSection(SubjectFormatSubject $section){
        $this->section = $section;
    }
}
