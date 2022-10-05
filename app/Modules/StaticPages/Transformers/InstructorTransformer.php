<?php


namespace App\Modules\StaticPages\Transformers;

use App\Modules\Users\User;
use League\Fractal\TransformerAbstract;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Courses\Models\SubModels\CourseStudent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Courses\Transformers\CourseDetailsTransformer;

class InstructorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'courses'
    ];

    public function __construct(private LengthAwarePaginator $courses)
    {

    }

    public function transform(User $instructor)
    {
        $transformData = [
            'id' => (int) $instructor->id,
            'name' => (string) $instructor->name,
            'profile_picture' => (string) imageProfileApi($instructor->profile_picture),
            'about' => $instructor->instructor?->about_instructor,
            'average_rating' => (string) $instructor->avgRating(),
            'reviews' => (int) $instructor->ratings()->count(),
            'comments' =>   $instructor->ratings()->select('comment')->take(5)->get(),
            'courses_count' => $instructor->courses()->count(),
            'student_count' => $this->getStudentsCount($instructor)
        ];

        $transformData['pagination'] = (object)[
            'per_page' => $this->courses->perPage(),
            'total' => $this->courses->total(),
            'current_page' => $this->courses->currentPage(),
            'count' => $this->courses->count(),
            'total_pages' => $this->courses->lastPage(),
            'next_page' => $this->courses->nextPageUrl(),
            'previous_page' => $this->courses->previousPageUrl()
        ];

        return $transformData;
    }

    public function includeCourses(User $instructor)
    {

      return $this->collection($this->courses, new CourseDetailsTransformer($instructor), ResourceTypesEnums::COURSE);

    }

    private function getStudentsCount($instructor)
    {
       $countStudents = CourseStudent::query()->where('instructor_id', $instructor->id)->distinct('student_id')->count('student_id');

      return $countStudents;
    }

}

