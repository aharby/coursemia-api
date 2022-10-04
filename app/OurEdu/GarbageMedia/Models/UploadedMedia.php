<?php

namespace App\OurEdu\GarbageMedia\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedMedia extends BaseModel
{
    use SoftDeletes ;

    protected $fillable =[
        'source_filename',
        'filename',
        'size',
        'mime_type',
        'url',
        'extension',
        'status'
    ];

}
