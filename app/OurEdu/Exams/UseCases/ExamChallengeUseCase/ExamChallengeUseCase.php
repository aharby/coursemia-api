<?php


namespace App\OurEdu\Exams\UseCases\ExamChallengeUseCase;


use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;

class ExamChallengeUseCase implements ExamChallengeUseCaseInterface
{

    private $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function create($examID, $studentID)
    {
        $exam = $this->examRepository->findOrFail($examID);

        $data = [];
        if ($studentID == $exam->student_id) {
            $data = [
                'status' => 422,
                'title' => trans("exam.you cannot challenge yourself"),
                'detail' => trans("exam.you cannot challenge yourself")
            ];
            return $data;
        }
        if ($exam->challenges()->where('student_id' , $studentID)->exists()) {
            $data = [
                'status' => 422,
                'title' => trans("exam.already took this challenge"),
                'detail' => trans("exam.already took this challenge")
            ];
            return $data;
        }

        if (!is_student_subscribed($exam->subject)) {
            $data = [
                'status' => 422,
                'title' => trans("exam.you must subscribe the exam subject to take the challenge"),
                'detail' => trans("exam.you must subscribe the exam subject to take the challenge")
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
            'is_correct_answer'=> 0
        ];

        foreach ($exam->questions as $question) {

            $examQuestion = new ExamQuestionRepository($question);

            $examQuestion->cloneQuestion($examQuestionOptions);
        }

        $examRepo->createChallenge([
            'student_id' => $studentID,
            'related_exam_id' => $relatedExam->id
        ]);


        $data = [
            'status' => 200 ,
            'exam' => $relatedExam
        ];
        return $data;
    }
}
