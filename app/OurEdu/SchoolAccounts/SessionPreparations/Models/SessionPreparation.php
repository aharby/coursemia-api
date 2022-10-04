<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionPreparation extends BaseModel
{
    use SoftDeletes, CreatedBy;

    protected $fillable = [
        'subject_id',
        'classroom_id',
        'classroom_class_id',
        'classroom_session_id',
        'created_by',
        'internal_preparation',
//        'student_preparation',
        'pre_Learning',
        'objectives',
        'introductory',
        'application',
        'evaluation',
        'published_at',
        'section_id'
    ];


    public function media()
    {
        return $this->hasMany(PreparationMedia::class,'preparation_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function section()
    {
        return $this->belongsTo(SubjectFormatSubject::class, 'section_id');
    }

    public function session()
    {
        return $this->belongsTo(ClassroomClassSession::class, "classroom_session_id");
    }
}
