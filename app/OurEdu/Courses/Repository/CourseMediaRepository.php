<?php

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CourseMediaRepository implements CourseMediaRepositoryInterface
{
    private $model;

    public function __construct(CourseMedia $courseMedia)
    {
        $this->model = $courseMedia;
    }

    public function getInstructorCoursesMedia(Course $course = null)
    {
        $query = $this->model->where(
            function (Builder $q) use ($course) {
                if (isset($course)) {
                    $q->where('course_id', "=", $course->id);
                    return;
                }

                $q->whereIn('course_id', auth()->user()->courses()->pluck('id'));
            }
        );

        if (\request()->filled("from")) {
            $query->where("created_at", '>=', Carbon::parse(\request()->get("from"))->format('Y-m-d 00:00:00'));
        };

        if (\request()->filled("to")) {
            $query->where("created_at", '<=', Carbon::parse(\request()->get("to"))->format('Y-m-d 23:59:59'));
        };

        if (\request()->filled("extension")) {
            $query->where("extension", \request()->get("extension"));
        };

        if (\request()->filled("course_name")) {
            $query->whereHas('course', function ($q) {
                $q->where('name', 'LIKE', '%' . request('course_name') . '%');
            });
        };
        return $query->orderBy('id', 'desc')->paginate(env("PAGE_LIMIT", 20));
    }

    public function getStudentCoursesMedia(Course $course = null)
    {
        $query = $this->model->where('active', 1)
            ->where(
                function (Builder $q) use ($course) {
                    if (isset($course)) {
                        $q->where('course_id', "=", $course->id);
                    }

                    $q->whereIn('course_id', auth()->user()->student->courses()->pluck('id'));
                }
            );

        if (\request()->filled("from")) {
            $query->where("created_at", '>=', Carbon::parse(\request()->get("from"))->format('Y-m-d 00:00:00'));
        };

        if (\request()->filled("to")) {
            $query->where("created_at", '<=', Carbon::parse(\request()->get("to"))->format('Y-m-d 23:59:59'));
        };

        if (\request()->filled("extension")) {
            $query->where("extension", \request()->get("extension"));
        };

        if (\request()->filled("course_name")) {
            $query->whereHas('course', function ($q) {
                $q->where('name', 'LIKE', '%' . request('course_name') . '%');
            });
        };
        return $query->orderBy('id', 'desc')->paginate(env("PAGE_LIMIT", 20));
    }

    public function toggleStatus(CourseMedia $courseMedia): ?CourseMedia
    {
        $courseMedia->update(
            [
                'active' => !$courseMedia->active
            ]
        );

        return $courseMedia;
    }

}
