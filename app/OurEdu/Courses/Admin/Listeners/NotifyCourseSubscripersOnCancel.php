<?php

namespace App\OurEdu\Courses\Admin\Listeners;

use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailManger;
use Illuminate\Support\Facades\App;
use App\OurEdu\Users\Models\Student;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\Courses\Admin\Events\CourseSessionCanceled;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;

class NotifyCourseSubscripersOnCancel
{
    private $notifierFactory;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }
    /**
     * Handle the event.
     *
     * @param  CourseSessionCanceled  $event
     * @return void
     */
    public function handle(CourseSessionCanceled $event)
    {
        $subscripers = Student::whereHas('courses', function ($query) use ($event) {
            $query->withoutGlobalScopes()->where('id', $event->courseSession->course->id);
        })->with('user')->get();

        $subscripedGroups = $subscripers->pluck('user')
        ->reject(function ($item, $key){return $item ==null;})
        ->reduce(function ($carry, $item) {
         array_push($carry[$item['language']],$item['email']);
         return $carry;
        },['ar'=>[],'en'=>[]]);
       
        $this->sendGroupMail("ar",$subscripedGroups['ar'], $event);
        $this->sendGroupMail("en",$subscripedGroups['en'], $event);
        
       
        foreach ($subscripers as $studentUser ){

            $notificationData = [
                'users' => $studentUser->user,
                NotificationEnums::DB => [
                    'data' => [
                        'title'        =>  buildTranslationKey('notification.session Canceled'),
                        'body'         => buildTranslationKey('notification.course_session_cancellation_notification_body' ,
                            [
                                'course_name'=> $event->courseSession->course->name,
                                'instructor_name' =>$event->courseSession->course->instructor ? $event->courseSession->course->instructor->name  : '',
                                'session_time' =>  $event->courseSession->start_time]),
                    ]
                ],
                
                NotificationEnums::FCM => [
                    'data' => [
                        'title'        =>  buildTranslationKey('notification.session Canceled'),
                        'body'         => buildTranslationKey('notification.course_session_cancellation_notification_body' ,
                            [
                                'course_name'=> $event->courseSession->course->name,
                                'instructor_name' =>$event->courseSession->course->instructor ? $event->courseSession->course->instructor->name  : '',
                                'session_time' =>  $event->courseSession->start_time]),
                    ]
                ]
            ];
            
            $this->notifierFactory->send($notificationData);
        }

    if ($event->courseSession->course->instructor) {
            $this->notifyInstructor($event);
       }

    }
    private function sendGroupMail(string $local, array $subscripers, $event)
    {
        if (count($subscripers) > 0) {
         
            (new MailManger)->prepareMail([
                'user_type' => UserEnums::STUDENT_TYPE,
                'data' => ['url' => CourseSessionEnums::getUpdatedSessionUrl($event->courseSession),
               'lang' => $local],
                'subject' => trans('emails.Course Session canceled',[], $local),
                'emails' =>  $subscripers,
                'view' => 'courseSessionCanceled'
            ])->handle();
         }
    }
    
    private function notifyInstructor($event){

        $notificationData = [
            'users' => $event->courseSession->course->instructor,
            NotificationEnums::DB => [
                'data' => [
                    'title'        =>  buildTranslationKey('notification.session Canceled'),
                    'body'         => buildTranslationKey('notification.course_session_cancellation_instructor_notification_body' ,
                        [  'course_name'=> $event->courseSession->course->name,
                            'session_time' =>  $event->courseSession->start_time]),
                ]
            ],
            
            NotificationEnums::FCM => [
                'data' => [
                    'title'        =>  buildTranslationKey('notification.session Canceled'),
                    'body'         => buildTranslationKey('notification.course_session_cancellation_instructor_notification_body' ,
                        [  'course_name'=> $event->courseSession->course->name,
                            'session_time' =>  $event->courseSession->start_time]),
                ]
            ]
        ];
        
        $this->notifierFactory->send($notificationData);
        
    }
}
