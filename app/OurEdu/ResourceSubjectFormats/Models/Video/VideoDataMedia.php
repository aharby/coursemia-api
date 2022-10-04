<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Video;

use App\OurEdu\BaseApp\BaseModel;

class VideoDataMedia extends BaseModel
{
    protected $table = 'res_video_data_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_video_data_id'
    ];
}
