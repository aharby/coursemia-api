<?php


namespace App\OurEdu\GeneralExams\UseCases\FinishGeneralExamUseCase;

use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;

class FinishGeneralExamUseCase implements FinishGeneralExamUseCaseInterface
{
    private $generalExamRepository;
    private $generalExamStudentRepository;
    private $userRepository;

    public function __construct(
        GeneralExamRepositoryInterface $generalExamRepository,
        GeneralExamStudentRepositoryInterface $generalExamStudentRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->generalExamRepository = $generalExamRepository;
        $this->generalExamStudentRepository = $generalExamStudentRepository;
        $this->userRepository = $userRepository;
    }

    public function finishExam(int $examId, int $studentID)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);
        $studentExam = $this->generalExamStudentRepository->findStudentExam($exam->id , $studentID);

        $total = $exam->questions()->count();
        $correctAnswers = $this->generalExamStudentRepository->getStudentCorrectAnswersCount($exam->id , $studentID);

        $percentage = $total ? ($correctAnswers / $total) * 100 : 0;

        $data = [
            'is_finished' => 1,
            'finished_time' => now(),
            'result'    =>  number_format($percentage, 2, '.', '')
        ];

        if ($studentExam->is_finished == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is already finished');
            $return['title'] = 'The exam is already finished';
            return $return;
        } else {
            $this->generalExamStudentRepository->update($studentExam->id, $data);
            $return['status'] = 200;
            $return['message'] = trans('api.The exam finished successfully');

            return $return;
        }
    }
}
