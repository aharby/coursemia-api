<?php

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\Users\User;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Models\SubModels\SubjectLog;

class SubjectLogsRepository implements SubjectLogsRepositoryInterface
{
    public function getSubjectLogs($subjectId)
    {
        $logs = SubjectLog::where("event_properties->subject->id", $subjectId)
            ->latest()
            ->paginate();

        $users = User::find($logs->pluck('event_properties.by'));
        $logs->each(function ($log) use ($users) {
            $log->data = ['before' =>$log->event_properties['subject'], 'after' =>$log->event_properties['subjectAttributes'] ];
            $log->by = $users->firstWhere('id', $log->event_properties['by']);
            $log->subject = new Subject($log->event_properties['subject']);
            $log->action = $log->event_properties["action"];
            $log->task = $log->event_properties["task"] ? new Task($log->event_properties["task"]) : null;
        });

        return $logs;
    }

    public function findOrFail($logId)
    {
        $log = SubjectLog::findOrFail($logId);
        $log->subject = new Subject($log->event_properties['subject']);
        $log->contentAuthors = isset($log->event_properties['subjectAttributes']['content_authors'])? User::find($log->event_properties['subjectAttributes']['content_authors']) : collect([]);
        $log->instructors = isset($log->event_properties['subjectAttributes']['instructors']) ? User::find($log->event_properties['subjectAttributes']['instructors']) : collect([]);

        return $log;
    }
}
