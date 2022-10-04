<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Complete;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompleteAnswer extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_complete_answers';

    protected $fillable = [
        'answer',
        'res_complete_question_id',
    ];
}
