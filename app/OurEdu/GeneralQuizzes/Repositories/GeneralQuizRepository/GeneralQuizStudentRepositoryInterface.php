<?php

namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\Users\User;

interface GeneralQuizStudentRepositoryInterface
{
    public function create($data);
    public function findOrFail($quizId);
    public function findStudentGeneralQuiz($quizId , $studentId);
    public function getStudentCorrectAnswersCount($quizId , $studentId);
    public function getStudentCorrectAnswersScore($quizId , $studentId);
    public function update($quizId , $data);
    public function getGeneralQuizStudents(User $student, array $data = []);
    // public function getStudentsOrder($subjectId);

}
