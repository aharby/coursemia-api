<?php

namespace Tests\Feature\Courses\Admin;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Courses\Models\Course;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Courses\Admin\Events\CourseSessionUpdated;
use App\OurEdu\Courses\Admin\Events\CourseSessionCanceled;

class CourseSessionControllerTest extends TestCase
{
    use WithFaker;

    public function test_edit_course_sessions()
    {
        dump('test_edit_course_sessions');

        Event::fake();
        $this->authAdmin();

        $courseSession = factory(CourseSession::class)->create();
        $row = factory(CourseSession::class)->make()->toArray();
        $courseSession->course->students()->saveMany(make(Student::class, [], 2));

        $response = $this
            ->put(route('admin.courseSessions.put.edit', $courseSession->id), $row);

        // Perform session updated
        Event::assertDispatched(CourseSessionUpdated::class, function ($e) use ($courseSession) {
            return $e->courseSession->id === $courseSession->id;
        });

        $this->assertEquals($row['content'], $courseSession->fresh()->content);
        $this->assertEquals($row['date'], $courseSession->fresh()->date);
    }

    public function test_cancel_course_sessions()
    {
        dump('test_cancel_course_sessions');

        Event::fake();
        $this->authAdmin();

        $courseSession = factory(CourseSession::class)->create();
        $courseSession->course->students()->saveMany(make(Student::class, [], 2));

        $response = $this
            ->get(route('admin.courseSessions.cancel', $courseSession->id));

        // Perform session updated
        Event::assertDispatched(CourseSessionCanceled::class, function ($e) use ($courseSession) {
            return $e->courseSession->id === $courseSession->id;
        });

        $this->assertEquals(CourseSessionEnums::CANCELED, $courseSession->fresh()->status);
    }
}
