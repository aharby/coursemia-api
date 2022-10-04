<?php

namespace Tests\Feature\Courses\Admin;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\OurEdu\Courses\Models\Course;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Courses\Models\SubModels\CourseSession;

class CoursesControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_courses()
    {
        dump('test_list_courses');
        $this->authAdmin();

        create(CourseSession::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.courses.get.index'))
            ->assertStatus(200);
    }

    public function test_create_courses()
    {
        dump('test_create_courses');
        $this->disableExceptionHandling();

        $this->authAdmin();

        $row = factory(Course::class)->make()->toArray();

        $row['sessions'] = make(CourseSession::class, [], 2)->toArray();

        $row['picture'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this
            ->withSession(['locale' => 'en'])
            ->post(route('admin.courses.post.create'), $row);

        $this->assertDatabaseHas('courses', [
            'name' => $row['name'],
            'instructor_id' => $row['instructor_id'],
            'subject_id' => $row['subject_id'],
        ]);
    }


    public function test_edit_courses()
    {
        dump('test_edit_courses');

        $this->authAdmin();

        $record = factory(Course::class)->create();
        $row = factory(Course::class)->make()->toArray();

        $response = $this
            ->put(route('admin.courses.put.edit', $record->id), $row);

        $record = Course::find($record->id);

        $this->assertEquals($row['name'], $record->fresh()->name);
        $this->assertEquals($row['subject_id'], $record->fresh()->subject_id);
    }

    public function test_delete_courses()
    {
        dump('test_delete_courses');

        $this->authAdmin();

        $record = Course::create(factory(Course::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.courses.delete', $record->id));

        $this->assertSoftDeleted('courses', [
            'id' => $record->id,
            'subject_id' => $record->subject_id,
            'instructor_id' => $record->instructor_id,

        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);
    }

    public function test_list_course_sessions()
    {
        dump('test_list_course_sessions');
        $this->authAdmin();

        $session = create(CourseSession::class);

        $response = $this->withSession(['locale' => 'en'])
            ->get(route('admin.courses.get.course.sessions', $session->course))
            ->assertOk();

        $response->assertSee($session->content);
    }
}
