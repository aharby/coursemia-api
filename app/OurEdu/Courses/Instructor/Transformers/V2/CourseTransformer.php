<?php

namespace App\OurEdu\Courses\Instructor\Transformers\V2;

use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Instructor\Transformers\ScheduleSubjectTransformer;

class CourseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
        'sessions',
        'subject'
    ];

    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    public function transform(Course $course)
    {
        $curencyCode = $course->subject->educationalSystem->country->currency ?? '';
         $transformedData = [
            'id' => (int) $course->id,
            'name' => (string) $course->name,
            'description' => (string) $course->description,
            'course-type' => (string) $course->type,
            'instructor_id' => (int) $course->instructor_id,
            'instructor_name' => (string) $course->instructor->name,
            'subject_id' => (int) $course->subject_id,
            'subscription_cost' =>(float) $course->subscription_cost . " " . $curencyCode,
            'start_date' => (string) $course->start_date,
            'end_date' => (string) $course->end_date,
            'picture' => (string) imageProfileApi($course->picture, 'large'),
            'is_active' => (boolean) $course->is_active,
            'number_of_sessions'    =>  $course->sessions()->count(),
            "subject_name" => (string)($course->subject->name ?? ""),
            "educational_system" => (string)($course->subject->educationalSystem->name ?? ""),
            "educational_term" => (string)($course->subject->educationalTerm->title ?? ""),
            "educational_grade" => (string)($course->subject->gradeClass->title ?? ""),
        ];


        return $transformedData;
    }

    public function includeSessions(Course $course)
    {
        if(count($this->params['courseSessions'])>0)
        {
            $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);
            return $this->collection($this->params['courseSessions'], new CourseSessionTransformer($token), ResourceTypesEnums::COURSE_SESSION);
        }
    }
    public function includeSubject(Course $course)
    {
        if ($course->subject) {
            return $this->item($course->subject, new ScheduleSubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }
}
