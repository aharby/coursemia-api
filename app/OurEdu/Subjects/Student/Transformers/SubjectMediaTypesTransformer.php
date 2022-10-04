<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class SubjectMediaTypesTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [

    ];


    /**
     * @param array $mediaTypeData
     * @return array
     */
    public function transform($mediaTypeData)
    {
        return [
            'id' => Str::uuid(),
            'name' => (string) $mediaTypeData['name'],
            'type' => (string) $mediaTypeData['type'],
            'media_type' => (string) $mediaTypeData['type'],
        ];
    }

    public function includeActions($mediaTypeData)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject-media', ['subjectId' => $mediaTypeData['subject_id'] , 'type' => $mediaTypeData['type']]),
            'label' => trans('subjects.View subject Media'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_SUBJECT_MEDIA
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
