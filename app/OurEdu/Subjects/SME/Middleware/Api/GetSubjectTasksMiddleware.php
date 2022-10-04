<?php

namespace App\OurEdu\Subjects\SME\Middleware\Api;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\UserEnums;
use Closure;

class GetSubjectTasksMiddleware
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

        $user = auth()->user();
        $subject = Subject::findOrFail($request->id);
        if ($user->type == UserEnums::SME_TYPE) {

            if ($subject->sme_id == $user->id) {
                return $next($request);
            } else {
                unauthorize();

            }


        }
        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            if ($subject->contentAuthors()->where('id', $user->id)->exists()) {
                return $next($request);

            } else {
                return unauthorize();
            }


        }
        return unauthorize();
    }
}
