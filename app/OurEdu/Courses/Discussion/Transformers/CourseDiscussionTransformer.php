<?php

namespace App\OurEdu\Courses\Discussion\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class CourseDiscussionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        "discussions",
        "actions",
    ];

    public function __construct(private LengthAwarePaginator $discussions)
    {
    }

    public function transform(Course $course): array
    {
        $transformData =  [
            "id" => $course->id,
            "is_user_active" => $this->isUserActiveToDiscuss($course),
            'is_ended' => $course->end_date < now()->toDateString() ? true :false,
        ];

        $transformData['pagination'] = (object)[
            'per_page' => $this->discussions->perPage(),
            'total' => $this->discussions->total(),
            'current_page' => $this->discussions->currentPage(),
            'count' => $this->discussions->count(),
            'total_pages' => $this->discussions->lastPage(),
            'next_page' => $this->discussions->nextPageUrl(),
            'previous_page' => $this->discussions->previousPageUrl()
        ];

        return $transformData;
    }

    public function includeDiscussions(Course $course)
    {
        return $this->collection($this->discussions, new DiscussionTransformer(), ResourceTypesEnums::COURSE_DISCUSSION);
    }

    public function includeActions(Course $course)
    {
        $actions = [];

        if ($course->end_date > now() and $this->isUserActiveToDiscuss($course)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.discussions.store', ['course' => $course->id]),
                'label' => trans('courses.discussion.add discussions'),
                'key' => APIActionsEnums::ADD_COURSE_DISCUSSION,
                'method' => 'POST'
            ];
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    private function isUserActiveToDiscuss(Course $course): bool
    {
        $user = Auth::user();
        $isActive = true;

        if ($user->type != UserEnums::INSTRUCTOR_TYPE) {
            $student = $course->students()
                ->where("students.user_id", "=", $user->id)
                ->firstOrFail();

            $isActive =  $student->pivot->is_discussion_active ?? false;
        }

        return $isActive;
    }
}
