<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass;


use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportJobError extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        "import_job_id",
        "row",
        "error"
        ];
}
