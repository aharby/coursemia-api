<?php


namespace App\OurEdu\GeneralQuizzes\Homework\UseCases;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

interface RetakeGeneralQuizUseCaseInterface
{
    public function retake(GeneralQuiz $generalQuiz, ItemInterface $data);
}
