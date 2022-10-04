<?php

namespace App\OurEdu\Subjects\UseCases\EditResource\MultipleChoiceUseCase;


interface FillMultipleChoiceUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId,$data);
}
