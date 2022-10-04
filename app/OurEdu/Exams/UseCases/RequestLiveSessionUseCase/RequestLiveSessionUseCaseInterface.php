<?php


namespace App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase;


interface RequestLiveSessionUseCaseInterface
{
    public function getAvailableVcrSpot($subjectId);
}
