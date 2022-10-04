<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultipleChoiceUseCase;


interface FillMultipleChoiceUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId,$data);
}