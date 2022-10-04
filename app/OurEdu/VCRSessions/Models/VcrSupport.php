<?php

namespace App\OurEdu\VCRSessions\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;

class VcrSupport extends BaseModel
{
    protected $table = 'vcr_supports';

    protected $fillable = [
        'message',
        'user_id',
        'school_account_branch_id',
        'session_info',
        'agora_log_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(SchoolAccountBranch::class,'school_account_branch_id');
    }
}
