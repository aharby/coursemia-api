<?php


namespace App\OurEdu\Exams\UseCases\StartExamUseCase;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use DateTime;

class StartExamUseCase implements StartExamUseCaseInterface
{
    private $repository;

    public function __construct(ExamRepositoryInterface $ExamRepositry)
    {
        $this->repository = $ExamRepositry;
    }

    public function startExam(int $examId)
    {
        $exam = $this->repository->findOrFail($examId);

        if ($exam->is_finished == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is already finished');
            $return['title'] = 'The exam is already finished';
            return $return;
        }

//        if ($exam->is_started == 1) {
//            $return['status'] = 422;
//            $return['detail'] = trans('api.The exam is already started');
//            $return['title'] = 'The exam is already started';
//            return $return;
//        }

        $data = [
            'is_started' => 1,
            'start_time' => now()
        ];

        $examRepo = new ExamRepository($exam);

        if ($exam->is_started == 1 && $exam->start_time != null) {

            // if it is not practice don't apply time check
            if($exam->time_to_solve != null){
                $timeToSolve = (int) $exam->time_to_solve;
                $examStartTimePlusTimeToSolve = (new DateTime($exam->start_time))->modify('+'.$timeToSolve.' sec');

                if(new DateTime(now()) > $examStartTimePlusTimeToSolve){
                    $return['status'] = 422;
                    $return['detail'] = trans('api.error getting exam, exam time finished');
                    $return['title'] = 'error getting exam, exam time finished';
                    return $return;
                }
            }


            // get latest exam_question_answer of the current student id
            $studentId = auth()->user()->student->id;
            $question_answer=ExamQuestionAnswer::where('student_id',$studentId)->orderBy('created_at','desc')
                ->limit(1)->first();
            $questionId = $question_answer?->question_id;
            // then get its exam_question id
            $examQuestion = ExamQuestion::find($questionId);

            if($examQuestion?->exam_id > $examId ){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting exam, maybe you started another exam');
                $return['title'] = 'error getting exam, maybe you started another exam';
                return $return;
            }
            // then see that question number on exam questions
            $firstNonAnsweredQuestionId = ExamQuestion::where('exam_id',$examId)->where('id','>',$questionId)->first();
            $alreadyAnswered = ExamQuestionAnswer::where('question_id',$firstNonAnsweredQuestionId)->first();

            if($alreadyAnswered){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting exam');
                $return['title'] = 'error getting exam';
                return $return;
            }
            // get number of page, number of page = order of $firstNonAnsweredQuestion in exam questions
            $examQuestions = ExamQuestion::where('exam_id',$examId);
            $totalNoOfQuestions = $examQuestions->get()->count();
            $QuestionsIds = $examQuestions->pluck('id');
            $totalNoOfAnsweredQuestions = ExamQuestionAnswer::whereIn('question_id',$QuestionsIds)->where('student_id', $studentId)->count();

//            $pageNo = $totalNoOfQuestions - $totalNoOfAnsweredQuestions;
            $pageNo = ++$totalNoOfAnsweredQuestions;
            if($pageNo > $totalNoOfQuestions){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting exam');
                $return['title'] = 'error getting exam';
                return $return;

            }
            $return['status'] = 200;
            $return['exam_data'] = [
                'exam_title' => $exam->title,
                'difficulty_level' => $exam->difficulty_level,
                'questions_number' => $exam->questions_number,
                'subject_id' => $exam->subject_id,
            ];
            $return['message'] = trans('api.The exam started successfully');
            $return['questions'] = $examRepo->returnQuestion($pageNo);
            return $return;
        } else {

            if ($exam->type == ExamTypes::COMPETITION) {
                if ($examRepo->competitionStudentsCount() <= 1) {
                    $return['status'] = 422;
                    $return['detail'] = trans('api.The competition must be two students');
                    $return['title'] = 'The competition must be two students';
                    return $return;
                }
            }
            $this->repository->update($exam, $data);
            $return['status'] = 200;
            $return['exam_data'] = [
                'exam_title' => $exam->title,
                'difficulty_level' => $exam->difficulty_level,
                'questions_number' => $exam->questions_number,
                'subject_id' => $exam->subject_id,
            ];
            $return['message'] = trans('api.The exam started successfully');
            $return['questions'] = $examRepo->returnQuestion(1);
            return $return;
        }
    }

    public function startInstructorCompetition(int $competitionId)
    {
        $exam = $this->repository->findOrFail($competitionId);
        $data = [
            'is_started' => 1,
            'start_time' => now()
        ];

        if ($exam->is_started == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The competition is already started');
            $return['title'] = 'The competition is already started';
            return $return;
        } else {
            $this->repository->update($exam, $data);
            $return['status'] = 200;
            $return['message'] = trans('api.The competition started successfully');
            return $return;
        }
    }
}
