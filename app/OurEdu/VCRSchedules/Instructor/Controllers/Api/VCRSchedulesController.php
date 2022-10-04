<?php

namespace App\OurEdu\VCRSchedules\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSchedules\Instructor\Transformers\VCRSessionTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\VCRSchedules\Instructor\Transformers\VCRScheduleInstructorTransformer;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;

class VCRSchedulesController extends BaseApiController
{
    private $vcrScheduleRepository;

    public function __construct(VCRScheduleRepositoryInterface $vcrScheduleRepository)
    {
        $this->vcrScheduleRepository = $vcrScheduleRepository;
    }

    public function getNextWeekVcrSchedule()
    {
        $instructorId = auth()->user()->id;

        $vcrSchedules = $this->vcrScheduleRepository->getNextWeekSchedule($instructorId);
        $meta[] = ['pagination' => [
            'per_page' => $vcrSchedules->perPage(),
            'total' => $vcrSchedules->total(),
            'current_page' => $vcrSchedules->currentPage(),
            'count' => $vcrSchedules->count(),
            'total_pages' => $vcrSchedules->lastPage(),
            'next_page' => $vcrSchedules->nextPageUrl(),
            'previous_page' => $vcrSchedules->previousPageUrl()
        ]];

        $vcrSchedules  =
            $vcrSchedules->each(function ($query) {
                $query->date = app(CarbonImmutable::class)->next($query->day)->toDateString();
            })->sortByDesc('date');

        return $this->transformDataModInclude($vcrSchedules, '', new VCRScheduleInstructorTransformer(), ResourceTypesEnums::VCR_SCHEDULE,$meta);
    }

    public function getVCRSchedules()
    {
        $instructor = Auth::user();

        $vcrSchedules = $this->vcrScheduleRepository->getInstructorSchedules($instructor);

        return $this->transformDataModInclude($vcrSchedules, '', new VCRScheduleInstructorTransformer(), ResourceTypesEnums::VCR_SCHEDULE);
    }

    public function getVCRScheduleSessions(VCRSchedule $schedule)
    {
        $sessions = $this->vcrScheduleRepository->getScheduleSessions($schedule);

        return $this->transformDataModInclude($sessions, "", new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION);
    }

    public function getRequestInfo(VCRSession $VCRSession)
    {
        $VCRSession->load('classroom.branch.schoolAccount');

        return $this->transformDataModInclude($VCRSession, "subject,actions", new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION);
    }
}
