<?php

namespace App\OurEdu\VCRSessions\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class VCRSessionMedia extends BaseModel
{
    use SoftDeletes;

    protected $table = 'vcr_session_media';
    protected $fillable = [
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'vcr_session_id',
    ];
}
