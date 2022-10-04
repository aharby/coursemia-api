<?php


namespace App\OurEdu\Exams\UseCases\ExamTakeLikeUseCase;


use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;

class ExamTakeLikeUseCase implements ExamTakeLikeUseCaseInterface
{

    private $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function create($examID, $studentID)
    {
        $exam = $this->examRepository->findOrFail($examID);

        if (!is_student_subscribed($exam->subject)) {
            $data = [
                'status' => 422,
                'title' => trans("exam.you must subscribe the exam subject to take the exam"),
                'detail' => trans("exam.you must subscribe the exam subject to take the exam")
            ];
            return $data;
        }

        $examOptions = [
            'student_id' => $studentID ,
            'is_finished' => 0,
            'is_started' => 0,
            'finished_time'  => null,
            'start_time' => null,
            'student_time_to_solve'  => null,
            'result'  => null,
        ];

        $examRepo = new ExamRepository($exam);

        $relatedExam = $examRepo->cloneExam($examOptions);


        $examQuestionOptions = [
            'exam_id' => $relatedExam->id,
            'is_answered' => 0,
            'student_time_to_solve' => null,
        ];

        foreach ($exam->questions as $question) {

            $examQuestion = new ExamQuestionRepository($question);

            $examQuestion->cloneQuestion($examQuestionOptions);
        }

        $data = [
            'status' => 200 ,
            'exam' => $relatedExam
        ];
        return $data;
    }
}
