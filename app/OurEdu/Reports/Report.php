<?php


namespace App\OurEdu\Reports;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends BaseModel
{
    use  SoftDeletes, HasFactory;
    
    protected $table = 'student_reports';
    protected $fillable = [
        'report',
        'reportable_id',
        'reportable_type',
        'student_id',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
