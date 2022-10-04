<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Picture;

use App\OurEdu\BaseApp\BaseModel;

class PictureDataMedia extends BaseModel
{
    protected $table = 'res_picture_data_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_picture_data_id',
        'description',
    ];
}
