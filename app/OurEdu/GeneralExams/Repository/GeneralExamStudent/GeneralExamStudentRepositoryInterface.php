<?php

namespace App\OurEdu\GeneralExams\Repository\GeneralExamStudent;

interface GeneralExamStudentRepositoryInterface
{
    public function create($data);
    public function findOrFail($examId);
    public function findStudentExam($examId , $studentId);
    public function getStudentCorrectAnswersCount($examId , $studentId);
    public function update($examId , $data);
    public function getStudentsOrder($subjectId);

}
