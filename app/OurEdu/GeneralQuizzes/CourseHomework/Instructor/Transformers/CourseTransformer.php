<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers;

use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Courses\Enums\CourseSessionEnums;

class CourseTransformer extends TransformerAbstract
{

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function transform(Course $course)
    {
        $currency_code = $this->user->student->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => (int) $course->id,
            'name' => (string) $course->name,
            'description' => (string) $course->description,
            'course-type' => (string) $course->type,
            'instructor_id' => (int) $course->instructor_id,
            'subject_id' => (int) $course->subject_id,
            'subscription_cost' => (string) $course->subscription_cost .' '.$currency_code,
            'start_date' => (string) $course->start_date,
            'end_date' => (string) $course->end_date,
            'picture' => (string) imageProfileApi($course->picture, 'large'),
            'number_of_sessions' => $course->sessions()->where('status', '!=', CourseSessionEnums::CANCELED)->count(),
        ];

        return $transformedData;
    }
}
