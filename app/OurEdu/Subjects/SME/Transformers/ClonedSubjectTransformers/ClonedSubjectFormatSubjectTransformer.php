<?php

namespace App\OurEdu\Subjects\SME\Transformers\ClonedSubjectTransformers;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;

use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCase;
use League\Fractal\TransformerAbstract;

class ClonedSubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $updateSubjectStructuralUseCase;

    private $params;
    protected array $defaultIncludes = [
        'subjectFormatSubjects',
    ];
    protected array $availableIncludes = [

    ];

    public function __construct($params=[])
    {
        $this->params=$params;
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
//        $updateSubjectStructuralUseCase = new UpdateSubjectStructuralUseCase(new SubjectRepository(new Subject()), new GenerateTasksUseCase(new SubjectRepository(new Subject())));
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
//            'parent_subject_format_id' => (int)$subjectFormatSubject->parent_subject_format_id,
//            'is_editable' => count($updateSubjectStructuralUseCase->getSubjectFormatSubjectTasks($subjectFormatSubject)) == 0 ? true : false,
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
                new ClonedSubjectFormatSubjectTransformer(),
                \App\OurEdu\BaseApp\Enums\ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
