<?php

namespace App\OurEdu\Subjects\ContentAuthor\Middleware\Api;

use App\Exceptions\OurEduErrorException;
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
        $user = auth()->user();
        $resourceSubjectFormatSubject = ResourceSubjectFormatSubject::findOrFail($request->resourceId);

        if (! $resourceSubjectFormatSubject->task) {
            return unauthorize();
        }

        $checkOwner = $resourceSubjectFormatSubject->task->contentAuthors()->where('content_authors.id', $user->contentAuthor->id)->exists();

        if ($checkOwner) {
            return $next($request);
        }

        return unauthorize();
    }
}
