<?php


namespace App\OurEdu\Quizzes\UseCases\PeriodicTestUseCase;


interface PeriodicTestUseCaseInterface
{
    /**
     * @param $data
     * @param bool $isPublish
     * @return array
     */
    public function createPeriodicTest($data): array;

    /**
     * @param $PeriodicTest
     * @param $data
     * @param bool $isPublish
     * @return array
     */
    public function editPeriodicTest($periodicTestId, $data): array;

    /**
     * @param $PeriodicTestId
     * @return array
     */
    public function getPeriodicTest($periodicTestId): array;

    /**
     * @param $PeriodicTest
     * @param $data
     * @return array
     */
    public function updatePeriodicTestQuestions($periodicTestId, $data);

}
