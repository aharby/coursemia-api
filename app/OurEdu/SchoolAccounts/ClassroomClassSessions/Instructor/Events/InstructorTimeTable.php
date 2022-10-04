<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Events;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\Events\InstructorTimetableTransformer;
class InstructorTimeTable implements  ShouldBroadcast
{
    use SerializesModels, Dispatchable , ApiResponser;

    /**
     * @var array
     */
    private $branchId;
    private $vcrId;
    private $sessionId;
    /**
     * @var string
     */
    private $meetingType;

    /**
     * Create a new event instance.
     *
     * @param int $branchId
     * @param int $vcrId
     * @param int $sessionId
     * @param string $meetingType
     */
    public function __construct($branchId,$vcrId,$sessionId, $meetingType)
    {
        $this->branchId = $branchId;
        $this->vcrId = $vcrId;
        $this->sessionId = $sessionId;
        $this->meetingType = $meetingType;
    }

    public function broadcastOn(){
        return new PresenceChannel('instructor-timetable.'. $this->branchId);
    }

    public function broadcastAs() {
        return 'InstructorTimetable';
    }

    public function broadcastWith()
    {
        $data[] = [
            'branchId' => $this->branchId,
            'vcrId' => $this->vcrId,
            'sessionId' =>$this->sessionId,
            'meetingType' =>$this->meetingType,
        ];
        return $this->transformDataMod($data , new InstructorTimetableTransformer() , ResourceTypesEnums::INSTRUCTORS_TIMETABLE);
    }
}
