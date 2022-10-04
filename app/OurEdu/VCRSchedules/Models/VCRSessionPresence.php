<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;

class VCRSessionPresence extends BaseModel
{
    protected $table = 'vcr_sessions_presence';

    protected $fillable = [
        'vcr_session_id',
        'entered_at',
        'left_at',
        'session_time_to_start',
        'session_time_to_end',
        'user_id',
        'user_role',
        'user_uuid',
        'vcr_session_type',
    ];

    public function vcrSession()
    {
        return $this->belongsTo(VCRSession::class, 'vcr_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
