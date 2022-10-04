<?php

namespace App\OurEdu\Users\Repository;

use App\OurEdu\Users\User;
use App\OurEdu\Users\Models\UserLog;

class UserLogsRepository implements UserLogsRepositoryInterface
{
    public function getUserLogs($userId)
    {
        $logs = UserLog::where("event_properties->user->id", $userId)
            ->latest()
            ->paginate();

        $users = User::find($logs->pluck('event_properties.by'));


        $logs->each(function ($log) use ($users) {
            $log->data =['before' =>$log->event_properties['user'] , 'after' =>$log->event_properties['userAttributes'] ] ;
            $log->by = $users->firstWhere('id', $log->event_properties['by']);
            $log->user = new User($log->event_properties['user']);
            $log->action = $log->event_properties["action"];
        });

        return $logs;
    }

    public function findOrFail($logId)
    {
        $log = UserLog::findOrFail($logId);
        $log->user = new User($log->event_properties['user']);

        return $log;
    }
}
