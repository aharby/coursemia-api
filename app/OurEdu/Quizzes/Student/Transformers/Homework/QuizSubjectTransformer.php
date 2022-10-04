<?php

namespace App\OurEdu\Quizzes\Student\Transformers\Homework;

use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class QuizSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    protected $params;
    protected $student;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Subject $subject)
    {
        $transformedData = [
            'id' => (int) $subject->id,
            'name' => (string) $subject->name,
            'image' => (string) $subject->image,
        ];

        return $transformedData;
    }
}
