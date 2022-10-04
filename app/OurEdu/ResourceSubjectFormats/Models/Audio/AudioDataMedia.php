<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Audio;

use App\OurEdu\BaseApp\BaseModel;

class AudioDataMedia extends BaseModel
{
    protected $table = 'res_audio_data_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_audio_data_id'
    ];
}
