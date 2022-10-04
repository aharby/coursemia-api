<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\OurEdu\Courses\Admin\Events\CourseSessionUpdated::class => [
            \App\OurEdu\Courses\Admin\Listeners\NotifyCourseSubscripersOnUpdate::class,
        ],
        \App\OurEdu\Courses\Admin\Events\CourseSessionCanceled::class => [
            \App\OurEdu\Courses\Admin\Listeners\NotifyCourseSubscripersOnCancel::class,
        ],
        \App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionUpdated::class => [
            \App\OurEdu\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnUpdate::class,
        ],
        \App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionDeleted::class => [
            \App\OurEdu\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnDelete::class,
        ],
        \App\OurEdu\Subjects\Events\SubjectPausedEvent::class => [
            \App\OurEdu\Subjects\Listeners\OnSubjectPause::class,
        ],
        \App\OurEdu\Subjects\Events\SubjectResumedEvent::class => [
            \App\OurEdu\Subjects\Listeners\OnSubjectResume::class,
        ],
        \App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatPausedEvent::class => [
            \App\OurEdu\ResourceSubjectFormats\Listeners\OnResourceFormatSubjectFormatPause::class,
        ],
        \App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatResumedEvent::class => [
            \App\OurEdu\ResourceSubjectFormats\Listeners\OnResourceFormatSubjectFormatResume::class,
        ],
        \App\OurEdu\Subjects\Events\SubjectFormatSubjectPausedEvent::class => [
            \App\OurEdu\Subjects\Listeners\OnSubjectFormatSubjectPause::class,
        ],
        \App\OurEdu\Subjects\Events\SubjectFormatSubjectResumedEvent::class => [
            \App\OurEdu\Subjects\Listeners\OnSubjectFormatSubjectResume::class,
        ],
        \App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionCanceled::class => [
            \App\OurEdu\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnCancel::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
