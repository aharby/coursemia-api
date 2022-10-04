<?php

namespace App\OurEdu\Courses\Discussion\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Courses\Models\CourseDiscussionComment;
use App\OurEdu\Courses\Discussion\Middleware\IsHaveCourseMiddleware;
use App\OurEdu\Courses\Discussion\Requests\DiscussionCommentsRequest;
use App\OurEdu\Courses\Repository\DiscussionCommentRepositoryInterface;
use App\OurEdu\Courses\Discussion\Transformers\DiscussionCommentsTransformers;

class DiscussionCommentsController extends BaseApiController
{
    public function __construct(
        private DiscussionCommentRepositoryInterface $discussionCommentRepository,
        private ParserInterface $parserInterface
    ) {
        $this->middleware('type:instructor|student');
        $this->middleware(IsHaveCourseMiddleware::class);
    }

    public function index(CourseDiscussion $courseDiscussion)
    {
        $discussionComments = $this->discussionCommentRepository->getDiscussionComments($courseDiscussion);

        return $this->transformDataModInclude($discussionComments, '', new DiscussionCommentsTransformers(), ResourceTypesEnums::DISCUSSION_COMMENTS);
    }

    public function store(CourseDiscussion $courseDiscussion, DiscussionCommentsRequest $request)
    {
        $requestData = $this->parserInterface->deserialize($request->getContent())
            ->getData();

        $data = [
            'comment' => $requestData->comment,
            'user_id' => $request->user()->id,
            'course_discussion_id' => $courseDiscussion->id
        ];

        $discussionComments = $this->discussionCommentRepository->create($data);

        return $this->transformDataModInclude(
            $discussionComments,
            '',
            new DiscussionCommentsTransformers(),
            ResourceTypesEnums::DISCUSSION_COMMENTS
        );
    }

    public function update(
        CourseDiscussionComment $courseDiscussionComment,
        DiscussionCommentsRequest $request
    ) {
        if ($courseDiscussionComment->user_id !== auth()->id()) {
            unauthorize();
        }
      
        $requestData = $this->parserInterface->deserialize($request->getContent())
            ->getData();
        $courseDiscussionComment = tap($courseDiscussionComment)->update(['comment' => $requestData->comment]);

        return $this->transformDataModInclude(
            $courseDiscussionComment,
            '',
            new DiscussionCommentsTransformers(),
            ResourceTypesEnums::DISCUSSION_COMMENTS
        );
    }

    public function delete(CourseDiscussionComment $courseDiscussionComment)
    {
        if ($courseDiscussionComment->user_id !== auth()->id()) {
            unauthorize();
        }
        $validateError = $this->validateEndedCourse($courseDiscussionComment);
        if ($validateError) {
            return formatErrorValidation($validateError);
        }

        if ($courseDiscussionComment->delete()) {
            return response()->json(
                [
                    'meta' => [
                        'message' => trans('app.Delete successfully')
                    ]
                ],
                200
            );
        }

        return [
            'message'=>trans('app.Something went wrong'),
            'code' => '500'
        ];
    }

    private function validateEndedCourse(CourseDiscussionComment $courseDiscussionComment)
    {
        $course = $courseDiscussionComment->discussions?->course;
        $error = [];
        if ($course && $course->end_date < now()->toDateString()) {
            $error= [
                "status" => 422,
                'title' => "could not write any comment for ended course",
                'detail' => trans('discussions.could not write any comment for ended course'),
            ];
        }
        
        return $error;
    }
}
