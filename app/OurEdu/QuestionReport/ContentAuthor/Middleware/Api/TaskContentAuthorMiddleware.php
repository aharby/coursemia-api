<?php

namespace App\OurEdu\QuestionReport\ContentAuthor\Middleware\Api;

use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\UserEnums;
use Closure;

class TaskContentAuthorMiddleware
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

        $userContentAuthorIds = QuestionReportTask::findOrFail($request->task)
            ->subject->contentAuthors()->where('id', $user->id)->first();

        if ($userContentAuthorIds) {
            return $next($request);
        }
        return unauthorize();
    }
}
