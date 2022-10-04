<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Pdf;

use App\OurEdu\BaseApp\BaseModel;

class PdfDataMedia extends BaseModel
{
    protected $table = 'res_pdf_data_media';

    protected $fillable =[
        'source_filename',
        'filename',
        'mime_type',
        'url',
        'extension',
        'status',
        'res_pdf_data_id'
    ];
}
