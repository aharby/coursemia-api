<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
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
            'is_active' => (boolean)$subjectFormatSubject->is_active,
            'is_editable' => (boolean)$subjectFormatSubject->is_editable,
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

    /**
     * @param $subject
     * @return \League\Fractal\Resource\Collection
     */
    public function includeActions($subjectFormatSubject)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.viewSubjectFormatSubjectDetails' , [ 'sectionId' => $subjectFormatSubject->id]),
            'label' => trans('subject.View Section Details'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.pause-unpause.subjectFormat',
                ['subjectFormatId' => $subjectFormatSubject->id]),
            'label' => $subjectFormatSubject->is_active ? trans('api.pause') : trans('api.un pause'),
            'method' => 'POST',
            'key' => $subjectFormatSubject->is_active ? APIActionsEnums::PAUSE : APIActionsEnums::UN_PAUSE
        ];

        if(count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


}

