<?php

namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class LookUpTransformer extends TransformerAbstract
{
    public function __construct(private User $user)
    {
    }

    protected array $availableIncludes = ['courses'];


    public function transform()
    {
        return [
            'id' => Str::uuid()
        ];
    }

    public function includeCourses()
    {
        $courses = Course::query()
            ->where('instructor_id', "=", $this->user->id)
            ->orderByDesc("start_date")
            ->paginate(env("PAGE_LIMIT", 20));
        return $this->collection($courses , new CourseTransformer(), ResourceTypesEnums::COURSE);
    }
}
