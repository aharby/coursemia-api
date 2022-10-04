<?php

namespace App\OurEdu\VideoCall\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCallRequest extends Model
{
    const STATUS_CANCELLED= 'cancelled';
    protected $fillable=['supervisor_leave_time','channel','parent_leave_time','status','from_user_id','to_user_id'];
}
