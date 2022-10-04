<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Picture;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PictureData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_picture_data';

    protected $fillable = [
        'title',
        'description',
        'resource_subject_format_subject_id',
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function media()
    {
        return $this->hasMany(PictureDataMedia::class,'res_picture_data_id');
    }

}
