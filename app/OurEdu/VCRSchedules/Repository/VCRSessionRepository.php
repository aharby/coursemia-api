<?php

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Pagination\LengthAwarePaginator;

class VCRSessionRepository implements VCRSessionRepositoryInterface
{

    use Filterable;
    protected $model;

    public function __construct(VCRSession $VCRSession)
    {
        $this->model = $VCRSession;
    }

    /**
     * @param $VCRSession
     * @return VCRSessionRepository
     */
    public function setVCRSession($VCRSession)
    {
        $this->model = $VCRSession;

        return $this;
    }

    /**
     * @param int $id
     * @return VCRRequest|null
     */
    public function findOrFail(int $id): ? VCRSession
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return VCRSession
     */
    public function create(array $data): VCRSession
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->findOrFail($id);
        return $model->update($data);
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     * @return VCRSession
     */
    public function findVCRSessionByCourseSession(int $courseId, int $sessionId): VCRSession
    {
        return $this->model->where('course_id', $courseId)
                ->where('course_session_id', $sessionId)
                ->firstOrFail();
    }

    public function getInstructorSessions(int $instructorId)
    {
        return $this->model->where('instructor_id', $instructorId)->get();
    }

    public function getSessionParticipants(VCRSession $VCRSession)
    {
        return $VCRSession->participants()->with(['user'])->get();
    }

    public function getSessionInstructor(int $sessionId)
    {
        return $this->model->whereId($sessionId)->first()->instructor;
    }

    public function getUnNotifiedClassroomStudents(int $classroomId,$isSpecialClassroom = false)
    {
        $query = Student::query()->with('user')
            ->whereHas('user');

        if($isSpecialClassroom){
            return $query
                ->whereHas('specialClassroom',function($query)use($classroomId){
                    $query->where('classroom_id',$classroomId);
            })->get();
        }

        return $query
            ->where('classroom_id',$classroomId)
            ->get();
    }

    public function findOrFailWhereType(string $type, int $id): ?VCRSession
    {
        return $this->model->where('vcr_session_type', $type)->findOrFail($id);
    }
}
