<?php

namespace App\OurEdu\GeneralQuizzes\Student\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class LookUpTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'subjects',
        'generalQuizTypes'
    ];


    public function transform(): array
    {
        return [
            'id' => Str::uuid(),
        ];
    }

    public function includeSubjects(): ?Collection
    {
        $student = Auth::user()->student;

        if ($student) {
            $subjects = $student->subjects;

            return $this->collection($subjects, new SubjectLookUpTransformer(), ResourceTypesEnums::SUBJECT);
        }

        return null;
    }

    public function includeGeneralQuizTypes(): ?Collection
    {
        $types = GeneralQuizTypeEnum::studentShowResultFilters();

        if (count($types)) {
            $typesArray = [];
            $type =[];
            foreach ($types as $key => $value) {
                $type['key'] = $key;
                $type['type'] = $value;

                $typesArray[] = $type;
            }

            return $this->collection($typesArray, new GeneralQuizTypeTransformer(), ResourceTypesEnums::GENERAL_QUIZ_TYPE);
        }

        return null;
    }
}
