<?php

namespace App\Modules\Users\Models;

use App\Modules\BaseApp\BaseModel;
use App\Modules\Courses\Models\Course;
use App\Modules\EducationalSystems\EducationalSystem;
use App\Modules\Events\Models\StudentStoredEvent;
use App\Modules\Exams\Models\Exam;
use App\Modules\Exams\Models\ExamQuestionAnswer;
use App\Modules\Exams\Models\PrepareExamQuestion;
use App\Modules\GradeClasses\GradeClass;
use App\Modules\Options\Option;
use App\Modules\Quizzes\Models\StudentQuiz;
use App\Modules\ResourceSubjectFormats\Models\Progress\ResourceProgressStudent;
use App\Modules\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use App\Modules\SchoolAccounts\Classroom;
use App\Modules\Schools\School;
use App\Modules\SubjectPackages\Package;
use App\Modules\SubjectPackages\SubscribedPackage;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Subscribes\Subscribe;
use App\Modules\Subscribes\SubscribeCourse;
use App\Modules\Users\User;
use App\Modules\VCRSchedules\Models\VCRRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Student extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'birth_date',
        'educational_system_id',
        'school_id',
        'class_id',
        'academical_year_id',
        'wallet_amount',
        'classroom_id',
        'password',
    ];

    public function subscribe()
    {
        return $this->hasMany(Subscribe::class, 'student_id');
    }

    public function specialClassroom()
    {
        return $this->belongsToMany(Classroom::class);
    }

    public function subscribeCourse()
    {
        return $this->hasMany(SubscribeCourse::class, 'student_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_subscribe_students', 'student_id', 'subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function educationalSystem()
    {
        return $this->belongsTo(EducationalSystem::class);
    }

    public function academicalYear()
    {
        return $this->belongsTo(Option::class, 'academical_year_id');
    }

    public function gradeClass()
    {
        return $this->belongsTo(GradeClass::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot('date_of_pruchase', 'is_discussion_active');
    }

    public function subscribedPackages()
    {
        return $this->hasMany(SubscribedPackage::class, 'student_id');
    }

    /* this relation should be abandoned :-
    * (the invitations (while are the main filler to this relation)
    * based on sender_id which is the user id .
    * since then all relations between sub types of users (parents , students , studentTeacher)
    * should stay inside user model
    */
//    public function parents()
//    {
//        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
//    }


//    public function studentParents()
//    {
//        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
//    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packages_subscribed_students', 'student_id', 'package_id');
    }

    public function subjectFormatSubjectProgress()
    {
        return $this->hasMany(SubjectFormatProgressStudent::class, 'student_id');
    }

    public function events()
    {
        return $this->hasMany(
            StudentStoredEvent::class,
            'event_properties->user_attributes->student->id'
        );
    }

    public function answers()
    {
        return $this->hasMany(ExamQuestionAnswer::class, 'student_id');
    }

    public function generateTags(): array
    {
        return [
            $this->user_id
        ];
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.class_id')) {
            if (is_null($this->getOriginal('class_id'))) {
                $data['old_values']['grade_class_name'] = '';
            } else {
                $data['old_values']['grade_class_name'] = GradeClass::find($this->getOriginal('class_id'))->title;
            }
            if (!is_null($this->getAttribute('class_id'))) {
                $data['new_values']['grade_class_name'] = GradeClass::find($this->getAttribute('class_id'))->title;
            }
        }

        if (Arr::has($data, 'new_values.educational_system_id')) {
            if (is_null($this->getOriginal('educational_system_id'))) {
                $data['old_values']['educational_system_name'] = '';
            } else {
                $data['old_values']['educational_system_name'] = EducationalSystem::find(
                    $this->getOriginal('educational_system_id')
                )->name;
            }
            if (!is_null($this->getAttribute('educational_system_id'))) {
                $data['new_values']['educational_system_name'] = EducationalSystem::find(
                    $this->getAttribute('educational_system_id')
                )->name;
            }
        }

        if (Arr::has($data, 'new_values.academical_year_id')) {
            if (is_null($this->getOriginal('academical_year_id'))) {
                $data['old_values']['academical_year_name'] = '';
            } else {
                $data['old_values']['academical_year_name'] = Option::find(
                    $this->getOriginal('academical_year_id')
                )->title;
            }
            if (!is_null($this->getAttribute('academical_year_id'))) {
                $data['new_values']['academical_year_name'] = Option::find(
                    $this->getAttribute('academical_year_id')
                )->title;
            }
        }

        if (Arr::has($data, 'new_values.school_id')) {
            if (is_null($this->getOriginal('school_id'))) {
                $data['old_values']['school_name'] = '';
            } else {
                $data['old_values']['school_name'] = School::find($this->getOriginal('school_id'))->name;
            }
            if (!is_null($this->getAttribute('school_id'))) {
                $data['new_values']['school_name'] = School::find($this->getAttribute('school_id'))->name;
            }
        }
        return $data;
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function quizzes()
    {
        return $this->hasMany(StudentQuiz::class, 'student_id');
    }
    public function resourceSubjectProgress()
    {
        return $this->hasMany(ResourceProgressStudent::class, 'student_id');
    }

    public function takenExamQuestions()
    {
        return $this->belongsToMany(
            PrepareExamQuestion::class,
            'prepare_exam_question_student',
            'student_id',
            'prepare_exam_question_id'
        );
    }

    public function takenCompetitionQuestions()
    {
        return $this->belongsToMany(
            PrepareExamQuestion::class,
            'prepare_competition_question_student',
            'student_id',
            'prepare_exam_question_id'
        );
    }

    public function takenPracticeQuestions()
    {
        return $this->belongsToMany(
            PrepareExamQuestion::class,
            'prepare_practice_question_student',
            'student_id',
            'prepare_exam_question_id'
        );
    }
    public function competitionStudent()
    {
        return $this->belongsToMany(Exam::class, 'competition_student')->withPivot('result')->withTimestamps();
    }
    public function vcrRequests()
    {
        return $this->hasMany(VCRRequest::class, 'student_id');
    }
}
