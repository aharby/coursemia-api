<?php

namespace App\OurEdu\Exams\Events\ExamEvents;

use App\OurEdu\Events\Contracts\StudentStoredEventContract;
use App\OurEdu\Events\Enums\StudentEventsEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Illuminate\Foundation\Events\Dispatchable;

class StudentFinishedExam extends ShouldBeStored implements  StudentStoredEventContract
{
    use SerializesModels, Dispatchable;

    /**
     * @var array
     */
    public $exam_attributes;
    public $user_attributes;
    public $subject_attributes;
    public $by;
    public $action;

    /**
     * Create a new event instance.
     *
     * @param array $exam_attributes
     * @param array $user_attributes
     * @param array $subject_attributes
     * @param string $action
     */
    public function __construct(array $exam_attributes,
                                array $user_attributes,
                                array $subject_attributes,
                                $action = StudentEventsEnum::STUDENT_FINISHED_EXAM)    {
        $this->by = Auth::id();
        $this->exam_attributes = $exam_attributes;
        $this->user_attributes = $user_attributes;
        $this->subject_attributes = $subject_attributes;
        $this->action = $action;
    }
}
