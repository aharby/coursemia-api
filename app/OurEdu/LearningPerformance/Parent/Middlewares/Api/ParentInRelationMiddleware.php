<?php


namespace App\OurEdu\LearningPerformance\Parent\Middlewares\Api;

use App\OurEdu\Users\Models\Student;
use Closure;

class ParentInRelationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $parent = auth()->user();
        $student = Student::findOrFail($request->studentId);
        $inRelation = $parent->students()
                        ->where('student_id', $student->user->id)
                        ->exists();
        if ($inRelation) {
            return $next($request);
        }
        return unauthorize();
    }
}
