<?php

namespace App\OurEdu\Subjects\Events;

use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Illuminate\Foundation\Events\Dispatchable;

class SubjectFormatSubjectModified extends ShouldBeStored implements  SubjectStoredEventInterface
{
    use SerializesModels, Dispatchable;

    /**
     * @var array
     */
    public $subjectFormatSubjectAttributes;
    public $subjectFormatSubject;
    public $by;
    public $action;
    public $task;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $subjectFormatSubjectAttributes, array $subjectFormatSubject, $action = 'Section created', $task = null)
    {
        $this->by = Auth::id();
        $this->subjectFormatSubjectAttributes = $subjectFormatSubjectAttributes;
        $this->action = $action;
        $this->subjectFormatSubject = $subjectFormatSubject;
        $this->task = $task;
    }
}
