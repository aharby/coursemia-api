<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCase;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCase;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $updateSubjectStructuralUseCase;

    private $params;
    protected array $defaultIncludes = [
        'subjectFormatSubjects',
        'resourceSubjectFormatSubjects',
    ];
    protected array $availableIncludes = [

    ];

    public function __construct($params = [])
    {
        $this->params = $params;
        if(isset($this->params['minimal_data']) && $this->params['minimal_data']){
            $this->defaultIncludes = [
//                'subjectFormatSubjects',
           ];
        }
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        $updateSubjectStructuralUseCase = new UpdateSubjectStructuralUseCase(new SubjectRepository(new Subject()), new GenerateTasksUseCase(new SubjectRepository(new Subject())));

        if(isset($this->params['minimal_data']) && $this->params['minimal_data']){
            return [
                'id' => (int)$subjectFormatSubject->id,
                'title' => (string)$subjectFormatSubject->title,
                'subject_type' => (string)$subjectFormatSubject->subject_type,
                'list_order_key' =>$subjectFormatSubject->list_order_key,
            ];
        }

        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'description' => (string)$subjectFormatSubject->description,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
//            'parent_subject_format_id' => (int)$subjectFormatSubject->parent_subject_format_id,
//            'is_editable' => count($updateSubjectStructuralUseCase->getSubjectFormatSubjectTasks($subjectFormatSubject)) == 0 ? true : false,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
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
        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer($this->params),
                \App\OurEdu\BaseApp\Enums\ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeResourceSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $resources = $subjectFormatSubject->resourceSubjectFormatSubject()->orderBy('id', 'asc')->get();
        if (count($resources) > 0) {
            return $this->collection(
                $resources,
                new ResourceSubjectFormatSubjectTransformer($this->params),
                \App\OurEdu\BaseApp\Enums\ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
