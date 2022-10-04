<?php

namespace App\OurEdu\Courses\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\BaseApp\Traits\Ratingable;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\SubModels\CourseMedia;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Models\AppleIAPProduct;
use App\OurEdu\Scopes\ActiveScope;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subscribes\Subscription;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Course extends BaseModel implements Auditable
{
    use SoftDeletes;
    use CreatedBy;
    use Ratingable;
    use HasAttach;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;


    protected static $attachFields = [
        'picture' => [
            'sizes' => ['small/courses' => 'resize,256x144', 'large/courses' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
        'medium_picture' => [
            'sizes' => ['small/courses' => 'resize,256x144', 'large/courses' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
        'small_picture' => [
            'sizes' => ['small/courses' => 'resize,256x144', 'large/courses' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
    ];
    protected $auditExclude = [
        'picture',
        'medium_picture',
        'small_picture'
    ];
    protected $fillable = [
        'name',
        'type',
        'description',
        'start_date',
        'end_date',
        'subscription_cost',
        'subject_id',
        'instructor_id',
        'picture',
        'is_active',
        'is_top_qudrat',
        'out_of_date',
        'medium_picture',
        'small_picture'
    ];

    /**
     * Get the course's apple price.
     *
     * @return string
     */
    public function getApplePriceAttribute()
    {
        $iap = AppleIAPProduct::query()
            ->where('product_id', '>', PaymentEnums::COURSE_OFFSET)
            ->where('price', '>', $this->subscription_cost)
            ->first();
        return $iap?->price;
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->whereIn('type', [CourseEnums::PUBLIC_COURSE, CourseEnums::SUBJECT_COURSE]);
        });
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function vcrSessions()
    {
        return $this->hasMany(VCRSession::class, 'course_id');
    }

    public function VCRSessionPresence()
    {
        return $this->hasManyThrough(VCRSessionPresence::class, VCRSession::class, 'course_id', 'vcr_session_id');
    }

    public function sessions()
    {
        return $this->hasMany(CourseSession::class, 'course_id');
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscripable');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id')
            ->withPivot('date_of_pruchase', 'is_discussion_active');
    }

    public function transformAudit(array $data): array
    {
        //in case create public_course this type of course not has subject :D
        if (Arr::has($data, 'new_values.subject_id') && !is_null(Arr::get($data, 'new_values.subject_id'))) {
            if (is_null($this->getOriginal('subject_id'))) {
                $data['old_values']['subject_name'] = '';
            } else {
                $data['old_values']['subject_name'] = Subject::find($this->getOriginal('subject_id'))->name;
            }
            $data['new_values']['subject_name'] = Subject::find($this->getAttribute('subject_id'))->name;
        }

        if (Arr::has($data, 'new_values.instructor_id')) {
            if (is_null($this->getOriginal('instructor_id'))) {
                $data['old_values']['instructor_name'] = '';
            } else {
                $data['old_values']['instructor_name'] = User::find($this->getOriginal('instructor_id'))->name;
            }
            $data['new_values']['instructor_name'] = User::find($this->getAttribute('instructor_id'))->name;
        }
        return $data;
    }

    public function VCRSession()
    {
        return $this->hasManyThrough(VCRSession::class, CourseSession::class);
    }

    public function discussions()
    {
        return $this->hasMany(CourseDiscussion::class);
    }
    public function media()
    {
        return $this->hasMany(CourseMedia::class, 'course_id');
    }

    public function homeworks()
    {
        return $this->hasMany(GeneralQuiz::class, 'course_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }
}
