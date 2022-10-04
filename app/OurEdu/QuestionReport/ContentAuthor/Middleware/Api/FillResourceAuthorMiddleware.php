<?php

namespace App\OurEdu\QuestionReport\ContentAuthor\Middleware\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\UserEnums;
use Closure;

class FillResourceAuthorMiddleware
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

//        try {
        $user = auth()->user();
        $task = QuestionReportTask::findOrFail($request->task);
        $checkOwner = $task->contentAuthors()->where('content_authors.id', $user->contentAuthor->id)->exists();
        if ($checkOwner) {
            return $next($request);
        }
//        } catch (\Throwable $exception) {
//            throw new OurEduErrorException($exception->getMessage());
//        }


        return unauthorize();
    }
}
