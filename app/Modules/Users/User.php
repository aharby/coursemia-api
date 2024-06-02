<?php

namespace App\Modules\Users;

use App\Modules\Assessments\Models\Assessment;
use App\Modules\Assessments\Models\AssessmentAssessorViewer;
use App\Modules\Assessments\Models\AssessmentUser;
use App\Modules\BaseApp\Traits\CreatedBy;
use App\Modules\BaseApp\Traits\HasAttach;
use App\Modules\Certificates\Models\ThankingCertificate;
use App\Modules\Countries\Models\Country;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\GeneralQuizzes\Models\GeneralQuizStudent;
use App\Modules\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\Modules\Invitations\Enums\InvitationEnums;
use App\Modules\Invitations\Models\Invitation;
use App\Modules\Payments\Enums\PaymentEnums;
use App\Modules\Payments\Enums\TransactionTypesEnums;
use App\Modules\Payments\Models\Order;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Post\Models\Post;
use App\Modules\Quizzes\Quiz;
use App\Modules\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\Modules\SchoolAccounts\ClassroomClassSessions\Instructor\Models\ClassroomClassSessionScores;
use App\Modules\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\Modules\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\Modules\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\Modules\SchoolAdmin\Models\SchoolAdmin;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Users\Models\ContentAuthor;
use App\Modules\Users\Models\FirebaseToken;
use App\Modules\Users\Models\Instructor;
use App\Modules\Users\Models\ParentData;
use App\Modules\Users\Models\Student;
use App\Modules\Users\Models\StudentTeacher;
use App\Modules\Users\Models\StudentTeacherStudent;
use App\Modules\Users\Models\UserDevice;
use App\Modules\Users\Traits\Invitable;
use App\Modules\Users\Traits\UserRatingable;
use App\Modules\VCRSchedules\Models\VCRSchedule;
use App\Modules\VCRSchedules\Models\VCRSessionParticipant;
use App\Modules\VCRSchedules\Models\VCRSessionPresence;
use App\Modules\VCRSessions\General\Models\UserZoom;
use App\UserFollow;
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

use App\Modules\payment\Models\CartCourse;

class User extends Authenticatable
{
    use HasAttach, Notifiable, Invitable, UserRatingable, HasFactory;
//    use \OwenIt\Auditing\Auditable;
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
        'photo',
        'referer_id',
        'country_code',
        'refer_code',
        'full_name',
        'email',
        'phone',
        'password',
        'country_id',
    ];

    public function devices(){
        return $this->hasMany(UserDevice::class);
    }

    public function followers(){
        return $this->hasMany(UserFollow::class, 'followed_id');
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function getRankAttribute(){
        
        $higherUsers = 0;
        
        if(array_key_exists('total_correct_answers', $this->attributes))
        {
            $higherUsers = \App\Modules\Users\Models\User::where('total_correct_answers', '>', $this->attributes['total_correct_answers'])->count();
        }

        return $higherUsers+1;
    }

    public function courses(){
        return $this->hasManyThrough(Course::class, CourseUser::class, 'user_id', 'id', 'id', 'course_id');
    }

    public function cartCourses(){
        return $this->hasManyThrough(Course::class, CartCourse::class, 'user_id', 'id', 'id', 'course_id');
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
        return $this->belongsTo(\App\Modules\Roles\Role::class, 'role_id')->withTrashed()->withDefault();
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

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'user':
                    $quer->orderBy('full_name', $sortByDir);
                    break;
                case 'email':
                    $quer->orderBy('email', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }

    public function cartItems()
    {
        return $this->hasMany(\App\Modules\CartItems\Models\CartItem::class);
    }
}
