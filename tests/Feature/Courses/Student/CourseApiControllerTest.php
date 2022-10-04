<?php

namespace Tests\Feature\Courses;

use App\OurEdu\Courses\Models\SubModels\CourseSession;
use Tests\TestCase;
use App\OurEdu\Ratings\Rating;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Mail;
use App\OurEdu\Courses\Models\Course;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_student_can_list_available_courses()
    {
        $this->disableExceptionHandling();

        dump('test_student_can_list_available_courses');

        $student = create(Student::class);

        $user = $student->user;

        $course = create(Course::class, ['is_active' => true]);

        $student->subjects()->sync($course->subject_id);

        $response = $this->getJson(route('api.student.courses.listCourses', ['language' => 'en', 'student' => $student->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'id',
                        'attributes'    =>  [
                            'name'
                        ]
                    ]
                ]
            ]);
    }

    public function test_user_can_view_course()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_view_course');

        $student = create(Student::class);

        $user = $student->user;

        $course = create(Course::class, ['is_active' => true]);

        $response = $this->getJson(route('api.student.courses.show', ['language' => 'en', 'id' => $course->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                        'type',
                        'id',
                        'attributes'    =>  [
                            'name',
                            'subscription_cost',
                            'subject_id',
                        ]
                    ]
            ]);
    }


    public function test_student_can_view_instructor_rating()
    {
        $this->disableExceptionHandling();

        dump('test_student_can_view_instructor_rating');

        $student = create(Student::class);

        $user = $student->user;

        $course = create(Course::class, ['is_active' => true]);

        create(Rating::class, ['instructor_id' => $course->instructor_id]);

        $response = $this->getJson(route('api.student.courses.instructorProfile', ['language' => 'en', 'id' => $course->instructor_id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" =>
                        [
                            'type',
                            'id',
                            'attributes'    =>  [
                                'first_name',
                                'last_name',
                            ]
                        ]

            ]);
    }
    public function test_user_can_view_course_session()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_view_course_session');

        $student = create(Student::class);

        $user = $student->user;

        $courseSession = create(CourseSession::class);

        $response = $this->getJson(route('api.student.courses.courseSession', ['language' => 'en', 'id' => $courseSession->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    'type',
                    'id',
                    'attributes'    =>  [
                        'course_id',
                        'date',
                        'content',
                        'start_time',
                        'end_time',
                    ]
                ]
            ]);
    }
}
