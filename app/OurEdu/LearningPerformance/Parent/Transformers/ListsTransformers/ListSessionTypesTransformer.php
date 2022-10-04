<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ListSessionTypesTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'course'
    ];

    public function transform($data)
    {
        $transformerData = [
            'id' => Str::uuid(),
            'vcr_session_type' => $data['type'],
            'count' => $data['count'],
        ];
        return $transformerData;
    }
    public function includeCourse($data)
    {
        $courses =  $data['courses'];

        return $this->collection($courses, new CoursesListTransformer($data['student']), ResourceTypesEnums::COURSE);
    }
}
