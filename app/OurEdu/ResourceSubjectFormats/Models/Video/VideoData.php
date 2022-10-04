<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Video;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_video_data';

    protected $fillable = [
        'title',
        'description',
        'resource_subject_format_subject_id',
        'video_type',
        'link',
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function media()
    {
        return $this->hasMany(VideoDataMedia::class,'res_video_data_id');
    }
}
