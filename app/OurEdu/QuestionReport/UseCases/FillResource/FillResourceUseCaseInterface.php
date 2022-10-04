<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource;


use App\OurEdu\Users\User;

interface FillResourceUseCaseInterface
{
    public function fillResource(int $questionId, $data);
}
