<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Flash;

use App\OurEdu\BaseApp\BaseModel;

class FlashDataMedia extends BaseModel
{
    protected $table = 'res_flash_data_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_flash_data_id'
    ];
}
