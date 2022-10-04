<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\Subjects\Instructor\Transformers\ListSubjectsTransformer;
use App\OurEdu\Users\Enums\AvailableEnum;
use App\OurEdu\Users\Models\Instructor;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserTransformer;

class InstructorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'user',
        'subjects'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Instructor $instructor)
    {
        $transformedData = [
            'id' => $instructor->id,
            'about_instructor' => $instructor->about_instructor,
            'hire_date' => $instructor->hire_date,
            'school_id' => $instructor->school_id
        ];

        return $transformedData;
    }

    public function includeUser($instrucor)
    {
        if ($instrucor->user) {
            return $this->item($instrucor->user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeSubjects($instrucor)
    {
        if ($instrucor->user->subjects()->count()) {
            if (isset($this->params['subjects_limit'])) {
                // return all
                return $this->collection(
                    $instrucor->user->subjects()->get(),
                    new ListSubjectsTransformer(),
                    ResourceTypesEnums::SUBJECT
                );
            }
            // paginate only 3 subjects
            return $this->collection(
                $instrucor->user->subjects()->paginate(AvailableEnum::SUBJECT_LIMIT, ['*'], 'subjects_page'),
                new ListSubjectsTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }
    }

}
