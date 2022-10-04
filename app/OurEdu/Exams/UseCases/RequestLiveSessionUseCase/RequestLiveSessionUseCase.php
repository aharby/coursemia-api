<?php


namespace App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase;


use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;

class RequestLiveSessionUseCase implements RequestLiveSessionUseCaseInterface
{
    protected $VCRScheduleRepository;


    public function __construct(
        VCRScheduleRepositoryInterface $VCRScheduleRepository
    ) {
        $this->VCRScheduleRepository = $VCRScheduleRepository;
    }

    public function getAvailableVcrSpot($subjectId) {

        $time = date('H:i:s');
        $date = date('Y-m-d');
        $day = date('l', strtotime(now()));
        return $this->VCRScheduleRepository->getVcrFitsDayTimeAndSubject( $day , $time , $date ,$subjectId);
    }

}
