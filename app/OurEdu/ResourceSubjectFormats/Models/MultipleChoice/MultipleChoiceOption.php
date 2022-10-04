<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultipleChoiceOption extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_multiple_choice_options';

    protected $fillable = [
        'answer',
        'res_multiple_choice_data_id',
        'multiple_choice_type',
        'is_correct_answer'
    ];
    public function examQuestionAnswer(){
        return $this->hasMany(ExamQuestionAnswer::class,'option_table_id');
    }
}
