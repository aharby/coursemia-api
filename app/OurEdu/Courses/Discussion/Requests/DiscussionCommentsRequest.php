<?php

namespace App\OurEdu\Courses\Discussion\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\CourseDiscussionComment;
use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;

class DiscussionCommentsRequest extends BaseApiParserRequest
{
    protected function prepareForValidation()
    {
        if ($this->route('courseDiscussion')) {
            $courseDiscussion = $this->route('courseDiscussion');
            if (!($courseDiscussion instanceof CourseDiscussion)) {
                $courseDiscussion = CourseDiscussion::query()->findOrFail($this->route('courseDiscussion'));
            }
            $course = $courseDiscussion->course;
        } elseif ($this->route('courseDiscussionComment')) {
            $courseDiscussionComment = $this->route('courseDiscussionComment');
            if (!($courseDiscussionComment instanceof CourseDiscussionComment)) {
                $courseDiscussionComment = CourseDiscussionComment::query()->findOrFail($this->route('courseDiscussionComment'));
            }

            $course = $courseDiscussionComment->discussions?->course;
        }

        if ($course and Carbon::now()->gt(Carbon::parse($course->end_date)->addDay(1)->subSecond())) {
            $this->addError("end_date", trans('discussions.could not write any comment for ended course'));
        }
        if ($this->user()->type === 'student') {
            $isActive = $course->students()->where('id', $this->user()->student->id)->first() ?
                $course->students()->where('id', $this->user()->student->id)->first(
                )->pivot->is_discussion_active : null;
            if (!$isActive) {
                $this->addError("is_discussion_active", trans('discussions.you are not active to write discussion'));
            }
        }
    }

    #[ArrayShape(['attributes.comment' => "string"])]
    public function rules(): array
    {
        return [
            'attributes.comment' => 'required|string',
        ];
    }

    protected function addError($key, $message)
    {
        $validator = $this->getValidatorInstance();

        $validator->after(
            function ($validator) use ($key, $message) {
                $validator->errors()->add($key, $message);
            }
        );
    }
}
