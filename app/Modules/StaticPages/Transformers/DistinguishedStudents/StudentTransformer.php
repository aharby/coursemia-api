<?php

namespace App\Modules\StaticPages\Transformers\DistinguishedStudents;


use App\Modules\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class StudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;

    }

    public function transform(Student $student)
    {
        $transformedData = [
            'id' => $student->id,
            'first_name' => (string) $student->user->first_name,
            'last_name' => (string) $student->user->last_name,
            'language' =>(string) $student->user->language,
            'mobile' => (string) $student->user->mobile,
            'profile_picture' => (string) imageProfileApi($student->user->profile_picture),
            'user-type' => (string) $student->user->type,
            'user_type' => (string) $student->user->type,
            'email' => (string) $student->user->email,
            'country_id' => $student->user->country_id,
            'name' => $student->user->name,
            'birth_date' => $student->birth_date,
            'educational_system_id' => $student->educational_system_id,
            'school_id' => $student->school_id,
            'class_id' => $student->class_id,
            'academical_year_id' => $student->academical_year_id,
        ];
        return $transformedData;
    }

}
