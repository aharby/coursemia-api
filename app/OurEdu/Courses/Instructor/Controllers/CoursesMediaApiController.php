<?php


namespace App\OurEdu\Courses\Instructor\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Instructor\Transformers\CourseMediaTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseMedia;
use App\OurEdu\Courses\Repository\CourseMediaRepositoryInterface;
use App\OurEdu\Courses\UseCases\CourseMediaUseCase\CourseMediaUseCaseInterface;
use App\OurEdu\GarbageMedia\MediaEnums;
use Illuminate\Http\Request;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class CoursesMediaApiController extends BaseApiController
{
    public function __construct(
        public CourseMediaUseCaseInterface    $courseMediaUseCase,
        public CourseMediaRepositoryInterface $courseMediaRepository,
        public ParserInterface                $parserInterface
    )
    {
    }

    public function listCourseMedia(Course $course = null)
    {
        $this->setFilters();
        $data = $this->courseMediaRepository->getInstructorCoursesMedia($course);
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude($data, 'actions', new CourseMediaTransformer($course), ResourceTypesEnums::COURSE_MEDIA, $meta);
    }

    public function attacheMediaToCourse(Request $request, Course $course)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCaseResponse = $this->courseMediaUseCase->attache($data->medias, $course);

        if ($useCaseResponse['status'] != 200) {
            return formatErrorValidation($useCaseResponse);
        }
        $medias = $course->media()->latest()->take(count($data->medias))->get();
        return $this->transformDataModInclude($medias, 'actions', new CourseMediaTransformer(), ResourceTypesEnums::COURSE_MEDIA);
    }
    public function detachMediaFromCourse(Request $request, Course $course)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $this->courseMediaUseCase->detach($data->medias, $course);
        return  [
            "status" => 200,
            'title' => "detach media from course",
            'detail' => trans('general_quizzes.detach media from course successfully'),
        ];
    }

    public function toggleMediaStatus(CourseMedia $media)
    {
        $media = $this->courseMediaRepository->toggleStatus($media);

        return $this->transformDataMod($media, new CourseMediaTransformer(), ResourceTypesEnums::COURSE_MEDIA);
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
            'data' => auth()->user()->courses->pluck('name','id')->toArray(),
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
