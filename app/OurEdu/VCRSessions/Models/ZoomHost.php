<?php

namespace App\OurEdu\VCRSessions\Models;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomHost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'zoom_user_id',
        'status',
        'usages_number',
        'current_vcr_session_id'
    ];

    public function lastVcrSession()
    {
        return $this->belongsTo(VCRSession::class,'current_vcr_session_id');
    }
}
