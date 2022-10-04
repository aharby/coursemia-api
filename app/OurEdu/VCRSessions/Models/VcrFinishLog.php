<?php

namespace App\OurEdu\VCRSessions\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class VcrFinishLog extends BaseModel
{
    use SoftDeletes;

    protected $table = 'vcr_finish_log';
    protected $fillable = [
        'vcr_session_id',
        'closed_by',
        'closed_from'
    ];
}
