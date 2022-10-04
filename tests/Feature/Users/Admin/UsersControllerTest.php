<?php

namespace Tests\Feature\Users\Admin;

use App\OurEdu\Schools\School;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use WithFaker;


    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_users()
    {
        dump('test_list_users');
        $this->authAdmin();
        $this
            ->get('admin/users')
            ->assertStatus(200);
    }

    public function test_create_user()
    {
        dump('test_create_admin_user');

        $this->authAdmin();

        $row = factory(User::class)->make()->toArray();
        $row['password_confirmation'] = $row['password'];
        if ($row['type'] == UserEnums::CONTENT_AUTHOR_TYPE) {
            $row['hire_date'] = date('Y-m-d');
        }

        if ($row['type'] == UserEnums::INSTRUCTOR_TYPE) {
            $row['hire_date'] = date('Y-m-d');
            $row['school_id'] = School::first()->id;
        }
        $response = $this->post('admin/users/create/', $row);
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
             'first_name' => $row['first_name'],
             'email' => $row['email'],
             'type' =>  $row['type']
         ]);
    }


    public function test_edit_user()
    {
        dump('test_edit_users');

        $this->authAdmin();
        $record = factory(User::class)->create();
        $row = factory(User::class)->make()->toArray();
        $row['type'] = $record->type;
        $row['password_confirmation'] = $row['password'];
        if ($row['type'] == UserEnums::CONTENT_AUTHOR_TYPE) {
            $row['hire_date'] = date('Y-m-d');
        }
        if ($row['type'] == UserEnums::INSTRUCTOR_TYPE) {
            $row['hire_date'] = date('Y-m-d');
            $row['school_id'] = School::first()->id;
        }
        $response = $this->put('admin/users/edit/'.$record->id, $row);
        $record = User::find($record->id);
        $this->assertDatabaseHas('users', [
              'first_name' => $row['first_name'],
              'email' => $row['email'],
              'type' =>  $row['type']
            ]);
    }

    public function test_delete_users()
    {
        dump('test_delete_users');

        $this->authAdmin();
        $record = factory(User::class)->create();

        if ($record->type==UserEnums::CONTENT_AUTHOR_TYPE) {
            $record->contentAuthor()->create(['hire_date'=>'2015-01-01','user_id'=>$record->id]);
        }

        if ($record->type==UserEnums::INSTRUCTOR_TYPE) {
            Instructor::create(['hire_date'=>'2015-01-01','user_id'=>$record->id]);
        }


        $response = $this
                 ->delete('admin/users/delete/'.$record->id);
        $this->assertSoftDeleted('users', ['id' => $record->id]);
        $response->assertStatus(302);
    }

    public function test_suspend_users()
    {
        dump('test_suspend_users');
        $this->authAdmin();

        $record = factory(User::class)->create(['type'=> UserEnums::SME_TYPE]);
        $response = $this->post('admin/users/suspend/'.$record->id);
        $response->assertStatus(302);
    }

    public function test_add_student_teacher_to_student()
    {
        dump('test_add_student_teacher_to_student');
        $this->authAdmin();

        $student = factory(User::class)->create(['type'=> UserEnums::STUDENT_TYPE]);
        $studentTeacher = factory(User::class)->create(['type'=> UserEnums::STUDENT_TEACHER_TYPE]);
        $subject = factory(Subject::class)->create();
        $data['subject_id'] = $subject->id;
        $data['student_teacher_id'] = $studentTeacher->id;
        $response = $this->post('admin/users/add-student-teacher/'.$student->id, $data);
        $response->assertStatus(302);
    }

    public function test_detach_student_teacher_from_student()
    {
        dump('test_detach_student_teacher_from_student');
        $this->authAdmin();

        $student = factory(User::class)->create(['type'=> UserEnums::STUDENT_TYPE]);
        $studentTeacher = factory(User::class)->create(['type'=> UserEnums::STUDENT_TEACHER_TYPE]);
        $subject = factory(Subject::class)->create();
        $data['subject_id'] = $subject->id;
        $data['student_teacher_id'] = $studentTeacher->id;
        $response = $this->post('admin/users/add-student-teacher/'.$student->id, $data);
        $response = $this->delete('admin/users/detach-student-teacher/'.$student->id.'/'.$studentTeacher->id);
        $response->assertStatus(302);
    }
}
