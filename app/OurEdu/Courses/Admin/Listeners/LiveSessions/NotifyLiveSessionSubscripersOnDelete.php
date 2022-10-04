<?php

namespace App\OurEdu\Courses\Admin\Listeners\LiveSessions;

use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailManger;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\BaseApp\Enums\UrlActionEnums;
use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionDeleted;

class NotifyLiveSessionSubscripersOnDelete
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionDeleted  $event
     * @return void
     */
    public function handle(LiveSessionDeleted $event)
    {
        $subscripers = Student::whereHas('courses', function ($query) use ($event) {
            $query->withoutGlobalScopes()->where('id', $event->liveSession['id']);
        })->with('user')->get();

        $subscripedGroups = $subscripers->pluck('user')
        ->reject(function ($item, $key){return $item ==null;})
        ->reduce(function ($carry, $item) {
         array_push($carry[$item['language']],$item['email']);
         return $carry;
        },['ar'=>[],'en'=>[]]);
       
        $this->sendGroupMail("ar",$subscripedGroups['ar'], $event);
        $this->sendGroupMail("en",$subscripedGroups['en'], $event);
  
/*
        $subscripedUsers = $subscripers->pluck('user')->flatten(1)->pluck('email');

        if (! $subscripedUsers->count()) {
            return;
        }

        (new MailManger)->prepareMail([
            'user_type' => UserEnums::STUDENT_TYPE,
            'data' => ['url' => UrlActionEnums::getUpdatedLiveSessionUrl($event->liveSession)],
            'subject' => trans('emails.Live Session deleted'),
            'emails' => $subscripedUsers->toArray(),
            'view' => 'liveSessionDeleted'
        ])->handle();
**/
    }
    private function sendGroupMail(string $local, array $subscripers, $event)
    {
        if (count($subscripers) > 0) {
         
            (new MailManger)->prepareMail([
                'user_type' => UserEnums::STUDENT_TYPE,
                'data' => ['url' => UrlActionEnums::getUpdatedLiveSessionUrl($event->liveSession),
               'lang' => $local],
                'subject' => trans('emails.Live Session deleted',[], $local),
                'emails' =>  $subscripers,
                'view' => 'liveSessionDeleted'
            ])->handle();
         }
    }
}
