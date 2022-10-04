<?php

namespace App\OurEdu\LandingPage\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CoursesTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'instructor',
        'subject'
    ];
    public function transform(Course $course): array
    {
        $currencyCode = $course->subject->educationalSystem->country->currency ?? trans('app.SAR Currency');
        return [
            'id' => $course->id,
            'name' => $course->name,
            'picture' => (string) imageProfileApi($course->picture, 'large'),
            'subscription_cost' => $course->subscription_cost . " " . $currencyCode,
            'medium_picture' => (string) imageProfileApi($course->medium_picture, 'large'),
            'small_picture' => (string) imageProfileApi($course->small_picture, 'large'),
        ];
    }

    public function includeInstructor(Course $course): ?Item
    {
        if ($course->instructor) {
            return $this->item($course->instructor, new UserTransformer(), ResourceTypesEnums::USER);
        }

        return null;
    }

    public function includeSubject(Course $course): ?Item
    {
        if ($course->subject) {
            return $this->item($course->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }

        return null;
    }
}
