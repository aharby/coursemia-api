<?php

namespace App\OurEdu\Feedbacks;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends BaseModel
{
    use HasFactory;
    
    protected $table = 'students_feedback';

    protected $fillable = [
        'approved',
        'feedback',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
