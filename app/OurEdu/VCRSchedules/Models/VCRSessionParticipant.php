<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;

class VCRSessionParticipant extends BaseModel
{
    protected $table = 'vcr_sessions_participants';

    protected $fillable = [
        'participant_uuid',
        'vcr_session_id',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
