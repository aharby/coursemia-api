<?php

namespace App\OurEdu\Courses\Discussion\Controllers;

use App\OurEdu\Courses\Discussion\Transformers\CourseDiscussionTransformer;
use App\OurEdu\Courses\Models\CourseDiscussion;
use Illuminate\Http\JsonResponse;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Courses\Discussion\Requests\CourseDiscussionRequest;
use App\OurEdu\Courses\Discussion\Middleware\IsHaveCourseMiddleware;
use App\OurEdu\Courses\Discussion\Transformers\DiscussionTransformer;


class CourseDiscussionController extends BaseApiController
{
    public function __construct(
        private ParserInterface $parserInterface,
        private CourseRepositoryInterface $courseRepository
    ) {
        $this->middleware('type:instructor|student');
        $this->middleware(IsHaveCourseMiddleware::class);
    }


    /**
     * Display a listing of the resource.
     * @param Course $course
     * @return JsonResponse
     */
    public function index(Course $course)
    {
        $discussions = $course->discussions()->latest()
            ->paginate(10, "*" , "discussion-page");

        return $this->transformDataModIncludeItem(
            $course,
            '',
            new CourseDiscussionTransformer($discussions),
            ResourceTypesEnums::COURSE,
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CourseDiscussionRequest $request
     * @return JsonResponse
     */
    public function store(CourseDiscussionRequest $request)
    {
        $requestData = $this->parserInterface->deserialize($request->getContent())
            ->getData();

        $data = [
            'body' => $requestData->body,
            'user_id' => $request->user()->id,
            'course_id' => $request->route('course')
        ];

        $discussion = $this->courseRepository->createDiscussion($data);

        return $this->transformDataModInclude(
            $discussion,
            '',
            new DiscussionTransformer(),
            ResourceTypesEnums::COURSE_DISCUSSION
        );
    }

    public function show(CourseDiscussion $courseDiscussion)
    {
        return $this->transformDataModIncludeItem(
            $courseDiscussion,
            '',
            new DiscussionTransformer(),
            ResourceTypesEnums::COURSE_DISCUSSION,
        );
    }

    public function update(CourseDiscussion $courseDiscussion, CourseDiscussionRequest $request)
    {
        if ($courseDiscussion->user_id !== auth()->id()) {
            unauthorize();
        }
        $requestData = $this->parserInterface->deserialize($request->getContent())
         ->getData();
        $courseDiscussion = tap($courseDiscussion)->update(['body' => $requestData->body]);

        return $this->transformDataModInclude(
            $courseDiscussion,
            '',
            new DiscussionTransformer(),
            ResourceTypesEnums::COURSE_DISCUSSION
        );
    }

    public function delete(CourseDiscussion $courseDiscussion)
    {
        if ($courseDiscussion->user_id !== auth()->id()) {
            unauthorize();
        }

        $validateError = $this->validateEndedCourse($courseDiscussion);
        if ($validateError) {
            return formatErrorValidation($validateError);
        }

        if ($courseDiscussion->delete()) {
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

    private function validateEndedCourse($courseDiscussion)
    {
        $error = [];
        $course = $courseDiscussion?->course;

        if ($course && $course->end_date < now()->toDateString())
        {
            $error= [
                "status" => 422,
                'title' => "could not write any discussion for ended course",
                'detail' => trans('discussions.could not write any discussion for ended course'),
            ];
        }

        return $error;
    }
}
