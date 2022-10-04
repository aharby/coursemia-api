<?php


namespace App\OurEdu\Exams\UseCases\FinishExamUseCase;

use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;
use App\OurEdu\Exams\Student\Jobs\StudentFinishedCompetitionJob;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;
use App\OurEdu\Users\Models\Student;
use Carbon\Carbon;
class FinishExamUseCase implements FinishExamUseCaseInterface
{
    private $repositoryExam;
    private $repositoryExamQuestion;
    protected $requestLiveSessionUseCase;
    protected $notifierFactory;


    public function __construct(
        ExamRepositoryInterface $ExamRepositry,
        ExamQuestionRepository $examQuestionRepository,
        RequestLiveSessionUseCaseInterface $requestLiveSessionUseCase,
        NotifierFactoryInterface $notifierFactory

    )
    {
        $this->repositoryExam = $ExamRepositry;
        $this->repositoryExamQuestion = $examQuestionRepository;
        $this->requestLiveSessionUseCase = $requestLiveSessionUseCase;
        $this->notifierFactory = $notifierFactory;

    }

    public function finishExam(int $examId)
    {
        $exam = $this->repositoryExam->findOrFail($examId);

        $validationErrors = $this->validateFinishExam($exam);

        if ($validationErrors) {

            return $validationErrors;
        }

        $total = $exam->questions()->count();
        $correctAnswers = $exam->questions()->where('is_correct_answer', 1)->count();

        $percentage = $total ? ($correctAnswers / $total) * 100 : 0;
        $finishedTime = now();

        $data=[
            'is_finished' => 1,
            'finished_time' => $finishedTime,
            'result'    =>  number_format($percentage, 2, '.', '')
        ];

        $studentTimeToSolve = $finishedTime->diffInSeconds($exam->start_time);
        $solvingSpeedPercentage = 0;
        if ($exam->time_to_solve > 0) {
            $solvingSpeedPercentage = (number_format($studentTimeToSolve, 2, '.', '') / $exam->time_to_solve) * 100;
        }

        $validationErrors = $this->validateFinishExam($exam);

        if ($validationErrors) {

            return $validationErrors;
        }

        $exam->update([
            'student_time_to_solve' =>  number_format($studentTimeToSolve, 2, '.', ''),
            'solving_speed_percentage' => $solvingSpeedPercentage
        ]);

//        $this->sumExamTimeToSolve($exam);

        $this->repositoryExam->update($exam, $data);
        $return['status'] = 200;
        $return['exam_data'] = [
            'exam_title' => $exam->title,
            'difficulty_level' => $exam->difficulty_level,
            'questions_number' => $exam->questions_number,
            'subject_id' => $exam->subject_id,
        ];
        if ($exam->type == ExamTypes::EXAM){
            $return['exam_data']['result'] = number_format($percentage, 2, '.', '');
        }
        $return['message'] = trans('api.The exam finished successfully');

        return $return;
    }

    private function validateFinishExam($exam)
    {
        if ($exam->type == ExamTypes::EXAM && $exam->is_started == 0) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is not started yet');
            $return['title'] = 'The exam is not started yet';
            return $return;
        }

        if ($exam->is_finished == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is already finished');
            $return['title'] = 'The exam is already finished';
            return $return;
        }

        if ($exam->type == ExamTypes::EXAM &&
            Carbon::now() > Carbon::createFromTimeString($exam->start_time)->addSeconds($exam->time_to_solve)->addSeconds(ExamEnums::FINISH_EXAM_TOLERANCE_TIME)) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam time has passed');
            $return['title'] = 'The exam time has passed';
            return $return;
        }
    }

    public function finishExamCompetition(int $examId)
    {
        $exam = $this->repositoryExam->findOrFail($examId);
//        $return['status'] = 200;
//        $return['message'] = trans('api.The exam finished successfully');
//        return $return;
        $data=[
            'is_finished' => 1,
            'finished_time' => now()
        ];
//        if ($exam->is_finished == 1)
//        {
//            $return['status'] = 422;
//            $return['detail'] = trans('api.The exam is already finished');
//            $return['title'] = 'The exam is already finished';
//            return $return;
//        }
//        else{
        $this->repositoryExam->update($exam, $data);
//            $return['status'] = 200;
//            $return['message'] = trans('api.The exam finished successfully');
//
//            return $return;
//        }





        foreach ($exam->competitionStudents as $student) {
            $results = [];
            foreach ($exam->questions as $question) {
                $results[] = CompetitionQuestionStudent::where('exam_id', $exam->id)
                        ->where('exam_question_id', $question->id)
                        ->where('student_id', $student->id)->first()->is_correct_answer ?? 0;
            }
            $studentsResults[]=[
                'id'=>$student->id,
                'name'=>$student->user->name,
                'results'=>$results,
            ];
            $exam->competitionStudents()->where('student_id',$student->id)->update(['result' => array_sum($results)]);
        }

        foreach ($studentsResults as &$studentResults) {
            $studentResults['avg']=  $this->array_avg($studentResults['results'])[1]['avg'] ??0;
        }


        $avg = array_column($studentsResults, 'avg');

        array_multisort($avg, SORT_DESC, $studentsResults);
        return $studentsResults;
    }
    public function array_avg($array, $round = 1)
    {
        $num = count($array);
        return array_map(
            function ($val) use ($num, $round) {
                return array('count' => $val, 'avg' => round($val / $num * 100, $round));
            },
            array_count_values($array)
        );
    }

    protected function sumExamTimeToSolve($exam)
    {
        $exam->load('questions');

        $studentTimeToSolve = $exam->questions->sum('student_time_to_solve');

        $exam->update([
            'student_time_to_solve' =>  number_format($studentTimeToSolve, 2, '.', '')
        ]);
    }

    public function finishInstructorCompetition(int $competitionId)
    {
        $exam = $this->repositoryExam->findOrFail($competitionId);
        $data=[
            'is_finished' => 1,
            'finished_time' => now()
        ];
        if ($exam->is_finished == 1)
        {
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is already finished');
            $return['title'] = 'The exam is already finished';
            return $return;
        }
        else{
        $this->repositoryExam->update($exam, $data);
            $return['status'] = 200;
            $return['message'] = trans('api.The competition finished successfully');

            return $return;
        }


//
//
//
//        foreach ($exam->competitionStudents as $student) {
//            $results = [];
//            foreach ($exam->questions as $question) {
//                $results[] = CompetitionQuestionStudent::where('exam_id', $exam->id)
//                        ->where('exam_question_id', $question->id)
//                        ->where('student_id', $student->id)->first()->is_correct_answer ?? 0;
//            }
//            $studentsResults[]=[
//                'id'=>$student->id,
//                'name'=>$student->user->name,
//                'results'=>$results,
//            ];
//        }
//
//        foreach ($studentsResults as &$studentResults) {
//            $studentResults['avg']=  $this->array_avg($studentResults['results'])[1]['avg'] ??0;
//        }
//
//
//        $avg = array_column($studentsResults, 'avg');
//
//        array_multisort($avg, SORT_DESC, $studentsResults);
//        return $studentsResults;
    }

    public function getStudentOrderInCompetition(Exam $exam, Student $student)
    {
        $students = $exam->competitionStudents()
            ->with('competitionStudent',function ($query) use ($exam){
                $query->where('exam_id',$exam->id);
            })
            ->orderByPivot('result', 'DESC')->get();

        return $students;
    }

    public function getStudentBulkOrderInCompetition(Exam $exam, Student $student)
    {
        $students = $exam->competitionStudents()
            ->with('competitionStudent',function ($query) use ($exam){
                $query->where('exam_id',$exam->id);
            })
            ->orderByPivot('result', 'DESC')->get()->groupBy('pivot.result');


        foreach ($students as $key =>  $competitionStudent) {

            $pivot = $competitionStudent->first()->pivot;
            $competitionStudent->rank = $competitionStudent->first()->pivot->rank;
            $competitionStudent->exam = $exam;
            $competitionStudent->result = $pivot->result;
            $competitionStudent->is_finished =$pivot->is_finished;
        }
        return $students;
    }
    public function getAllStudentsCompetition(Exam $exam)
    {
        return $exam->competitionStudents()->count();
    }

    public function getFinishedStudentsInCompetition(Exam $exam)
    {
        return $exam->competitionStudents()->whereNotNull(['result'])->count();
    }
}

