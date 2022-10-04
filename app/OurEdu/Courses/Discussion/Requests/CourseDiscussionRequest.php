<?php

namespace App\OurEdu\Courses\Discussion\Requests;

use Carbon\Carbon;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class CourseDiscussionRequest extends BaseApiParserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->route('course')) {
            $course = Course::where('id', $this->route('course'))->first();

            if (!($course instanceof Course)) {
                $course =$this->route('course');
            }
        } elseif ($this->route('courseDiscussion')) {
            $courseDiscussion = $this->route('courseDiscussion');
            if (!($courseDiscussion instanceof CourseDiscussion)) {
                $courseDiscussion = CourseDiscussion::query()->findOrFail($this->route('courseDiscussion'));
            }
            $course = $courseDiscussion->course;
        }

        if ($course and Carbon::now()->gt(Carbon::parse($course->end_date)->addDay(1)->subSecond())) {
            $this->addError("end_date", trans('discussions.could not write any discussion for ended course'));
        }

        if ($this->user()->type === 'student') {
            $isActive = $course?->students()->where('id', $this->user()->student->id)->first() ?
                $course->students()->where('id', $this->user()->student->id)->first(
                )->pivot->is_discussion_active : null;

            if (!$isActive) {
                $this->addError("is_discussion_active", trans('discussions.you are not active to write discussion'));
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.body' => 'required|string'
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
