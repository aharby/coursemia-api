<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;

class HotSpotQuestionAudio extends BaseModel
{
    protected $table = 'hot_spot_question_audio';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_hot_spot_question_id'
    ];
}
