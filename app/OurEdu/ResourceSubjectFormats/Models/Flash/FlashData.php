<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Flash;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_flash_data';

    protected $fillable = [
        'title',
        'description',
        'resource_subject_format_subject_id'
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function media()
    {
        return $this->hasMany(FlashDataMedia::class, 'res_flash_data_id');
    }
}
