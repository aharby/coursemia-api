<?php

namespace App\OurEdu\Subjects\Middleware;

use App\OurEdu\BaseApp\Middleware\CheckUsageMiddleware;
use App\OurEdu\Subjects\Models\Subject;
use Closure;
use Illuminate\Http\Request;

class CheckSubjectUsageMiddleware extends CheckUsageMiddleware
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
        $subject = Subject::findOrFail($request->id);
        $errorsArr = [];
        if ($subject->students()->count()) {
            $errorsArr[] = trans('app.cant delete subject has students subscribed to it');
        }

        if (count($errorsArr)){
            foreach ($errorsArr as $error) {
                flash()->error($error);
            }
            return back();
        }
        return $next($request);
    }
}
