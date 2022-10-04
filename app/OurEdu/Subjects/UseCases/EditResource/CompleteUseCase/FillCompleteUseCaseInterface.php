<?php

namespace App\OurEdu\Subjects\UseCases\EditResource\CompleteUseCase;

interface FillCompleteUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data);
}
