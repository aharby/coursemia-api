<?php

namespace Tests\Feature;

use App\OurEdu\Feedbacks\Feedback;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbacksApiControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_post_feedback()
    {
        dump('test_post_feedback');
        
        $student = $this->authStudent();
        $this->apiSignIn($student);
        $data = [
            'data' => [
                'type' => 'feedback',
                'id'   => null,
                'attributes' => [
                    'feedback' => 'test-feedback'
                ]
            ]
        ];
        $response = $this->postJson("/api/v1/en/student/feedbacks/send-feedback", $data)->assertOk();
        $this->assertDatabaseHas('students_feedback', [
            'feedback' => 'test-feedback'
        ]);
    }
}
