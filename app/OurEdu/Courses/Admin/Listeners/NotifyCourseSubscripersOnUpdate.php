<?php

namespace App\OurEdu\Courses\Admin\Listeners;

use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailManger;
use Illuminate\Support\Facades\App;
use App\OurEdu\Users\Models\Student;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Admin\Events\CourseSessionUpdated;

class NotifyCourseSubscripersOnUpdate
{
    /**
     * Handle the event.
     *
     * @param  CourseSessionUpdated  $event
     * @return void
     */
    public function handle(CourseSessionUpdated $event)
    {
        
        $subscripers = Student::whereHas('courses', function ($query) use ($event) {
            $query->withoutGlobalScopes()->where('id', $event->courseSession['course_id']);
        })->with('user')->get();
        $subscripedGroups = $subscripers->pluck('user')
        ->reject(function ($item, $key){return $item ==null;})
        ->reduce(function ($carry, $item) {
         array_push($carry[$item['language']],$item['email']);
         return $carry;
        },['ar'=>[],'en'=>[]]);
       
        $this->sendGroupMail("ar",$subscripedGroups['ar'], $event);
        $this->sendGroupMail("en",$subscripedGroups['en'], $event);
        
    }
   
    private function sendGroupMail(string $local, array $subscripers, $event)
    {
        if (count($subscripers) > 0) {
         
            (new MailManger)->prepareMail([
                'user_type' => UserEnums::STUDENT_TYPE,
                'data' => ['url' => CourseSessionEnums::getUpdatedSessionUrl($event->courseSession),
               'lang' => $local],
                'subject' => trans('emails.Course Session updated',[], $local),
                'emails' =>  $subscripers,
                'view' => 'courseSessionUpdated'
            ])->handle();
         }
    }
}
