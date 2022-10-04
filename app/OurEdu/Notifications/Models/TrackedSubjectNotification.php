<?php


namespace App\OurEdu\Notifications\Models;

use App\OurEdu\BaseApp\BaseModel;

class TrackedSubjectNotification extends BaseModel
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'sender_user_type',
        'receiver_user_type',
        'notification_type',
        'created_at',
        'updated_at',
    ];
}
