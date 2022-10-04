<?php

namespace App\OurEdu\Subjects\Models;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Models\AppleIAPProduct;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Scopes\NotEndedScope;
use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Countries\Country;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Scopes\ActiveScope;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Subscribes\Subscription;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Subject extends BaseModel implements Auditable
{
    use SoftDeletes;
    use CreatedBy;
    use HasAttach;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected static $attachFields = [
        'image' => [
            'sizes' => [
                'small/subject/subject-images' => 'resize,256x144',
                'large/subject/subject-images' => 'resize,1024x576'
            ],
            'path' => 'uploads'
        ],
    ];

    protected $auditInclude = [
        'name',
        'start_date',
        'end_date',
        'subscription_cost',
        'educational_system_id',
        'country_id',
        'grade_class_id',
        'educational_term_id',
        'academical_years_id',
        'sme_id',
        'created_by',
        'is_active',
        'section_type',
        'subject_library_text',
        'subject_library_attachment',
        'total_points',
        'color',
        'image',
        'out_of_date',

    ];
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'subscription_cost',
        'educational_system_id',
        'country_id',
        'grade_class_id',
        'educational_term_id',
        'academical_years_id',
        'sme_id',
        'created_by',
        'is_active',
        'section_type',
        'subject_library_text',
        'subject_library_attachment',
        'total_points',
        'color',
        'image',
        'out_of_date',
        'direction',
        'is_top_qudrat',
        'our_edu_reference'
    ];

    /**
     * Get the subject's apple price.
     *
     * @return string
     */
    public function getApplePriceAttribute()
    {
        $iap = AppleIAPProduct::query()
            ->where('product_id', '>', PaymentEnums::SUBJECT_OFFSET)
            ->where('price', '>', $this->subscription_cost)
            ->first();
        return $iap?->price;
    }

    public function subjectFormatSubject()
    {
        return $this->hasMany(SubjectFormatSubject::class, 'subject_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function educationalSystem()
    {
        return $this->belongsTo(EducationalSystem::class);
    }

    public function educationalTerm()
    {
        return $this->belongsTo(Option::class);
    }

    public function academicalYears()
    {
        return $this->belongsTo(Option::class);
    }

    public function sme()
    {
        return $this->belongsTo(User::class);
    }

    public function gradeClass()
    {
        return $this->belongsTo(GradeClass::class);
    }

    public function contentAuthors()
    {
        return $this->belongsToMany(
            User::class,
            'subject_content_author'
        )->withTimestamps();
    }

    public function instructors()
    {
        return $this->belongsToMany(
            User::class,
            'subject_instructor'
        )->withTimestamps();
    }

    public function educationalSupervisors()
    {
        return $this->belongsToMany(
            User::class,
            'edu_supervisors_subjects',
            'subject_id',
            'edu_supervisor_id'
        )
            ->withPivot('edu_system_id', 'grade_class_id')
            ->withTimestamps();
    }

    public function schoolInstructors()
    {
        return $this->belongsToMany(
            User::class,
            'subject_school_instructor',
            'subject_id',
            'instructor_id'
        );
    }

    protected static function boot()
    {
        parent::boot();
        // Subject::observe(SubjectObserver::class);
        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope(new NotEndedScope());
    }

    public function task()
    {
        return $this->hasMany(
            Task::class,
            'subject_id'
        );
    }

    public function questionReportTasks()
    {
        return $this->hasMany(
            QuestionReportTask::class,
            'subject_id'
        );
    }

    public function media()
    {
        return $this->hasMany(SubjectMedia::class, 'subject_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'subject_subscribe_students',
            'subject_id',
            'student_id'
        )->withPivot('date_of_purchase');
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'subject_id');
    }

    public function preparedQuestions()
    {
        return $this->hasMany(PrepareExamQuestion::class, 'subject_id');
    }

    public function preparedGeneralQuestions()
    {
        return $this->hasMany(PreparedGeneralExamQuestion::class, 'subject_id');
    }

    public function studentProgress()
    {
        return $this->hasMany(SubjectFormatProgressStudent::class, 'subject_id');
    }

    public function scopeSmeSubject($query)
    {
        if (auth()->user()->type == UserEnums::SME_TYPE) {
            return $query->where('sme_id', auth()->id());
        } else {
            return $query;
        }
    }

    public function questionReport()
    {
        return $this->hasMany(QuestionReport::class);
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscripable');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function reports()
    {
        return $this->morphMany('App\OurEdu\Reports\Report', 'reportable');
    }

    public function reportTasks()
    {
        return $this->hasMany(QuestionReportTask::class);
    }

    public function generalExams()
    {
        return $this->hasMany(GeneralExam::class);
    }

    public function reportedSubjectFormatSubject()
    {
        return $this->belongsToMany(
            SubjectFormatSubject::class,
            'report_subject_format_subject',
            'subject_id',
            'section_id'
        )->whereNull('section_parent_id');
    }

    public function questionReportSubjectFormatSubject()
    {
        return $this->belongsToMany(
            SubjectFormatSubject::class,
            'question_report_subject_format_subject',
            'subject_id',
            'section_id'
        )->whereNull('section_parent_id');
    }

    public function generalExamQuestionReportSubjectFormatSubject()
    {
        return $this->belongsToMany(
            SubjectFormatSubject::class,
            'general_exam_question_report_subject_format_subject',
            'subject_id',
            'section_id'
        )->whereNull('section_parent_id');
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.country_id')) {
            if (is_null($this->getOriginal('country_id'))) {
                $data['old_values']['country_name'] = '';
            } else {
                $data['old_values']['country_name'] = Country::find($this->getOriginal('country_id'))->name;
            }
            if (!is_null($this->getAttribute('country_id'))) {
                $data['new_values']['country_name'] = Country::find($this->getAttribute('country_id'))->name;
            }
        }

        if (Arr::has($data, 'new_values.grade_class_id')) {
            if (is_null($this->getOriginal('grade_class_id'))) {
                $data['old_values']['grade_class_name'] = '';
            } else {
                $data['old_values']['grade_class_name'] = GradeClass::find($this->getOriginal('grade_class_id'))->title;
            }
            if (!is_null($this->getAttribute('grade_class_id'))) {
                $data['new_values']['grade_class_name'] = GradeClass::find(
                    $this->getAttribute('grade_class_id')
                )->title;
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

        if (Arr::has($data, 'new_values.academical_years_id')) {
            if (is_null($this->getOriginal('academical_years_id'))) {
                $data['old_values']['academical_years_name'] = '';
            } else {
                $data['old_values']['academical_years_name'] = Option::find(
                    $this->getOriginal('academical_years_id')
                )->title;
            }
            if (!is_null($this->getAttribute('academical_years_id'))) {
                $data['new_values']['academical_years_name'] = Option::find(
                    $this->getAttribute('academical_years_id')
                )->title;
            }
        }

        if (Arr::has($data, 'new_values.educational_term_id')) {
            if (is_null($this->getOriginal('educational_term_id'))) {
                $data['old_values']['educational_term_name'] = '';
            } else {
                $data['old_values']['educational_term_name'] = Option::find(
                    $this->getOriginal('educational_term_id')
                )->title;
            }
            if (!is_null($this->getAttribute('educational_term_id'))) {
                $data['new_values']['educational_term_name'] = Option::find(
                    $this->getAttribute('educational_term_id')
                )->title;
            }
        }

        if (Arr::has($data, 'new_values.sme_id')) {
            if (is_null($this->getOriginal('sme_id'))) {
                $data['old_values']['sme_name'] = '';
            } else {
                $data['old_values']['sme_name'] = User::find($this->getOriginal('sme_id'))->name;
            }
            if (!is_null($this->getAttribute('sme_id'))) {
                $data['new_values']['sme_name'] = User::find($this->getAttribute('sme_id'))->name;
            }
        }
        return $data;
    }

    public function classroomClasses()
    {
        return $this->hasMany(ClassroomClass::class);
    }

    public function branchQuestionsPermissions()
    {
        return $this->belongsToMany(
            SchoolAccountBranch::class,
            "schools_subjects_questions_bank_permissions",
            "subject_id",
            "branch_id"
        )
            ->withPivot(['branch_scope', 'grade_scope', "school_scope", 'school_id', 'branch_id'])
            ->withTimestamps();
    }

    public function VCRSessions()
    {
        return $this->hasMany(VCRSession::class);
    }

    public function VCRSchedules()
    {
        return $this->hasMany(VCRSchedule::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function liveSessions()
    {
        return $this->hasMany(LiveSession::class);
    }
}
