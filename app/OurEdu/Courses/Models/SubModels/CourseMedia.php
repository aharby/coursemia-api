<?php

namespace App\OurEdu\Courses\Models\SubModels;

use App\OurEdu\Courses\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMedia extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'course_id',
        'source_filename',
        'filename',
        'size',
        'mime_type',
        'url',
        'extension',
        'active'
    ];

    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class, "course_id");
    }
}
