<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Users\Models\ContentAuthor;
use Illuminate\Database\Eloquent\SoftDeletes;
use Grimzy\LaravelMysqlSpatial\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends BaseModel implements Auditable
{
    use SoftDeletes, CreatedBy, HasFactory;
    use \OwenIt\Auditing\Auditable;


    protected $fillable = [
        'is_active',
        'is_expired',
        'is_assigned',
        'is_done',
        'due_date',
        'subject_id',
        'resource_subject_format_subject_id',
        'subject_format_subject_id',
        'title',
        'pulled_at',
        'is_paused',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $task) {
            $resources = $task->resourceSubjectFormatSubject()->get();
            foreach ($resources as $resource) {
                $resource->delete();
            }
        });
    }

    protected $dates = ['pulled_at'];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function contentAuthors()
    {
        return $this->belongsToMany(ContentAuthor::class, 'content_author_task')->withTimestamps();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class);
    }

    public function scopeNotPaused(Builder $query)
    {
        return $query->where('is_paused', false);
    }
}
