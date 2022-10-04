<?php

namespace App\OurEdu\Courses\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Users\User;

class CourseDiscussion extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['course_id', 'user_id', 'body'];

    public function comments(): HasMany
    {
        return $this->hasMany(CourseDiscussionComment::class, 'course_discussion_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($courseDiscussion) {
            $courseDiscussion->comments()->delete();
        });
    }
}
