<?php

namespace App\OurEdu\Courses\Discussion\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Users\UserEnums;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Courses\Models\CourseDiscussionComment;
class DiscussionCommentsTransformers extends TransformerAbstract
{
    protected array $defaultIncludes = [
        "user",
        "actions"
    ];

    public function transform(CourseDiscussionComment $discussionComments): array
    {
        return [
            'id' => (int)$discussionComments->id,
            'createdAt' => (string)$discussionComments->created_at,
            'comment' => (string)$discussionComments->comment
        ];
    }
    public function includeActions(CourseDiscussionComment $discussionComments)
    {
        $actions = [];
        if (now() < $discussionComments->discussions->course->end_date and $discussionComments->user_id == auth()->id()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.comments.update',
                    [
                        'courseDiscussionComment' => $discussionComments->id
                    ]
                ),
                'label' => trans('courses.discussion.update comment'),
                'key' => APIActionsEnums::UPDATE_COURSE_DISCUSSION_COMMENT,
                'method' => 'PUT'
            ];
        }

        if (now() < $discussionComments->discussions->course->end_date and $discussionComments->user_id == auth()->id()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.comments.delete',
                    [
                        'courseDiscussionComment' => $discussionComments->id
                    ]
                ),
                'label' => trans('courses.discussion.delete comment'),
                'key' => APIActionsEnums::DELETE_COURSE_DISCUSSION_COMMENT,
                'method' => 'DELETE'
            ];
        }

        if (auth()->user()->type == UserEnums::INSTRUCTOR_TYPE &&  $discussionComments->user->type == UserEnums::STUDENT_TYPE)
        {
            $student = $discussionComments->discussions->course->students()
                ->where('student_id', '=', $discussionComments->user->student->id)
                ->firstOrFail();

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.instructor.toggleStudentActivation',
                    [
                        'course'=> $discussionComments->discussions->course_id,
                        'student' => $student->id,
                    ]
                ),
                'label' =>$student->pivot->is_discussion_active ?  trans('app.inactivate') : trans('app.activate') ,
                'method' => 'GET',
                'key' => APIActionsEnums::ACTIVATE_STUDENT_COURSE
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.discussions.instructor.deleteDiscussionComment',
                    [
                        'courseDiscussionComment'=> $discussionComments->id,
                    ]
                ),
                'label' =>trans('courses.discussion.delete comment'),
                'method' => 'DELETE',
                'key' => APIActionsEnums::INSTRUCTOR_DELETE_DISCUSSION_COMMENT
            ];
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
    public function includeUser(CourseDiscussionComment $discussionComments): Item
    {
        return $this->item($discussionComments->user, new UserTransformer(), ResourceTypesEnums::USER);
    }
}
