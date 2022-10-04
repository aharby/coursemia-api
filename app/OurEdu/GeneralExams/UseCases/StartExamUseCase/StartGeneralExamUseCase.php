<?php


namespace App\OurEdu\GeneralExams\UseCases\StartExamUseCase;

use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\GeneralExamStudentAnswer;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use DateTime;

class StartGeneralExamUseCase implements StartGeneralExamUseCaseInterface
{
    private $generalExamRepository;
    private $generalExamStudentRepository;
    private $userRepository;

    public function __construct(
        GeneralExamRepositoryInterface $generalExamRepository,
        GeneralExamStudentRepositoryInterface $generalExamStudentRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->generalExamRepository = $generalExamRepository;
        $this->generalExamStudentRepository = $generalExamStudentRepository;
        $this->userRepository = $userRepository;
    }

    public function startExam(int $examId, int $studentId)
    {
        $generalExam = $this->generalExamRepository->findOrFail($examId);

        $startTime = date('Y-m-d H:i:s', strtotime("$generalExam->date $generalExam->start_time"));
        if(now() < $startTime){
            $return['status'] = 422;
            $return['detail'] = trans('api.general_exam_not_started');
            $return['title'] = 'general exam not started';
            return $return;
        }

        $student = $this->userRepository->findStudentOrFail($studentId);

        $generalExamStudent = $this->generalExamStudentRepository->findStudentExam($examId, $studentId);
        $generalExamRepository = new GeneralExamRepository($generalExam);

        if ($generalExamStudent) {

            $generalExamEndDateTime = new DateTime($generalExam->date.' '.$generalExam->end_time);
            if(new DateTime(now()) > $generalExamEndDateTime){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting general exam, general exam time finished');
                $return['title'] = 'error getting general exam, general exam time finished';
                return $return;
            }

            // get latest exam_question_answer of the current student id
            $studentId = auth()->user()->student->id;

            // then see that question number on exam questions
            $firstNonAnsweredQuestionId = GeneralExamQuestion::where('general_exam_id',$examId)->first();
            $alreadyAnswered = GeneralExamStudentAnswer::where('general_exam_question_id',$firstNonAnsweredQuestionId)->first();
            if($alreadyAnswered){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting exam');
                $return['title'] = 'error getting exam';
                return $return;
            }

            // get number of page, number of page = order of $firstNonAnsweredQuestion in exam questions
            $examQuestions = GeneralExamQuestion::where('general_exam_id',$examId);
            $totalNoOfQuestions = $examQuestions->get()->count();
            $QuestionsIds = $examQuestions->pluck('id');
            $totalNoOfAnsweredQuestions = GeneralExamStudentAnswer::whereIn('general_exam_question_id',$QuestionsIds)->where('student_id', $studentId)->count();

            $pageNo = ++$totalNoOfAnsweredQuestions;
            if($pageNo > $totalNoOfQuestions){
                $return['status'] = 422;
                $return['detail'] = trans('api.error getting general exam');
                $return['title'] = 'error getting general exam';
                return $return;
            }

            $return['status'] = 200;
            $return['message'] = trans('api.The exam started successfully');
            $return['questions'] = $generalExamRepository->returnQuestion($pageNo);

            return $return;

        } else {
            $data = [
                'student_id' => $studentId,
                'subject_id' => $generalExam->subject_id,
                'general_exam_id' => $examId
            ];

            $generalExamStudent = $this->generalExamStudentRepository->create($data);
            $return['status'] = 200;
            $return['message'] = trans('api.The exam started successfully');
            $return['questions'] = $generalExamRepository->returnQuestion(1);

            return $return;
        }
    }
}
