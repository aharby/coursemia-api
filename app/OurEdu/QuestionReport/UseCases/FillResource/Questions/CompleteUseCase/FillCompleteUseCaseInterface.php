<?php

namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\CompleteUseCase;

interface FillCompleteUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data);
}
