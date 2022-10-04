<?php

namespace App\OurEdu\GeneralExams\Repository\GeneralExamStudent;

use App\OurEdu\GeneralExams\Models\GeneralExamStudent;
use App\OurEdu\GeneralExams\Models\GeneralExamStudentAnswer;
use Illuminate\Support\Facades\DB;

class GeneralExamStudentRepository implements GeneralExamStudentRepositoryInterface
{
    public function create($data)
    {
        return GeneralExamStudent::create($data);
    }

    public function findOrFail($examId)
    {
        return GeneralExamStudent::findOrFail($examId);
    }

    public function update($examId , $data)
    {
        return GeneralExamStudent::findOrFail($examId)->update($data);
    }

    public function findStudentExam($examId , $studentId){

       return GeneralExamStudent::where('general_exam_id' , $examId)->where('student_id' , $studentId)->first();
    }

    public function getStudentCorrectAnswersCount($examId , $studentId){
        return GeneralExamStudentAnswer::where('student_id' , $studentId)->where('general_exam_id', $examId)->where('is_correct' , 1)->count();
    }

    public function getStudentsOrder($subjectId){
         return GeneralExamStudent::where('subject_id', $subjectId)->select(DB::raw( 'student_id , AVG(result) as result_average' ))
            ->orderBy('result_average', 'desc')
            ->groupBy('student_id')
            ->get()->pluck('result_average', 'student_id')->toArray();
           
    }

}
