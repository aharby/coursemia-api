<?php

namespace Tests\Feature\Courses\Admin;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use App\OurEdu\Courses\Enums\CourseEnums;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\LiveSessions\Models\SubModels\LiveSessionSession;
use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionDeleted;
use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionUpdated;

class LiveSessionsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_live_sessions()
    {
        dump('test_list_live_sessions');
        $this->authAdmin();

        create(LiveSession::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.liveSessions.get.index'))
            ->assertStatus(200);
    }

    public function test_create_live_sessions()
    {
        dump('test_create_live_sessions');

        $this->authAdmin();
        
        $liveSession = make(LiveSession::class)->toArray();

        $session = make(CourseSession::class)->toArray();

        $row = array_merge($liveSession, $session);

        $row['picture'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this
            ->post(route('admin.liveSessions.post.create'), $row);

        $this->assertDatabaseHas('courses', [
            'type'  =>  CourseEnums::LIVE_SESSION,
            'name' => $row['name'],
            'instructor_id' => $row['instructor_id'],
            'subject_id' => $row['subject_id'],
        ]);

        $this->assertDatabaseHas('course_sessions', [
            'date' => $row['date'],
            'content' => $row['content'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
        ]);
    }


    public function test_edit_live_sessions()
    {
        dump('test_edit_live_sessions');
        
        $this->disableExceptionHandling();

        $this->authAdmin();
        Event::fake();

        $record = create(LiveSession::class);

        $recordSession = $record->sessions()->save(make(CourseSession::class));

        $liveSession = make(LiveSession::class)->toArray();

        $session = make(CourseSession::class)->toArray();

        $row = array_merge($liveSession, $session);

        $response = $this
            ->put(route('admin.liveSessions.put.edit', $record->id), $row);

        $this->assertEquals($row['name'], $record->fresh()->name);
        $this->assertEquals($row['subject_id'], $record->fresh()->subject_id);
        $this->assertEquals($row['date'], $recordSession->fresh()->date);
        $this->assertEquals($row['content'], $recordSession->fresh()->content);

        // Perform session updated
        Event::assertDispatched(LiveSessionUpdated::class, function ($e) use ($record) {
            return $e->liveSession['id'] === $record->id;
        });
    }

    public function test_delete_liveSessions()
    {
        dump('test_delete_liveSessions');

        Event::fake();

        $this->authAdmin();

        $record = LiveSession::create(factory(LiveSession::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.liveSessions.delete', $record->id));

        $this->assertSoftDeleted('courses', [
            'id' => $record->id,
            'type' => $record->type,
            'subject_id' => $record->subject_id,
            'instructor_id' => $record->instructor_id,

        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);

        // Perform session updated
        Event::assertDispatched(LiveSessionDeleted::class, function ($e) use ($record) {
            return $e->liveSession['id'] === $record->id;
        });
    }
}
