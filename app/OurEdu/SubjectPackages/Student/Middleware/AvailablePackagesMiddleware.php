<?php

namespace App\OurEdu\SubjectPackages\Student\Middleware;

use App\OurEdu\SubjectPackages\Package;
use Closure;

class AvailablePackagesMiddleware
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
        $student = auth()->user()->student;
        $package = Package::where('grade_class_id', $student->class_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('country_id', auth()->user()->country_id)
            ->where('id', $request->route('packageId'))->first();

        if ($package) {
            // if the student already subscribed to the package
            if (!$package->students()->where('packages_subscribed_students.student_id', $student->id)->exists()) {
                return $next($request);
            } else {
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Already subscribed to this subject',
                    'detail' => trans('subject_package.Already subscribed to this package')
                ]);
            }
        } else {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'Cant subscribe to this package',
                'detail' => trans('subject.Cant subscribe to this package')
            ], 403);
        }
    }
}
