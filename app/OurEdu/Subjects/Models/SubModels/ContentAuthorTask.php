<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Users\Models\ContentAuthor;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

class ContentAuthorTask extends BaseModel
{
    use SoftDeletes;

    protected $table = 'content_author_task';
    protected $fillable = [
        'task_id',
        'content_author_id',
        'created_at',
        'updated_at',

    ];

    public function task() {
        return $this->belongsTo(Task::class , 'task_id');
    }

}
