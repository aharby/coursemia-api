<?php

namespace App\Modules\payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modules\Users\Models\Student;
use App\Modules\Courses\Models\Course;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'total_price', 'stripe_invoice_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'order_course')->withTimestamps();
    }
}
