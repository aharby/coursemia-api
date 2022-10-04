<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SingleSectionTransformer extends TransformerAbstract
{

    private $params;
    protected array $defaultIncludes = [
        'subjectFormatSubjects',
        'resourceSubjectFormatSubjects',
        'actions',
    ];
    protected array $availableIncludes = [

    ];

    public function __construct($params = [])
    {
        $this->params = $params;
        if(isset($this->params['minimal_data']) && $this->params['minimal_data']) {
            $this->defaultIncludes = [
                'subjectFormatSubjects',
            ];
        }
        if(isset($this->params['no_childs']) && $this->params['no_childs']){
            $this->defaultIncludes = [];
        }
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
//        if(isset($this->params['minimal_data']) && $this->params['minimal_data']){
//            return [
//                'id' => (int)$subjectFormatSubject->id,
//                'title' => (string)$subjectFormatSubject->title,
//                'subject_type' => (string)$subjectFormatSubject->subject_type,
//            ];
//        }

        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'description' => (string)$subjectFormatSubject->description,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
            'direction'=>$subjectFormatSubject->subject->direction,
        ];
    }


    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }


    public function includeSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('id', 'asc')->get();
        $this->params['minimal_data'] = true;
        $this->params['no_childs'] = true;

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SingleSectionTransformer($this->params),
                \App\OurEdu\BaseApp\Enums\ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeResourceSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $resources = $subjectFormatSubject->resourceSubjectFormatSubject()->orderBy('id', 'asc')->get();
        if (count($resources) > 0) {
            $this->params['minimal_data'] = true;
            $this->params['without_accept_criteria'] = true;
            return $this->collection(
                $resources,
                new ResourceSubjectFormatSubjectTransformer($this->params),
                \App\OurEdu\BaseApp\Enums\ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeActions($subjectFormatSubject)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.create-resource-structural', ['section' => $subjectFormatSubject->id]),
            'label' => trans('app.create resource structure'),
            'method' => 'PUT',
            'key' => APIActionsEnums::CREATE_RESOURCE
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

}
