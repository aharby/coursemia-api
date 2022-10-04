<?php


namespace App\OurEdu\Courses\Student\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Instructor\Transformers\CourseMediaTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseMediaRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;

class CoursesMediaApiController extends BaseApiController
{
    public function __construct(
        public CourseMediaRepositoryInterface $courseMediaRepository
    )
    {
    }

    public function listCourseMedia(Course $course = null)
    {
        $this->setFilters();
        $data = $this->courseMediaRepository->getStudentCoursesMedia($course);
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude($data, '', new CourseMediaTransformer(), ResourceTypesEnums::COURSE_MEDIA, $meta);
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'extension',
            'type' => 'select',
            'data' => MediaEnums::getCourseMediaTypes(),
            'trans' => false,
            'value' => request()->get('extension'),
        ];

        $this->filters[] = [
            'name' => 'course_name',
            'type' => 'select',
            'data' => auth()->user()->student?->courses->pluck('name','id')->toArray(),
            'trans' => false,
            'value' => request()->get('course_name'),
        ];

        $this->filters[] = [
            'name' => 'from',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('from'),
        ];

        $this->filters[] = [
            'name' => 'to',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('to'),
        ];
    }

}
