<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;

class HotSpotMedia extends BaseModel
{
    protected $table = 'hot_spot_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_hot_spot_data_id',
        'image_width',
        'image_height',
    ];

    public function data(){
        return $this->belongsTo(HotSpotData::class);
    }


}
