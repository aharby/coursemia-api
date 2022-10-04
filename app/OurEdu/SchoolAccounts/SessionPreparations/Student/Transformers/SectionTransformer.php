<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\Student\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

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
