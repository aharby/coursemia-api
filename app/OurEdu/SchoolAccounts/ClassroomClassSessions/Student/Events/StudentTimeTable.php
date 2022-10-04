<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Events;
use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\EventSourcing\ShouldBeStored;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\Events\StudentTimetableTransformer;
class StudentTimeTable implements  ShouldBroadcast
{
    use SerializesModels, Dispatchable , ApiResponser;

    /**
     * @var array
     */
    private $classroomId;
    private $vcrId;
    private $sessionId;
    /**
     * @var string
     */
    private $meetingType;

    /**
     * Create a new event instance.
     *
     * @param int $classroomId
     * @param int $vcrId
     * @param int $sessionId
     * @param string $meetingType
     */
    public function __construct($classroomId,$vcrId,$sessionId,$meetingType)
    {
        $this->classroomId = $classroomId;
        $this->vcrId = $vcrId;
        $this->sessionId = $sessionId;
        $this->meetingType = $meetingType;
    }

    public function broadcastOn(){

        return new PresenceChannel('student-timetable.'. $this->classroomId);
    }

    public function broadcastAs() {

        return 'StudentTimetable';
    }

    public function broadcastWith()
    {
        $data[] = [
            'classroomId' => $this->classroomId,
            'vcrId' => $this->vcrId,
            'sessionId' => $this->sessionId,
            'meetingType' => $this->meetingType,
        ];

        return $this->transformDataMod($data , new StudentTimetableTransformer() , ResourceTypesEnums::STUDENTS_TIMETABLE);
    }
}
