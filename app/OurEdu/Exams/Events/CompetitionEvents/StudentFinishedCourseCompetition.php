<?php

namespace App\OurEdu\Exams\Events\CompetitionEvents;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Events\Contracts\StudentStoredEventContract;
use App\OurEdu\Events\Enums\StudentEventsEnum;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\CourseCompetition\FinishCompetitionTransformer;
use App\OurEdu\Users\User;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class StudentFinishedCourseCompetition extends ShouldBeStored implements StudentStoredEventContract, ShouldBroadcast
{
    use  Dispatchable , ApiResponser;


    /**
     * @var array
     */
    public $competition_attributes;
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
    public function __construct(
        private $studentBulkOrderInCompetition,
        private $studentOrderInCompetition,
        private User $user,
        private int $allStudentsCompetition,
        private int $finishedStudentsInCompetition,
        private Exam $exam,
    )
    {
        $this->by = Auth::id();
        $this->competition_attributes = $exam->toArray();
        $this->subject_attributes = [$exam->subject_id];
        $this->user_attributes = $user->toArray();
        $this->action = StudentEventsEnum::STUDENT_FINISHED_COMPETITION;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('competition.'. $this->exam->id);
    }

    public function broadcastAs(): string
    {
        return 'CourseCompetitionFinished';
    }

    public function broadcastWith()
    {
        $data = (object)[
            'studentBulkOrderInCompetition' => $this->studentBulkOrderInCompetition,
            'studentOrderInCompetition' => $this->studentOrderInCompetition,
            'allStudentsCompetition' => $this->allStudentsCompetition,
            'finishedStudentsInCompetition'=> $this->finishedStudentsInCompetition,
            'exam' => $this->exam,
            'user' => $this->user,
            'students' => $this->studentBulkOrderInCompetition ?? $this->studentOrderInCompetition
        ];

        return $this->transformDataModInclude($data, 'competition_group_order.students', new FinishCompetitionTransformer(), ResourceTypesEnums::STUDENT_ANSWERED_COMPETITION_QUESTION);
    }
}
