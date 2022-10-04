<?php

namespace Tests\Feature\Invitations\Student\Api;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subscribes\Subscription;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_parent_can_subscripte_for_student_on_a_course()
    {
        dump('test_parent_can_subscripte_for_student_on_a_course');

        $student = create(Student::class);
        $studentUser = $student->user;

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $user->students()->sync($studentUser);

        $request_data = [
            'data'  =>  [
                'type'  =>  'subscription',
                'id'    =>  null,
                'attributes'    =>  [
                    'user_id' =>  $studentUser->id
                ]
            ]
        ];

        $this->assertCount(0, Subscription::where('user_id', $studentUser->id)->get());

        $course = factory(Course::class)->create([
            'is_active' => 1
        ]);

        $response = $this->postJson(route('api.parent.subscriptions.post.courseSubscripe', ['language' => 'en', 'id' => $course]), $request_data, $this->loginUsingHeader($user));
        $response->assertStatus(200);

        $this->assertCount(1, Subscription::where('user_id', $studentUser->id)->get());
    }

    public function test_parent_can_subscripte_for_student_on_a_subject()
    {
        dump('test_parent_can_subscripte_for_student_on_a_subject');

        $student = create(Student::class);
        $studentUser = $student->user;

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $user->students()->sync($studentUser);

        $request_data = [
            'data'  =>  [
                'type'  =>  'subscription',
                'id'    =>  null,
                'attributes'    =>  [
                    'user_id' =>  $studentUser->id
                ]
            ]
        ];

        $this->assertCount(0, Subscription::where('user_id', $studentUser->id)->get());

        $subject = create(Subject::class);

        $response = $this->postJson(route('api.parent.subscriptions.post.subjectSubscripe', ['language' => 'en', 'id' => $subject]), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);

        $this->assertCount(1, Subscription::where('user_id', $studentUser->id)->get());
    }

    public function test_user_list_subscriptions()
    {
        dump('test_user_list_subscriptions');

        $this->disableExceptionHandling();

        $student = create(Student::class);
        $studentUser = $student->user;

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $user->students()->sync($student->user_id);

        $course = create(Course::class);

        $course->subscriptions()->create(['user_id' => $student->user_id]);

        $response = $this->getJson(route('api.parent.subscriptions.userSubscriptions', ['language' => 'en', 'userId' => $student->user_id]), $this->loginUsingHeader($user));
        $response->assertOk()
            ->assertJsonFragment(['total' => 1])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_parent_pays_for_student_course_subscription()
    {
        dump('test_parent_pays_for_student_course_subscription');

        $this->disableExceptionHandling();

        $student = create(Student::class, ['wallet_amount' => 10]);
        $studentUser = $student->user;

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $user->students()->sync($student->user_id);

        $course = factory(Course::class)->create([
            'is_active' => 1,
            'subscription_cost' => 10
        ]);

        $this->assertCount(0, $course->students);

        $subscription = $course->subscriptions()->create(['user_id' => $student->user_id]);

        $response = $this->getJson(route('api.parent.subscriptions.subscriptionPayment', ['language' => 'en', 'id' => $subscription->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Thanks for pruchase')])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);

        $this->assertCount(1, $course->fresh()->students);

        $this->assertTrue($course->students()->where('students.id', $student->id)->exists());

        $this->assertSame(0.0, $student->fresh()->wallet_amount);
    }


    public function test_parent_pays_for_student_subject_subscription()
    {
        dump('test_parent_pays_for_student_subject_subscription');

        $this->disableExceptionHandling();

        $student = create(Student::class, ['wallet_amount' => 10]);
        $studentUser = $student->user;

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $user->students()->sync($student->user_id);

        $subject = create(Subject::class, ['subscription_cost' => 10]);

        $this->assertCount(0, $subject->students);

        $subscription = $subject->subscriptions()->create(['user_id' => $student->user_id]);

        $response = $this->getJson(route('api.parent.subscriptions.subscriptionPayment', ['language' => 'en', 'id' => $subscription->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Thanks for pruchase')])
            ->assertJsonFragment(['order_key' => $user->orders->first()->order_key])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);

        $this->assertCount(1, $subject->fresh()->students);
        $this->assertCount(1, $user->orders);

        $this->assertTrue($subject->students()->where('students.id', $student->id)->exists());

        $this->assertSame(0.0, $student->fresh()->wallet_amount);
    }
}
