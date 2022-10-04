<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Transformers;

use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SectionTransformer extends  TransformerAbstract
{
    protected array $defaultIncludes = [

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
            'has_sections' => (bool)$subjectFormatSubject->childSubjectFormatSubject()->exists(),
        ];
    }
}

