<?php

namespace Tests\Feature;

use App\OurEdu\Feedbacks\Feedback;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedbacksControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_feedbacks()
    {
        dump('test_list_feedbacks');
        
        $this->authAdmin();
        
        $response = $this
            ->get('admin/feedbacks')
            ->assertStatus(200);
    }

    public function test_approve_feedback()
    {
        dump('test_approve_feedback');
        
        $this->authAdmin();
        $user = factory(User::class)->create(['type' => UserEnums::STUDENT_TYPE]);
        $student = Student::create(['user_id' => $user->id]);
        $feedback = Feedback::create([
            'student_id' => $student->id,
            'feedback' => 'asfsfsdfd',
            'approved' => 0
        ]);
        $response = $this
            ->get('admin/feedbacks/approve/'.$feedback->id)
            ->assertStatus(302);
        $this->assertDatabaseHas('students_feedback', [
            'approved' => 1
        ]);
    }

    public function test_delete_feedback()
    {
        dump('test_delete_feedback');
        
        $this->authAdmin();
        $user = factory(User::class)->create(['type' => UserEnums::STUDENT_TYPE]);
        $student = Student::create(['user_id' => $user->id]);
        $feedback = Feedback::create([
            'student_id' => $student->id,
            'feedback' => 'asfsfsdfd',
            'approved' => 0
        ]);
        $response = $this
            ->delete('admin/feedbacks/delete/'.$feedback->id)
            ->assertStatus(302);
    }
}
