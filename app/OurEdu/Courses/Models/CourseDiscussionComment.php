<?php

namespace App\OurEdu\Courses\Models;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDiscussionComment extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function discussions(): BelongsTo
    {
        return $this->belongsTo(CourseDiscussion::class , 'course_discussion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
