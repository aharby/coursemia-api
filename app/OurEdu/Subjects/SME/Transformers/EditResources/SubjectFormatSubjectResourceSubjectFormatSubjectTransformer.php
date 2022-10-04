<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;
use League\Fractal\TransformerAbstract;

class SubjectFormatSubjectResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects' ,
        'resourceSubjectFormatSubject'
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
            'list_order_key' => $subjectFormatSubject->list_order_key,
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
            'endpoint_url' => buildScopeRoute('api.sme.subjects.pause-unpause.subjectFormat',
                                    ['subjectFormatId' => $subjectFormatSubject->id]),
            'label' => $subjectFormatSubject->is_active ? trans('api.pause') : trans('api.un pause'),
            'method' => 'POST',
            'key' => $subjectFormatSubject->is_active ? APIActionsEnums::PAUSE : APIActionsEnums::UN_PAUSE
        ];
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSubjectFormatSubjects($subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('list_order_key', 'ASC')->get();;

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
        $resourceSubjectFormatSubject = $subjectFormatSubject->resourceSubjectFormatSubject()->orderBy('list_order_key', 'ASC')->get();

        if (count($resourceSubjectFormatSubject) > 0) {
            return $this->collection(
                $resourceSubjectFormatSubject,
                new ResourceSubjectFormatSubjectTransformer(),
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
