<?php
/**
 * Created by PhpStorm.
 * User: Magdy
 * Date: 8/17/2019
 * Time: 1:11 AM
 */

namespace App\OurEdu\Subjects\Models\SubModels;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectMedia extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $fillable =[
        'source_filename',
        'filename',
        'size',
        'mime_type',
        'url',
        'extension',
        'status',
        'subject_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class , 'subject_id');
    }
}
