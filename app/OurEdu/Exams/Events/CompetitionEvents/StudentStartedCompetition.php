<?php

namespace App\OurEdu\Exams\Events\CompetitionEvents;

use App\OurEdu\Events\Contracts\StudentStoredEventContract;
use App\OurEdu\Events\Enums\StudentEventsEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Illuminate\Foundation\Events\Dispatchable;

class StudentStartedCompetition extends ShouldBeStored implements  StudentStoredEventContract , ShouldBroadcast
{
    use SerializesModels, Dispatchable , InteractsWithSockets;

    /**
     * @var array
     */
    public $competition_attributes;
    public $examId;
    public $user_attributes;
    public $subject_attributes;
    public $by;
    public $action;

    /**
     * Create a new event instance.
     *
     * @param array $competition_attributes
     * @param array $user_attributes
     * @param array $subject_attributes
     * @param string $action
     */
    public function __construct(array $competition_attributes,
                                array $user_attributes,
                                array $subject_attributes,
                                $action = StudentEventsEnum::STUDENT_STARTED_COMPETITION)
    {
        $this->by = Auth::id();
        $this->competition_attributes = $competition_attributes;
        $this->examId = $competition_attributes['exam_id'];
        $this->user_attributes = $user_attributes;
        $this->subject_attributes = $subject_attributes;
        $this->action = $action;

        //To Avoid Pushing notifications to user about himself
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn(){

        return new PresenceChannel('competition.'.$this->examId);
    }


    public function broadcastAs() {

        return 'StudentStartedCompetition';
    }
}
