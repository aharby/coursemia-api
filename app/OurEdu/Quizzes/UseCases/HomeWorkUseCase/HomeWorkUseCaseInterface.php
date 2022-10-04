<?php


namespace App\OurEdu\Quizzes\UseCases\HomeWorkUseCase;


interface HomeWorkUseCaseInterface
{
    /**
     * @param $data
     * @param bool $isPublish
     * @return array
     */
    public function createHomeWork($data): array;

    /**
     * @param $homework
     * @param $data
     * @param bool $isPublish
     * @return array
     */
    public function editHomeWork($homework, $data): array;

    /**
     * @param $homeworkId
     * @return array
     */
    public function getHomeWork($homeworkId): array;

    /**
     * @param $homework
     * @param $data
     * @return array
     */
    public function updateHomeWorkQuestions($homework, $data);

}
