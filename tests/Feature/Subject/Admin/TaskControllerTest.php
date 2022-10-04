<?php

namespace Tests\Feature\Subject\Admin;


use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskControllerTest extends TestCase
{

    use WithFaker;

    public function test_list_subject_tasks()
    {
        dump('test_list_subject_tasks');

        $this->authAdmin();
        $response = $this
            ->get('/admin/subjects/tasks')
            ->assertStatus(200);
    }

    public function test_content_author_task()
    {
        dump('test_content_author_task');

        $this->authAdmin();
        $response = $this
            ->get('/admin/tasks/content-author')
            ->assertStatus(200);
    }

    public function test_content_author_task_details()
    {
        dump('test_content_author_task_details');
        $author = User::where('type' , UserEnums::CONTENT_AUTHOR_TYPE)->first();
        $this->authAdmin();
        $response = $this
            ->get('/admin/tasks/content-author/' . $author->id)
            ->assertStatus(200);
    }
}
