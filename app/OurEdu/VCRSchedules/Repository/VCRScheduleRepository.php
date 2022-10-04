<?php

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class VCRScheduleRepository implements VCRScheduleRepositoryInterface
{
    use Filterable;

    protected $model;

    public function __construct(VCRSchedule $vcrSchedule)
    {
        $this->model = $vcrSchedule;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->model->with('subject')->latest()->paginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $where
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->model->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param int $id
     * @return VCRSchedule|null
     */
    public function findOrFail(int $id): ?VCRSchedule
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return VCRSchedule
     */
    public function create(array $data): VCRSchedule
    {
        return $this->model->create($data);
    }

    /**
     * @param VCRSchedule $vcrSchedule
     * @param array $data
     * @return bool
     */
    public function update(VCRSchedule $vcrSchedule, array $data): bool
    {
        return $vcrSchedule->update($data);
    }

    /**
     * @param VCRSchedule $vcrSchedule
     * @return bool
     * @throws \Exception
     */
    public function delete(VCRSchedule $vcrSchedule): bool
    {
        return $vcrSchedule->delete();
    }

    // working days functions
    public function getWorkingDay($scheduleId, $day)
    {
        return VCRScheduleDays::where('vcr_schedule_instructor_id', $scheduleId)
            ->where('day', $day)->first();
    }

    /**
     * @param int $instructorId
     * @return LengthAwarePaginator
     */
    public function getNextWeekSchedule($instructorId)
    {
        return VCRScheduleDays::whereHas(
            'vcrSchedule',
            function (Builder $query) use ($instructorId) {
                $query->whereHas('instructor',

                    function ($q) use ($instructorId) {
                        $q->where('id', $instructorId);
                    }
                )->nextWeek();
            }
        )->jsonPaginate(env('PAGE_LIMIT', 10));
    }

    public function createWorkingDays(array $data): VCRScheduleDays
    {
        return VCRScheduleDays::create($data);
    }

    public function updateWorkingDays(VCRScheduleDays $vcrScheduleDay, array $data): bool
    {
        return $vcrScheduleDay->update($data);
    }

    public function getVcrFitsDayTimeAndSubject($day, $time, $date, $subjectId): ?VCRSchedule
    {
        return $this->model->where('subject_id', $subjectId)
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->whereHas('workingDays', function ($workingDay) use ($day, $time) {
                $workingDay->where('day', $day)
                    ->where('from_time', '<=', $time)
                    ->where('to_time', '>=', $time);
            })
            // TODO::Don't Have Accepted Active Request
            //->whereNotHas()
            ->first();
    }

    /**
     * @param User $instructor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getInstructorSchedules(User $instructor)
    {
        return VCRSchedule::query()
            ->with("subject")
            ->where("instructor_id", "=", $instructor->id)
            ->orderByDesc("from_date")
            ->withCount("workingDays")
            ->paginate(env("PAGE_LIMIT", 20));
    }

    /**
     * @param VCRSchedule $schedule
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getScheduleSessions(VCRSchedule $schedule)
    {
        return VCRSession::query()
            ->with("subject")
            ->whereHas("VCRScheduleDays", function (Builder $scheduleDays) use ($schedule) {
                $scheduleDays->where("vcr_schedule_instructor_id", "=", $schedule->id);
            })
            ->orderBy("time_to_start")
            ->paginate(env("PAGE_LIMIT", 20));
    }

    public function getWorkingDayes($from , $to)
    {
        $dayes = [];
        $fromDate = Carbon::parse($from);
        $toDate = Carbon::parse($to);
        $diff = $fromDate->diffInDays($toDate);
        if($diff +1) {
      for ( $i = 0 ; $i < $diff +1  ; $i++){
          $date = clone $fromDate;
          $dayes[] = strtolower($date->addDays($i)->format('l'));
      }
      }

      return $dayes;

    }

    public function getInstructorSchedule($instructorID)
    {
        return $this->model->where('instructor_id' , $instructorID)
            ->orderBy('id' , 'desc')
            ->get();
    }

    public function getAvailableVcrSpotInstructors(Subject $subject)
    {
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $day = date('l', strtotime(now()));

        return $subject->instructors()->whereHas(
            'vcrSchedule',
            function ($vcr) use ($date, $day, $time, $subject) {
                $vcr->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date)
                    ->where('subject_id', $subject->id)
                    ->whereHas(
                        'workingDays',
                        function ($workingDay) use ($day, $time) {
                            $workingDay->where('day', $day)
                                ->where('from_time', '<=', $time)
                                ->where('to_time', '>=', $time);
                        }
                    );
            }
        )->with(
            'vcrSchedule',
            function ($withVcr) use ($date, $day, $time, $subject) {
                $withVcr->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date)
                    ->where('subject_id', $subject->id)
                    ->with(
                        'workingDays',
                        function ($withWorkingDay) use ($day, $time) {
                            $withWorkingDay->where('day', $day)
                                ->where('from_time', '<=', $time)
                                ->where('to_time', '>=', $time);
                        }
                    );
            }
        )
            ->get();
    }

    public function getAllVcrSpotInstructors($filters = [])
    {
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $day = date('l', strtotime(now()));

        return $this->applyFilters(new VCRSchedule(),$filters)
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->whereHas(
                'workingDays',
                function ($workingDay) use ($day, $time) {
                    $workingDay->where('day', $day)
                        ->where('from_time', '<=', $time)
                        ->where('to_time', '>=', $time);
                }
            )
            ->whereHas(
                'subject',
                function ($subject) {
                    $student = auth()->user()->student;

                    $subject->where('country_id', auth()->user()->country_id)
                        ->where(
                            'educational_system_id',
                            $student->educational_system_id
                        )->where(
                            'academical_years_id',
                            $student->academical_year_id
                        );
                }
            )
            ->whereHas('instructor')
            ->with(
                'workingDays',
                function ($withWorkingDay) use ($day, $time) {
                    $withWorkingDay->where('day', $day)
                        ->where('from_time', '<=', $time)
                        ->where('to_time', '>=', $time);
                }
            )
            ->paginate();
    }
}
