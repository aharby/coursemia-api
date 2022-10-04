<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Models;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreparationMedia extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'subject_id',
        'library',
        'school_public',
        'source_filename',
        'filename',
        'size',
        'mime_type',
        'url',
        'extension',
        'name',
        'description',
    ];

    public function sessionPreparation()
    {
        return $this->belongsTo(SessionPreparation::class,"preparation_id");
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function student()
    {
        return $this->belongsToMany(User::class,'preparation_media_student','preparation_media_id','student_id')->withPivot(['downloaded_at','viewed_at']);
    }
}
