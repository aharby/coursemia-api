<?php

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSessionParticipant;

class VCRSessionParticipantsRepository implements VCRSessionParticipantsRepositoryInterface
{

    protected $model;

    public function __construct(VCRSessionParticipant $VCRSessionParticipant)
    {
        $this->model = $VCRSessionParticipant;
    }


    public function findOrFail(int $id): ?VCRSessionParticipant
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): VCRSessionParticipant
    {
        return $this->model->create($data);
    }

    public function insert(array $data)
    {
         $this->model->insert($data);
    }

    // TODO:: could be hasManyTrough()
    public function getSessionStudentParticipants(int $sessionId, $notifiedUsers = null)
    {
        return User::where('type', UserEnums::STUDENT_TYPE)
            ->when(
                $notifiedUsers,
                function ($q) use ($notifiedUsers) {
                    $q->whereNotIn('id', $notifiedUsers);
                }
            )
            ->whereHas(
                'participatedVCRs',
                function ($student) use ($sessionId) {
                    $student->where('vcr_session_id', $sessionId);
                }
            )
            ->with('student')
            ->get();
    }

    // TODO:: could be hasManyTrough()
    public function getSessionAbsentStudentParticipants(int $sessionId, $attendedStudentsIds)
    {
        return User::where('type', UserEnums::STUDENT_TYPE)
            ->whereNotIn('id', $attendedStudentsIds)
            ->whereHas(
                'participatedVCRs',
                function ($student) use ($sessionId) {
                    $student->where('vcr_session_id', $sessionId);
                }
            )
            ->get();
    }

    public function getAbsentStudent($classroomId,$attendedStudentsIds)
    {
        return User::where('type', UserEnums::STUDENT_TYPE)
            ->whereNotIn('id', $attendedStudentsIds)
            ->where('is_active',1)
            ->whereHas(
                'student',
                function ($student) use ($classroomId) {
                    $student->where('classroom_id', $classroomId);
                }
            )
            ->cursor();
    }
}
