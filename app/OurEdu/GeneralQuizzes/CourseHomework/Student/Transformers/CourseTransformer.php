<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers;

use App\OurEdu\Users\UserEnums;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionTransformer;
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
            'number_of_sessions'    =>  $course->sessions()->where('status', '!=', CourseSessionEnums::CANCELED)->count(),
        ];


        return $transformedData;
    }
}
