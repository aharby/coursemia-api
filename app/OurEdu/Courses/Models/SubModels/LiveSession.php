<?php

namespace App\OurEdu\Courses\Models\SubModels;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Scopes\ActiveScope;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * This model uses the same courses table
 */
class LiveSession extends Course
{
    use HasFactory;

    protected $table = 'courses';

    protected $auditExclude = [
        'picture',
    ];
    protected static $attachFields = [
        'picture' => [
            'sizes' => ['small/live-lessons' => 'resize,256x144', 'large/live-lessons' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
    ];

    public static function boot()
    {
        parent::boot();

        // update type while creating or updating
        static::saving(function ($model) {
            $model->type = CourseEnums::LIVE_SESSION;
        });
        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', CourseEnums::LIVE_SESSION);
        });
    }

    public function session()
    {
        return $this->hasOne(CourseSession::class, 'course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


}
