<?php

namespace Tests\Feature\Subject\SME\Api;

use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\Subjects\Models\SubModels\SubjectContentAuthor;
use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerSMETest extends TestCase
{
    use WithFaker;

    public function test_get_all_tasks()
    {
        dump('test_get_all_tasks');
        $sme = $this->authSME();
        $this->apiSignIn($sme);
        $subject = factory(Subject::class)->create([
            'sme_id' => $sme->id
        ]);
        $tasks = factory(Task::class, 3)->create([
            'subject_id' => $subject->id
        ]);
        $response = $this->get('api/v1/en/sme/subjects/tasks');
        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
               '*' =>[
                   'type',
                    'attributes' => [
                    'subject_id',
                    ]
               ],
            ]
        ]);
    }

    public function test_get_subject_tasks_sme()
    {
        dump('test_get_subject_tasks_sme');
        $sme = $this->authSME();
        $this->apiSignIn($sme);
        $subject = factory(Subject::class)->create([
            'is_active' => 1,
            'sme_id' => $sme->id
        ]);
        $task = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'is_active' => 1,
            'is_done' => 0
        ]);
        $response = $this->get('api/v1/en/sme/subjects/'.$subject->id.'/tasks');
        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                    'type',
                    'attributes' => [
                        'subject_id',
                    ]
                ],
            ]
        ]);
    }

    public function test_get_subject_tasks_content_author()
    {
        dump('test_get_subject_tasks_content_author');
        $contentAuthor = $this->authContentAuthor();
        $this->apiSignIn($contentAuthor);
        $subject = factory(Subject::class)->create([
            'is_active' => 1
        ]);
        SubjectContentAuthor::create([
            'subject_id' => $subject->id,
            'user_id' => $contentAuthor->id
        ]);
        $tasks = factory(Task::class, 3)->create([
            'subject_id' => $subject->id,
            'is_active' => 1,
            'is_done' => 0
        ]);
        foreach ($tasks as $task ) {
            ContentAuthorTask::create([
                'task_id' => $task->id,
                'content_author_id' => $contentAuthor->contentAuthor->id
            ]);
        }
        $response = $this->get('api/v1/en/sme/subjects/'.$subject->id.'/tasks');
        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    public function test_sme_gets_content_authors_tasks_performance()
    {
        dump('test_sme_gets_content_authors_tasks_performance');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);

        $subject = factory(Subject::class)->create([
            'sme_id'    =>  $sme->id,
            'is_active' => 1
        ]);

        $tasks = factory(Task::class, 3)->create([
            'subject_id' => $subject->id
        ]);

        $contentAuthor = create(ContentAuthor::class);

        $contentAuthor->tasks()->sync($tasks);

        $response = $this->getJson(route('api.sme.subjects.get.performance', ['language' => 'en']), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    public function test_sme_can_view_task_details()
    {
        dump('test_sme_can_view_task_details');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);

        $subject = factory(Subject::class)->create([
            'sme_id'    =>  $sme->id,
            'is_active' => 1
        ]);

        $task = factory(Task::class)->create([
            'subject_id' => $subject->id
        ]);

        $response = $this->getJson(route('api.sme.subjects.get.view', ['language' => 'en', 'id' => $task->id]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['type' => 'task'])
            ->assertJsonStructure([
                'data' => []
            ]);
    }
}
