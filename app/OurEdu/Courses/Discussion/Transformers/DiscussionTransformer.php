<?php

namespace App\OurEdu\Courses\Discussion\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\TransformerAbstract;

class DiscussionTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
        "comments",
        "actions",
        "user",
    ];
    private LengthAwarePaginator $comments;
    private string $paginationPageName;

    /**
     * @param string $paginationPageName
     */
    public function __construct()
    {
        $this->paginationPageName = 'discussion-comment-page';
    }


    public function transform(CourseDiscussion $courseDiscussion)
    {
        $this->loadCommentsPaginator($courseDiscussion);

        $transformData =  [
            'id' => (int)$courseDiscussion->id,
            'body' => (string)$courseDiscussion->body,
            'published_at' => (string)$courseDiscussion->created_at,
        ];

        $transformData['pagination'] = (object)[
            'per_page' => $this->comments->perPage(),
            'total' => $this->comments->total(),
            'current_page' => $this->comments->currentPage(),
            'count' => $this->comments->count(),
            'total_pages' => $this->comments->lastPage(),
            'next_page' => $this->getPaginationNextURL($courseDiscussion),
            'previous_page' => $this->getPaginationPreviousURL($courseDiscussion)
        ];

        return $transformData;
    }

    public function includeActions(CourseDiscussion $courseDiscussion)
    {
        $actions = [];
        if (now() < $courseDiscussion->course->end_date and $courseDiscussion->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.discussions.comments.store', ['courseDiscussion' => $courseDiscussion->id]),
                'label' => trans('courses.discussion.add comment'),
                'key' => APIActionsEnums::ADD_COURSE_DISCUSSION_COMMENT,
                'method' => 'POST'
            ];
        }

        if (now() < $courseDiscussion->course->end_date and $courseDiscussion->user_id == auth()->id()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.update',
                    [
                        'courseDiscussion' => $courseDiscussion->id
                    ]
                ),
                'label' => trans('courses.discussion.update discussions'),
                'key' => APIActionsEnums::UPDATE_COURSE_DISCUSSION,
                'method' => 'PUT'
            ];
        }

        if (now() < $courseDiscussion->course->end_date and $courseDiscussion->user_id == auth()->id()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.delete',
                    [
                        'courseDiscussion' => $courseDiscussion->id
                    ]
                ),
                'label' => trans('courses.discussion.delete discussions'),
                'key' => APIActionsEnums::DELETE_COURSE_DISCUSSION,
                'method' => 'DELETE'
            ];
        }

        if (auth()->user()->type == UserEnums::INSTRUCTOR_TYPE &&  $courseDiscussion->user->type == UserEnums::STUDENT_TYPE)
        {
            $student = $courseDiscussion->course->students()
                ->where('student_id', '=', $courseDiscussion->user->student->id)
                ->firstOrFail();

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.instructor.toggleStudentActivation',
                    [
                        'course'=> $courseDiscussion->course_id,
                        'student' => $student->id,
                    ]
                ),
                'label' =>$student->pivot->is_discussion_active ?  trans('app.inactivate') : trans('app.activate') ,
                'method' => 'GET',
                'key' => APIActionsEnums::ACTIVATE_STUDENT_COURSE
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.instructor.deleteDiscussion',
                    [
                        'courseDiscussion'=> $courseDiscussion->id,
                    ]
                ),
                'label' =>trans('app.Instructor delete discussion') ,
                'method' => 'DELETE',
                'key' => APIActionsEnums::INSTRUCTOR_DELETE_DISCUSSION
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeUser(CourseDiscussion $courseDiscussion)
    {
        return $this->item($courseDiscussion->user, new UserTransformer(), ResourceTypesEnums::USER);
    }

    public function includeComments(CourseDiscussion $courseDiscussion)
    {
        return $this->collection($this->comments, new DiscussionCommentsTransformers(), ResourceTypesEnums::DISCUSSION_COMMENTS);
    }

    private function loadCommentsPaginator(CourseDiscussion $courseDiscussion)
    {
        $this->comments = $courseDiscussion->comments()
            ->orderByDesc("created_at")
            ->paginate(
                env("PAGE_LIMIT", 2),
                "*",
                $this->paginationPageName
            );
    }

    private function getPaginationNextURL(CourseDiscussion $courseDiscussion): string
    {
        $lastPage = $this->comments->lastPage();
        $currentPage = $this->comments->currentPage();

        if ($currentPage >= $lastPage) {
            return '';
        }

        return buildScopeRoute('api.discussions.show', ['courseDiscussion' => $courseDiscussion->id]) . '?' . $this->paginationPageName .'='. $currentPage+1;
    }

    private function getPaginationPreviousURL(CourseDiscussion $courseDiscussion): string
    {
        $currentPage = $this->comments->currentPage();

        if ($currentPage <= 1) {
            return '';
        }

        return buildScopeRoute('api.discussions.show', ['courseDiscussion' => $courseDiscussion->id]) . '?' . $this->paginationPageName .'='. $currentPage-1;
    }
}
