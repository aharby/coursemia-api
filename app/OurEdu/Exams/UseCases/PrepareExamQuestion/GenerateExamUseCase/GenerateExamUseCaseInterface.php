<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;

interface GenerateExamUseCaseInterface
{
    public function generateExam(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
    );

    public function generatePractice($student, $subjectId, $sectionIds);

    public function generateCompetition(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId
    );

    public function generateInstructorCompetition(
        $instructorId,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
        VCRSession $vcrSession
    );

    public function validateCompetitionSession(VCRSession $session);

    public function generateCourseCompetition(Course $course, $data);

}
