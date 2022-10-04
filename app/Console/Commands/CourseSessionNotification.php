<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\FcmNotification\FcmNotification;
use App\OurEdu\BaseNotification\MailNotification\MailNotificationInterface;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Subscribes\SubscribeCourse;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CourseSessionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course-session:notification:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'course-session:notification:send';
    private $mailNotification;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailNotificationInterface $mailNotification)
    {
        parent::__construct();
        $this->mailNotification = $mailNotification;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentTime = now()->format('H:i');
        $currentTimePlusOneMin = (new DateTime())->modify('+1 min')->format('H:i');

        // get all course session which starts at the current time in minutes
        $courseSessions = CourseSession::whereDate('date', '=', date('Y-m-d') )->where('start_time', '>=', $currentTime )->where('start_time', '<=', $currentTimePlusOneMin )->get();

        foreach ($courseSessions as $courseSession) {
            // get all student enrolled this course session
            $subscribersStudentsIds = SubscribeCourse::where('course_id',$courseSession->id)->pluck('student_id');
            $userIds = Student::whereIn('id',$subscribersStudentsIds)->pluck('user_id');
            $users = User::whereIn('id',$userIds);

            // and send notification of vcr link
            $url = buildScopeRoute('api.student.online-sessions.join-session', ['sessionId' => $courseSession->id]);
            $notificationData[NotificationEnums::MAIL] = array(
                'user_type' => UserEnums::STUDENT_TYPE,
                'data' => ['url' => $url],
                // TODO::joinCourseSession to be changed to users lang
                'subject' => trans('emails.your vcr'),
                'view' => 'joinCourseSession'
            );
//            $notificationData[NotificationEnums::FCM] = array(
//                'data' => [
//                    'title' => 'notification.vcr',
//                    'body' => 'notification.vcr',
//                    'screen_type' => NotificationEnum::INSTRUCTOR_VCR_SESSION,
//                    'params' => ['session_id' => $courseSession->id],
//                    'url' => $url,
//                ]
//            );

            $this->mailNotification->send($notificationData[NotificationEnums::MAIL], $users->pluck('email')->toArray());
//            Notification::send($users->get(), new FcmNotification($notificationData[NotificationEnums::FCM]['data']));

        }

        return 0;

    }
}
