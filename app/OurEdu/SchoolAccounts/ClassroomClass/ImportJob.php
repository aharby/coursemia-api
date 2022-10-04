<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\SchoolAccounts\Classroom;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportJob extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        "classroom_id",
        "filename",
        'status',
        'has_errors'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function errors()
    {
        return $this->hasMany(ImportJobError::class);
    }
}
