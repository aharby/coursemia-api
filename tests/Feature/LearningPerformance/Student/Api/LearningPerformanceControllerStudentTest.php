<?php

namespace Tests\Feature\LearningPerformance\Student\Api;

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


    public function test_get_student_order_in_subject()
    {
        dump('test_get_student_order_in_subject');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $subject = Subject::first();
        $this->getJson("/api/v1/en/student/learning-performance/subject/".$subject->id)->assertOk();
    }

}
