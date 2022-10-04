<?php

namespace Tests\Feature\LearningPerformance\StudentTeacher\Api;

use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LearningPerformanceControllerStudentTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */


    public function test_get_student_performance_in_subject()
    {
        dump('test_get_student_performance_in_subject');

        $studentTeacher = $this->authStudentTeacher();
        $student = $this->authStudent();
        $this->apiSignIn($studentTeacher);
        $subject = Subject::first();
        $this->getJson("/api/v1/en/student-teacher/learning-performance/subject-performance/".$student->id."/".$subject->id)->assertOk();
    }

}
