<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;

class HotSpotQuestionVideo extends BaseModel
{
    protected $table = 'hot_spot_question_video';

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
