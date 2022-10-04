<?php

namespace Tests\Feature\Subject\ContentAuthor\Api;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class TaskApiControllerContentAuthorTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */


    public function test_pull_task()
    {
        dump('test_pull_task');
        $sme = $this->authSME();
        
        $record = factory(Subject::class)->create();
//        $subjectFormatSubject = factory(SubjectFormatSubject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $record->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $section1 = factory(SubjectFormatSubject::class)->create();

        $section2 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section3 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);

        $section4 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);


        $section5 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section6 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);

        $section7 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);


        $resourceSection2 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section2->id]);

        $resourceSection3 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section3->id]);

        $resourceSection4 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section4->id]);


        $resourceSection5 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section5->id]);

        $resourceSection6 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section6->id]);

        $resourceSection7 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section7->id]);

        ////group1
        $taskResourceSection2 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection2->id,
            'subject_format_subject_id' => $section2->id,
            'is_assigned' => 0,
        ]);

        $taskResourceSection3 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection3->id,
            'subject_format_subject_id' => $section3->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection4 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection4->id,
            'subject_format_subject_id' => $section4->id,
            'is_assigned' => 0,

        ]);


        ////////group2
        $taskResourceSection5 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection5->id,
            'subject_format_subject_id' => $section5->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection6 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection6->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection7 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection7->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 0,

        ]);

        $this->apiSignIn($contentAuthor1);

        $response = $this->post("/api/v1/en/content-author/subjects/tasks/{$taskResourceSection2->id}/pull")
            ->assertOk();


        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection2->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,

        ]);
        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection3->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection4->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection5->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection6->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection7->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);
    }

    public function test_pull_task_after_create_new_task()
    {
        dump('test_pull_task');
        $sme = $this->authSME();
        
        $record = factory(Subject::class)->create();
//        $subjectFormatSubject = factory(SubjectFormatSubject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $record->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $section1 = factory(SubjectFormatSubject::class)->create();

        $section2 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section3 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);

        $section4 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);


        $section5 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section6 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);

        $section7 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);


        $resourceSection2 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section2->id]);

        $resourceSection3 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section3->id]);

        $resourceSection4 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section4->id]);


        $resourceSection5 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section5->id]);

        $resourceSection6 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section6->id]);

        $resourceSection7 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section7->id]);

        ////group1
        $taskResourceSection2 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection2->id,
            'subject_format_subject_id' => $section2->id,
            'is_assigned' => 0,
        ]);

        $taskResourceSection3 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection3->id,
            'subject_format_subject_id' => $section3->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection4 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection4->id,
            'subject_format_subject_id' => $section4->id,
            'is_assigned' => 0,

        ]);


        ////////group2
        $taskResourceSection5 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection5->id,
            'subject_format_subject_id' => $section5->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection6 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection6->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 0,

        ]);

        $taskResourceSection7 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection7->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 0,

        ]);

        $this->apiSignIn($contentAuthor1);

        $response = $this->post("/api/v1/en/content-author/subjects/tasks/{$taskResourceSection2->id}/pull")
            ->assertOk();


        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection2->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,

        ]);
        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection3->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection4->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection5->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection6->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseMissing('content_author_task', [
            'task_id' => $taskResourceSection7->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        //test pull task on same section after another content author pull other tasks
        $resourceSection2_2 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section2->id]);
        $taskResourceSection2_2 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection2_2->id,
            'subject_format_subject_id' => $section2->id,
            'is_assigned' => 0,
        ]);

        $this->apiSignIn($contentAuthor2);

        $response = $this->post("/api/v1/en/content-author/subjects/tasks/{$taskResourceSection2_2->id}/pull")
            ->assertOk();

        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection2_2->id,
            'content_author_id' => $contentAuthor2->contentAuthor->id,

        ]);

        //old content author tasks should not change
        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection2->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,

        ]);
        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection3->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->assertDatabaseHas('content_author_task', [
            'task_id' => $taskResourceSection4->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);
    }

    public function test_get_fill_Resource()
    {
        dump('test_get_fill_Resource');
        $this->disableExceptionHandling();

        $sme = $this->authSME();
        
        $record = factory(Subject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $record->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $section1 = factory(SubjectFormatSubject::class)->create();

        $section2 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section3 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);

        $section4 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section2->id]);


        $section5 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section1->id]);

        $section6 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);

        $section7 = factory(SubjectFormatSubject::class)->create(['parent_subject_format_id' => $section5->id]);


        $resourceSection2 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section2->id]);

        $resourceSection3 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section3->id]);

        $resourceSection4 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section4->id]);


        $resourceSection5 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section5->id]);

        $resourceSection6 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section6->id]);

        $resourceSection7 = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $section7->id]);

        ////group1
        $taskResourceSection2 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection2->id,
            'subject_format_subject_id' => $section2->id,
            'is_assigned' => 1,
        ]);

        $taskResourceSection3 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection3->id,
            'subject_format_subject_id' => $section3->id,
            'is_assigned' => 1,

        ]);

        $taskResourceSection4 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection4->id,
            'subject_format_subject_id' => $section4->id,
            'is_assigned' => 1,

        ]);


        ////////group2
        $taskResourceSection5 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection5->id,
            'subject_format_subject_id' => $section5->id,
            'is_assigned' => 1,

        ]);

        $taskResourceSection6 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection6->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 1,

        ]);

        $taskResourceSection7 = factory(Task::class)->create([
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSection7->id,
            'subject_format_subject_id' => $section6->id,
            'is_assigned' => 1,

        ]);

        $contentAuthor_tasks = ContentAuthorTask::create([
            'task_id' => $taskResourceSection2->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $this->apiSignIn($contentAuthor1);
        //$token = $this->loginUsingHeader($contentAuthor1);
        $response = $this->getJson(route('api.contentAuthor.subjects.getFillResource', ['resourceId' => $resourceSection2->id, 'language' => 'en']))->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ]
                        ,
                        'relationships' => [
                            'learningResourceAcceptCriteria' => [
                                'data' => [
                                    'type',
                                    'id'
                                ]
                            ]
                        ]
                    ],
                'included' => [
                    [
                        'type',
                        'id',
                        'attributes'
                    ]
                ]
            ]
        );
    }

    public function test_content_author_can_mark_task_as_done()
    {
        $this->disableExceptionHandling();
        
        $task = factory(Task::class)->create(['is_done' => false]);

        $user = factory(User::class)->create([
            'type'  =>  UserEnums::CONTENT_AUTHOR_TYPE
        ]);

        $contentAuthor = $user->contentAuthor()->create([
            'hire_date' =>  now()
        ]);

        $this->assertFalse($task->is_done);


        $response = $this->getJson(route('api.contentAuthor.subjects.markTaskAsDone', ['language' => 'en', 'id' => $task]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('task.Task marked as done successfully')])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);

        $this->assertEquals(1, $task->fresh()->is_done);
    }
}
