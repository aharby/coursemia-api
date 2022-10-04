<?php

declare(strict_types=1);

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use Illuminate\Pagination\LengthAwarePaginator;

interface VCRScheduleRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all() : LengthAwarePaginator;

    /**
     * @param int $id
     * @return VCRSchedule|null
     */
    public function findOrFail(int $id): ?VCRSchedule;

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param array $where
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param array $data
     * @return VCRSchedule
     */
    public function create(array $data): VCRSchedule;

    /**
     * @param VCRSchedule $vcrSchedule
     * @param array $data
     * @return bool
     */
    public function update(VCRSchedule $vcrSchedule, array $data): bool;

    /**
     * @param VCRSchedule $vcrSchedule
     * @return bool
     */
    public function delete(VCRSchedule $vcrSchedule): bool;

    /**
     * @param array $data
     * @return VCRScheduleDays
     */
    public function createWorkingDays(array $data): VCRScheduleDays;

    public function getWorkingDay($scheduleId, $day);

    public function updateWorkingDays(VCRScheduleDays $vcrScheduleDays, array $data): bool;

    public function getVcrFitsDayTimeAndSubject($day , $time , $date , $subjectId) : ?VCRSchedule ;

    /**
     * @param User $instructor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getInstructorSchedules(User $instructor);

    /**
     * @param VCRSchedule $schedule
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getScheduleSessions(VCRSchedule $schedule);

    public function  getWorkingDayes($from , $to);

    public function getInstructorSchedule($instructorID);

    public function getAvailableVcrSpotInstructors(Subject $subject);

    public function getAllVcrSpotInstructors($filters = []);

}
