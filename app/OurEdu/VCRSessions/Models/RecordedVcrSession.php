<?php

namespace App\OurEdu\VCRSessions\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecordedVcrSession extends BaseModel
{
    use SoftDeletes;

    protected $table = 'recorded_vcr_sessions';
    protected $fillable = [
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'vcr_session_id',
        'chunk_id'
    ];
}
