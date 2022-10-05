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
        \App\Modules\Courses\Admin\Events\CourseSessionUpdated::class => [
            \App\Modules\Courses\Admin\Listeners\NotifyCourseSubscripersOnUpdate::class,
        ],
        \App\Modules\Courses\Admin\Events\CourseSessionCanceled::class => [
            \App\Modules\Courses\Admin\Listeners\NotifyCourseSubscripersOnCancel::class,
        ],
        \App\Modules\Courses\Admin\Events\LiveSessions\LiveSessionUpdated::class => [
            \App\Modules\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnUpdate::class,
        ],
        \App\Modules\Courses\Admin\Events\LiveSessions\LiveSessionDeleted::class => [
            \App\Modules\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnDelete::class,
        ],
        \App\Modules\Subjects\Events\SubjectPausedEvent::class => [
            \App\Modules\Subjects\Listeners\OnSubjectPause::class,
        ],
        \App\Modules\Subjects\Events\SubjectResumedEvent::class => [
            \App\Modules\Subjects\Listeners\OnSubjectResume::class,
        ],
        \App\Modules\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatPausedEvent::class => [
            \App\Modules\ResourceSubjectFormats\Listeners\OnResourceFormatSubjectFormatPause::class,
        ],
        \App\Modules\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatResumedEvent::class => [
            \App\Modules\ResourceSubjectFormats\Listeners\OnResourceFormatSubjectFormatResume::class,
        ],
        \App\Modules\Subjects\Events\SubjectFormatSubjectPausedEvent::class => [
            \App\Modules\Subjects\Listeners\OnSubjectFormatSubjectPause::class,
        ],
        \App\Modules\Subjects\Events\SubjectFormatSubjectResumedEvent::class => [
            \App\Modules\Subjects\Listeners\OnSubjectFormatSubjectResume::class,
        ],
        \App\Modules\Courses\Admin\Events\LiveSessions\LiveSessionCanceled::class => [
            \App\Modules\Courses\Admin\Listeners\LiveSessions\NotifyLiveSessionSubscripersOnCancel::class
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
