<?php

namespace App\OurEdu\Courses\Middleware;

use App\OurEdu\Courses\Models\Course;
use Closure;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isInstanceOf;

class CheckCourseAvailabiltyForInstructorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $course = $request->route('course');
        if ($course) {
            if (auth()->id() != $course->instructor_id) {
                return formatErrorValidation(
                    [
                        'status' => 401,
                        'title' => 'unauthorized_action',
                        'detail' => trans('app.You dont have permission to edit')
                    ]
                );
            }
        }

        return $next($request);
    }
}
