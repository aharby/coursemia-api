<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\TrueFalse;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrueFalseOption extends BaseModel
{
    use HasFactory;

    protected $table = 'res_true_false_options';

    protected $fillable = [
        'option',
        'is_correct_answer',
        'res_true_false_question_id',
    ];
}
