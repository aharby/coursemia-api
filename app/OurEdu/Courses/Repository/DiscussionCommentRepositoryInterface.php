<?php
namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\CourseDiscussionComment;

interface DiscussionCommentRepositoryInterface
{
    public function create(array $data): CourseDiscussionComment;

    public function getDiscussionComments(CourseDiscussion $discussion);
}
