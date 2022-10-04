<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\CompleteUseCase;

interface FillCompleteUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data);
}
