<?php

namespace App\OurEdu\Subjects\ContentAuthor\Middleware\Api;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\UserEnums;
use Closure;
use Illuminate\Support\Facades\Auth;

class ContentAuthorAssignedToTaskMiddleware
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

        $user = Auth::guard('api')->user();

        $userContentAuthorIds = Task::findOrFail($request->id)
            ->contentAuthors()
            ->where('content_author_id', $user->contentAuthor->id)
            ->first();

        if ($userContentAuthorIds) {
            return $next($request);
        }
        return unauthorize();
    }
}
