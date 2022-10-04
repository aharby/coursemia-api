<?php

namespace Tests\Feature\Invitations\Student\Api;

use Carbon\Carbon;
use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailTemplate;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Mail;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\StudentTeacher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Users\Models\StudentTeacherStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Invitations\Notifications\InvitationNotification;

class InvitationControllerTest extends TestCase
{
    use WithFaker;

    public function test_student_search_perants()
    {
        $this->disableExceptionHandling();

        dump('test_student_search_perants');

        $parent = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $student = create(Student::class);
        $user = $student->user;


        $response = $this->getJson(route('api.invitations.search', ['q' => $parent->email, 'language' => 'en', 'type' => 'parent']), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['email' => $parent->email])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_parent_search_students()
    {
        $this->disableExceptionHandling();

        dump('test_parent_search_students');

        $parent = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $student = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $response = $this->getJson(route('api.invitations.search', ['q' => $student->email, 'language' => 'en', 'type' => 'student']), $this->loginUsingHeader($parent))
            ->assertOk()
            ->assertJsonFragment(['email' => $student->email])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_student_search_teachers()
    {
        $this->disableExceptionHandling();

        dump('test_student_search_teachers');

        $teacher = create(User::class, ['type' => UserEnums::STUDENT_TEACHER_TYPE]);

        $student = create(Student::class);
        $user = $student->user;


        $response = $this->getJson(route('api.invitations.search', ['q' => $teacher->email, 'language' => 'en', 'type' => 'student_teacher']), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['email' => $teacher->email])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_student_invite_perant()
    {
        dump('test_student_invite_perant');

        $this->disableExceptionHandling();

        Mail::fake();
        Mail::assertNothingSent();

        $student = create(Student::class);
        $user = $student->user;

        $email = $this->faker->email;

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $email
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $email, 'type' => 'parent']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);
        // getting the now time object
        $timeObj = Carbon::now()->toObject();


        // only once every 2 minutes
        if ($timeObj->minute - Carbon::now()->toObject()->minute == 2) {
            $response = $this->postJson(route('api.invitations.invite', ['language' => 'en']), $request_data, $this->loginUsingHeader($user))
                ->assertStatus(422)
                ->assertJsonFragment(['detail' => trans('api.You may send the person invitation once every 2 minutes')]);
        }
        Mail::assertQueued(MailTemplate::class, 1);

        Notification::fake();

        // sending to existing user
        $invitedUser = create(User::class);

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $invitedUser->email
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $invitedUser->email, 'type' => 'parent']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);


        Notification::assertSentTo(
            [$invitedUser],
            InvitationNotification::class
        );
    }

    public function test_parent_invite_student()
    {
        dump('test_parent_invite_student');

        Mail::fake();
        Mail::assertNothingSent();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $email = $this->faker->email;

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $email
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $email, 'type' => 'student']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);
        // getting the now time object
        $timeObj = Carbon::now()->toObject();


        // only once every 2 minutes
        if ($timeObj->minute - Carbon::now()->toObject()->minute == 2) {
            $response = $this->postJson(route('api.invitations.invite', ['language' => 'en']), $request_data, $this->loginUsingHeader($user))
                ->assertStatus(422)
                ->assertJsonFragment(['detail' => trans('api.You may send the person invitation once every 2 minutes')]);
        }


        Mail::assertQueued(MailTemplate::class, 1);

        Notification::fake();

        // sending to existing user
        $invitedUser = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $invitedUser->email
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $invitedUser->email, 'type' => 'student']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);


        Notification::assertSentTo(
            [$invitedUser],
            InvitationNotification::class
        );
    }

    public function test_student_invite_teacher()
    {
        dump('test_student_invite_teacher');

        $this->disableExceptionHandling();

        Mail::fake();
        Mail::assertNothingSent();

        $student = create(Student::class);
        $user = $student->user;

        $subjects = create(Subject::class, [], 2);

        $student->subjects()->sync($subjects);

        $email = $this->faker->email;

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $email,
                    'subjects'  =>  $subjects->pluck('id')
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $email, 'type' => 'student_teacher']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);
        // getting the now time object
        $timeObj = Carbon::now()->toObject();


        // only once every 2 minutes
        if ($timeObj->minute - Carbon::now()->toObject()->minute == 2) {
            $response = $this->postJson(route('api.invitations.invite', ['language' => 'en']), $request_data, $this->loginUsingHeader($user))
                ->assertStatus(422)
                ->assertJsonFragment(['detail' => trans('api.You may send the person invitation once every 2 minutes')]);
        }

        Mail::assertQueued(MailTemplate::class, 1);

        $this->assertDatabaseHas('invitations', [
            'sender_id' =>  $user->id,
            'receiver_email'    =>  $email
        ]);

        $invitation = Invitation::where([
            'sender_id' =>  $user->id,
            'receiver_email'    =>  $email
        ])->first();

        $this->assertCount(2, $invitation->subjects);

        Notification::fake();

        // sending to existing user
        $invitedUser = create(User::class);

        $request_data = [
            'data'  =>  [
                'type'  =>  'invitation',
                'id'    =>  null,
                'attributes'    =>  [
                    'email' =>  $invitedUser->email,
                    'subjects'  =>  $subjects->pluck('id')
                ]
            ]
        ];

        $response = $this->postJson(route('api.invitations.invite', ['language' => 'en', 'email' =>  $invitedUser->email, 'type' => 'student_teacher']), $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200);


        Notification::assertSentTo(
            [$invitedUser],
            InvitationNotification::class
        );

        $this->assertDatabaseHas('invitations', [
            'sender_id' =>  $user->id,
            'receiver_email'    =>  $invitedUser->email
        ]);

        $invitation = Invitation::where([
            'sender_id' =>  $user->id,
            'receiver_email'    =>  $invitedUser->email
        ])->first();

        $this->assertCount(2, $invitation->subjects);
    }

    public function test_user_cancel_invitation_request()
    {
        dump('test_user_cancel_invitation_request');

        $invitation = create(Invitation::class);

        $this->assertSame(InvitationEnums::PENDING, $invitation->status);

        $response = $this->getJson(route('api.invitations.cancelInviation', ['language' => 'en', 'id' => $invitation->id]), $this->loginUsingHeader($invitation->sender))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('api.Invitation canceled')]);

        $this->assertSame(InvitationEnums::CANCELED, $invitation->fresh()->status);

        $response = $this->getJson(route('api.invitations.cancelInviation', ['language' => 'en', 'id' => $invitation->id]), $this->loginUsingHeader($invitation->sender))
            ->assertStatus(422)
            ->assertJsonFragment(['detail' => trans('api.Invitation is not pending to perform action')]);
    }

    public function test_receiver_accept_invitation_request()
    {
        dump('test_receiver_accept_invitation_request');

        $invitation = create(Invitation::class);

        $this->assertSame(InvitationEnums::PENDING, $invitation->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]), $this->loginUsingHeader($invitation->receiver))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('api.Invitation status updated')]);

        $this->assertSame(InvitationEnums::ACCEPTED, $invitation->fresh()->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]), $this->loginUsingHeader($invitation->sender))
            ->assertStatus(422)
            ->assertJsonFragment(['detail' => trans('api.Invitation is not pending to perform action')]);
    }

    public function test_receiver_refuse_invitation_request()
    {
        dump('test_receiver_refuse_invitation_request');

        $invitation = create(Invitation::class);

        $this->assertSame(InvitationEnums::PENDING, $invitation->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::REFUSED]), $this->loginUsingHeader($invitation->receiver))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('api.Invitation status updated')]);

        $this->assertSame(InvitationEnums::REFUSED, $invitation->fresh()->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::REFUSED]), $this->loginUsingHeader($invitation->sender))
            ->assertStatus(422)
            ->assertJsonFragment(['detail' => trans('api.Invitation is not pending to perform action')]);
    }

    public function test_teacher_accepts_student_invitation()
    {
        dump('test_teacher_accepts_student_invitation');

        $teacherObj = create(StudentTeacher::class);
        $teacher = $teacherObj->user;

        $student = create(Student::class);
        $sender = $student->user;

        $subjects = create(Subject::class, [], 2);

        $student->subjects()->sync($subjects);

        $invitation = create(Invitation::class, [
            'sender_id' =>  $sender->id,
            'receiver_email' =>  $teacher->email,
        ]);

        $invitation->subjects()->sync($subjects);

        $this->assertSame(InvitationEnums::PENDING, $invitation->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]), $this->loginUsingHeader($invitation->receiver))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('api.Invitation status updated')]);

        $this->assertSame(InvitationEnums::ACCEPTED, $invitation->fresh()->status);

        $response = $this->getJson(route('api.invitations.changeStatus', ['language' => 'en', 'id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]), $this->loginUsingHeader($invitation->sender))
            ->assertStatus(422)
            ->assertJsonFragment(['detail' => trans('api.Invitation is not pending to perform action')]);

        $relationalTable = StudentTeacherStudent::where([
            'student_teacher_id'    =>  $teacher->id,
            'student_id'    =>  $sender->id
        ])->first();

        $this->assertCount(2, $relationalTable->subjects);
    }

    public function test_resend_invitation()
    {
        dump('test_resend_invitation');
        $this->disableExceptionHandling();

        $invitation = create(Invitation::class);

        $this->assertSame(InvitationEnums::PENDING, $invitation->status);

        Notification::fake();

        $response = $this->postJson(route('api.invitations.resendInvite', ['language' => 'en', 'id' =>  $invitation, 'type' => $invitation->receiver->type]), [], $this->loginUsingHeader($invitation->sender))
            ->assertStatus(200);

        Notification::assertSentTo(
            [$invitation->receiver],
            InvitationNotification::class
        );
    }
}
