<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Pdf;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdfData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_pdf_data';

    protected $fillable = [
        'title',
        'description',
        'resource_subject_format_subject_id',
        'pdf_type',
        'pdf',
        'link',
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function media()
    {
        return $this->hasMany(PdfDataMedia::class,'res_pdf_data_id');
    }

}
