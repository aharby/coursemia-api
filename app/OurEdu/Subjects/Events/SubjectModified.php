<?php

namespace App\OurEdu\Subjects\Events;

use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Illuminate\Foundation\Events\Dispatchable;


class SubjectModified extends ShouldBeStored implements  SubjectStoredEventInterface
{
    use SerializesModels, Dispatchable;

    /**
     * @var array
     */
    public $subjectAttributes;
    public $subject;
    public $by;
    public $action;
    public $task;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $subjectAttributes, array $subject, $action = 'Subject created', $task = null)
    {
        $this->by = Auth::id();
        $this->subjectAttributes = $subjectAttributes;
        $this->action = $action;
        $this->subject = $subject;
        $this->task = $task;
    }
}
