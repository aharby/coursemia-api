<?php

namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase;


interface FillMultipleChoiceUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @return mixed
     */
    public function fillResource(int $questionId,$data);
}
