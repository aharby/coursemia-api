<?php

namespace Tests\Feature\Course\Parent;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Courses\Models\Course;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_parent_can_list_subjects()
    {
        dump('test_parent_can_list_subjects');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        create(Course::class, ['is_active' => true]);

        $response = $this->getJson(route('api.parent.courses.get.index', ['language' => 'en']), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }
}
