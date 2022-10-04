<?php
namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\CourseDiscussionComment;

class DiscussionCommentRepository implements DiscussionCommentRepositoryInterface
{
    public CourseDiscussionComment $discussionComments;

    public function __construct(CourseDiscussionComment $discussionComments)
    {
        $this->discussionComments = $discussionComments;
    }

    public function create(array $data): CourseDiscussionComment
    {
        return $this->discussionComments->create($data);
    }

    public function getDiscussionComments(CourseDiscussion $discussion)
    {
        return $this->discussionComments->where('course_discussion_id', $discussion->id)->paginate(env("PAGE_LIMIT", 20));
    }
}
