<?php

namespace Tests\Feature\Subject\Parent;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;

class SubjectApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_parent_can_list_subjects()
    {
        dump('test_parent_can_list_subjects');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        create(Subject::class);

        $response = $this->getJson(route('api.parent.subjects.get.index', ['language' => 'en']), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_parent_view_subject()
    {
        dump('test_parent_view_subject');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $subject = create(Subject::class);

        $response = $this->getJson(route('api.parent.subjects.view-subject', ['language' => 'en', 'subject_id' => $subject->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_parent_list_subject_media()
    {
        dump('test_parent_list_subject_media');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $subject = create(Subject::class);

        create(SubjectMedia::class, ['subject_id' => $subject->id], 2);


        $response = $this->getJson(route('api.parent.subjects.view-subject-media', ['language' => 'en', 'subject_id' => $subject->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'attributes' => [
                            'extension',
                            'url'
                        ]
                    ]
                ]
            ]);
    }
}
