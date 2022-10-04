<?php

namespace App\OurEdu\Users;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAssessorViewer;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\Certificates\Models\ThankingCertificate;
use App\OurEdu\Countries\Country;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\Order;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Models\Transaction;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Models\ClassroomClassSessionScores;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAdmin\Models\SchoolAdmin;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Models\FirebaseToken;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\Models\ParentData;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Models\StudentTeacher;
use App\OurEdu\Users\Models\StudentTeacherStudent;
use App\OurEdu\Users\Traits\Invitable;
use App\OurEdu\Users\Traits\UserRatingable;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRSessionParticipant;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSessions\General\Models\UserZoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use SoftDeletes, CreatedBy, HasAttach, Notifiable, Invitable, UserRatingable, HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens; //passport auth

    protected static $attachFields = [
        'profile_picture' => [
            'sizes' => ['small/users/profiles' => 'crop,400x300', 'large/users/profiles' => 'resize,800x600'],
            'path' => 'uploads'
        ],
    ];

    protected $table = "users";
    protected $auditExclude = [
        'password',
        'profile_picture'
    ];
    protected $appends = ['name'];
    protected $fillable = [
        'first_name',
        'last_name',
        'language',
        'type',
        'username',
        'email',
        'mobile',
        'address',
        'last_logged_in_at',
        'confirmed',
        'super_admin',
        'is_admin',
        'password',
        'is_active',
        'confirm_token',
        'profile_picture',
        'suspended_at',
        'facebook_id',
        'twitter_id',
        'country_id',
        'branch_id',
        'school_id',
        'role_id',
        'otp',
        'deleted_ios_action'
    ];

    public function setPasswordAttribute($value)
    {
        if (trim($value)) {
            $this->attributes['password'] = bcrypt(trim($value));
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('confirmed', 1);
    }

    public function scopeNotSuperAdmin($query)
    {
        return $query->where('super_admin', '=', 0);
    }

    public function scopeWithoutLoggedUser($query)
    {
        return $query->where('id', '!=', auth()->id());
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'language' => $this->language,
            'type' => $this->type,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'is_admin' => $this->is_admin,
            'is_active' => $this->is_active,
            'suspended_at' => $this->suspended_at,
        ];
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function contentAuthor()
    {
        return $this->hasOne(ContentAuthor::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function payments(){

        return $this->hasMany(PaymentTransaction::class, 'receiver_id');
    }

    public function firebaseTokens()
    {
        return $this->hasMany(FirebaseToken::class);
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param Notification $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->firebaseTokens()->pluck('device_token')->toArray();
    }

    /**
     * The parent and student relation
     * @return BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }


    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    /**
     * The Student teacher and supervised student relation
     * @return BelongsToMany
     */
    public function supervisedStudents()
    {
        return $this->belongsToMany(User::class, 'student_student_teacher', 'student_teacher_id', 'student_id')
            ->withPivot('id', 'status')
            ->withTimestamps()
            ->wherePivot('status', InvitationEnums::ACCEPTED);
    }

    /**
     * The student and parent relation
     * @return BelongsToMany
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'student_student_teacher', 'student_id', 'student_teacher_id')
            ->withPivot('id', 'status')
            ->withTimestamps()
            ->wherePivot('status', InvitationEnums::ACCEPTED);
    }

    public function studentTeacherSubjects()
    {
        return $this->hasMany(StudentTeacherStudent::class, 'student_teacher_id')
            ->with('subjects');
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    /**
     * The instructor and student relation
     * @return BelongsToMany
     */
    public function instructedStudents()
    {
        return $this->belongsToMany(User::class, 'instructor_student', 'instructor_id', 'student_id');
    }

    /**
     * The student and instructor relation
     * @return BelongsToMany
     */
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'instructor_student', 'student_id', 'instructor_id');
    }

    /**
     * The student and parent invitations
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    /**
     * Parent sent payment transactions
     * @return HasMany
     */
    public function sentPaymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'sender_id');
    }

    /**
     * children spent payment transactions
     * @return HasMany
     */
    public function childrenSpentPaymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'sender_id');
    }

    /**
     * Payment orders
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * SME managed subjects
     * @return HasMany
     */
    public function managedSubjects()
    {
        return $this->hasMany(Subject::class, 'sme_id');
    }

    /**
     * studentTeacher relation
     * @return HasOne
     */
    public function studentTeacher()
    {
        return $this->hasOne(StudentTeacher::class, 'user_id');
    }


    /**
     * courses relation
     *  instructor's courses
     * @return HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * coursesStudents relation
     *  instructor's courses's students
     * @return BelongsToMany
     */
    public function coursesStudents()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'instructor_id', 'student_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_instructor')->withTimestamps();
    }

    public function schoolInstructorSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_school_instructor', 'instructor_id', 'subject_id');
    }

    public function schoolAccount()
    {
        return $this->hasOne(SchoolAccount::class, 'manager_id');
    }

    public function schoolAccountBranch()
    {
        return $this->hasOne(SchoolAccountBranch::class, 'supervisor_id');
    }

    public function schoolAccountBranchType()
    {
        if ($this->type == UserEnums::SCHOOL_LEADER) {
            return $this->hasOne(SchoolAccountBranch::class, 'leader_id');
        }
        if ($this->type == UserEnums::SCHOOL_SUPERVISOR) {
            return $this->hasOne(SchoolAccountBranch::class, 'supervisor_id');
        }

        return $this->branch();
    }

    public function schoolLeader()
    {
        return $this->hasOne(SchoolAccountBranch::class, 'leader_id');
    }

    public function schoolSupervisor()
    {
        return $this->hasOne(SchoolAccountBranch::class, 'supervisor_id');
    }

    public function transformAudit(array $data): array
    {
        if (in_array($this->getAttribute('type'), [UserEnums::STUDENT_TEACHER_TYPE, UserEnums::STUDENT_TYPE])) {
            if (array_key_exists('country_id', $data['new_values'])) {
                if (is_null($this->getOriginal('country_id'))) {
                    $data['old_values']['country_name'] = '';
                } else {
                    $data['old_values']['country_name'] = Country::find($this->getOriginal('country_id'))->name;
                }
                $data['new_values']['country_name'] = Country::find($this->getAttribute('country_id'))->name;
            }
        }
        return $data;
    }

    public function schoolInstructorBranch()
    {
        return $this->belongsTo(SchoolAccountBranch::class, 'branch_id');
    }

    public function parentData()
    {
        return $this->hasOne(ParentData::class);
    }

    public function participatedVCRs()
    {
        return $this->hasMany(VCRSessionParticipant::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(\App\OurEdu\Roles\Role::class, 'role_id')->withTrashed()->withDefault();
    }

    /**
     * @return HasMany
     */
    public function schoolInstructorSessions()
    {
        return $this->hasMany(ClassroomClassSession::class, "instructor_id");
    }

    public function branch()
    {
        return $this->belongsTo(SchoolAccountBranch::class, 'branch_id');
    }

    /**
     * @return BelongsToMany
     */
    public function branches()
    {
        return $this->belongsToMany(SchoolAccountBranch::class, "branch_user", "user_id", "branch_id");
    }

    public function VCRSessionsPresence()
    {
        return $this->hasMany(VCRSessionPresence::class);
    }

    public function school() {
        return $this->belongsTo(SchoolAccount::class, "school_id");
    }

    public function educationalSupervisorSubjects(){
        return $this->belongsToMany(Subject::class, 'edu_supervisors_subjects', 'edu_supervisor_id', 'subject_id')
        ->withPivot('edu_system_id', 'grade_class_id')
        ->with('gradeClass')
        ->withTimestamps();
    }

//    public function transactions()
//    {
//        return $this->hasMany(Transaction::class);
//    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, "created_by");
    }

    public function preparationMedia()
    {
        return $this->belongsToMany(PreparationMedia::class, 'preparation_media_student', 'student_id', 'preparation_media_id')->withPivot(['downloaded_at','viewed_at']);

    }

    public function certificates ()
    {
        return $this->belongsToMany(ThankingCertificate::class)
            ->withPivot(['id','vcr_session_id']);
    }

    public function userSessionScores(){
        return $this->hasMany(ClassroomClassSessionScores::class,'student_id');
    }



    public function schoolStudentGeneralQuizzes(){
        return $this->hasMany(GeneralQuizStudent::class, 'student_id');
    }

    public function generalQuizAnswers()
    {
        return $this->hasMany(GeneralQuizStudentAnswer::class, "student_id");
    }

    public function assessmentsAsViewer()
    {
        return $this->belongsToMany(Assessment::class, 'assessment_result_viewers', 'user_id', 'assessment_id')
            ->withPivot(['average_score', 'total_assesses_count', 'assessed_assesses_count'])
            ->withTimestamps()
            ;
    }

    public function assessmentsAsAssessee()
    {
        return $this->belongsToMany(Assessment::class, 'assessment_assessees', 'user_id', 'assessment_id');
    }

    public function assessmentUserAsAssessee()
    {
        return $this->hasMany(AssessmentUser::class, "assessee_id");
    }

    public function assessorViewerAvgScores()
    {
        return $this->hasMany(AssessmentAssessorViewer::class,'viewer_id');
    }

    public function zoom()
    {
        return $this->hasOne(UserZoom::class,'user_id');
    }

    public function assessmentsAsAssessor()
    {
        return $this->belongsToMany(Assessment::class, 'assessment_assessors', 'user_id', 'assessment_id');
    }


    public function schoolAdmin()
    {
        return $this->hasOne(SchoolAdmin::class, 'user_id');
    }

    public function schoolAdminAssignedSchools()
    {
        return $this->belongsToMany(SchoolAccount::class,"school_admin_schools", 'user_id','school_account_id');
    }

    public function getAddedBySocialAttribute()
    {
        return (!is_null($this->twitter_id) or !is_null($this->facebook_id) );
    }

    public function vcrSchedule(){
        return $this->hasMany(VCRSchedule::class,'instructor_id');
    }
}
