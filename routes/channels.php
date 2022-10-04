<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Broadcasting\ClassroomClassCreationChannel;
use App\Broadcasting\CompetitionChannel;
use App\Broadcasting\InstructorJoinRequestedSessionChannel;
use App\Broadcasting\SessionChannel;
use App\Broadcasting\StudentTimetableChannel;
use App\Broadcasting\InstructorTimetableChannel;
use App\Broadcasting\SchoolBranchChannel;
use App\Broadcasting\UserNotificationChannel;

Broadcast::channel('competition.{exam}', CompetitionChannel::class);

Broadcast::channel('session.{session}', SessionChannel::class);

Broadcast::channel('createClassroomClass.{channelId}', ClassroomClassCreationChannel::class);

Broadcast::channel('instructor-timetable.{branch}', InstructorTimetableChannel::class);

Broadcast::channel('student-timetable.{classroom}', StudentTimetableChannel::class);

Broadcast::channel('Branch.{branch_id}', SchoolBranchChannel::class);

Broadcast::channel('instructor-join-session.{session}',InstructorJoinRequestedSessionChannel::class);
Broadcast::channel('Notifications.{user_id}', UserNotificationChannel::class);

