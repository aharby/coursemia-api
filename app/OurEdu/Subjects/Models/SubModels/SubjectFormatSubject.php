<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReportQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\Subjects\Observers\SubjectFormatSubjectObserver;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Scopes\ActiveScope;
use App\OurEdu\Scopes\SubjectFormatSubjectActiveParentScope;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Exams\Models\ExamQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubjectFormatSubject extends BaseModel implements Auditable
{
    use SoftDeletes, CreatedBy, HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'subject_format_subject';

    protected $fillable = [
        'title',
        'is_active',
        'description',
        'subject_id',
        'subject_type',
        'parent_subject_format_id',
        'has_data_resources',
        'created_by',
        'is_editable',
        'list_order_key',
        'total_points',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope(new SubjectFormatSubjectActiveParentScope());


        static::addGlobalScope('activeWhereParentActive', function (Builder $builder) {
            if (\Auth::check()){
                if (in_array(auth()->user()->type, [UserEnums::STUDENT_TYPE, UserEnums::PARENT_TYPE , UserEnums::CONTENT_AUTHOR_TYPE])) {
                    $builder->whereHas('subject', function ($query) {
                        $query->where('is_active', 1);
                    });
                }
            }

        });

        SubjectFormatSubject::observe(SubjectFormatSubjectObserver::class);
        static::deleting(function (self $subjectFormatSubject) {

            $childSubjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->get();
            foreach ($childSubjectFormatSubjects as $childSubjectFormatSubject) {
                $childSubjectFormatSubject->delete();
            }

            $resourceSubjectFormatSubjects = $subjectFormatSubject->resourceSubjectFormatSubject()->get();
            foreach ($resourceSubjectFormatSubjects as $resourceSubjectFormatSubject) {
                $resourceSubjectFormatSubject->delete();
            }

            $likes = $subjectFormatSubject->likes()->get();
            foreach ($likes as $like) {
                $like->delete();
            }

            $examQuestions = $subjectFormatSubject->examQuestions()->get();
            foreach ($examQuestions as $examQuestion) {
                $examQuestion->delete();
            }

            $tasks = $subjectFormatSubject->tasks()->get();
            foreach ($tasks as $task) {
                $task->delete();
            }

            $activeReportTasks = $subjectFormatSubject->activeReportTasks()->get();
            foreach ($activeReportTasks as $activeReportTask) {
                $activeReportTask->delete();
            }


            $reports = $subjectFormatSubject->reports()->get();
            foreach ($reports as $report) {
                $report->delete();
            }

            $reportedSubjectFormatSubjectsId = $subjectFormatSubject->reportedSubjectFormatSubject()->get()->pluck("id");
            $subjectFormatSubject->reportedSubjectFormatSubject()->detach($reportedSubjectFormatSubjectsId);

            $questionReportSubjectFormatSubjectsId = $subjectFormatSubject->questionReportSubjectFormatSubject()->get()->pluck("id");
            $subjectFormatSubject->questionReportSubjectFormatSubject()->detach($questionReportSubjectFormatSubjectsId);

            $generalExamQuestionReportSubjectFormatSubjectsId = $subjectFormatSubject->generalExamQuestionReportSubjectFormatSubject()->get()->pluck("id");
            $subjectFormatSubject->generalExamQuestionReportSubjectFormatSubject()->detach($generalExamQuestionReportSubjectFormatSubjectsId);

            $generalExamQuestionReports = $subjectFormatSubject->generalExamQuestionReport()->get();
            foreach ($generalExamQuestionReports as $generalExamQuestionReport) {
                $generalExamQuestionReport->delete();
            }

            $preparedGeneralExamQuestions = $subjectFormatSubject->preparedGeneralExamQuestions()->get();
            foreach ($preparedGeneralExamQuestions as $preparedGeneralExamQuestion) {
                $preparedGeneralExamQuestion->delete();
            }

            $preparedExamQuestions = $subjectFormatSubject->preparedExamQuestions()->get();
            foreach ($preparedExamQuestions as $preparedExamQuestion) {
                $preparedExamQuestion->delete();
            }

            $times = $subjectFormatSubject->time()->get();
            foreach ($times as $time) {
                $time->delete();
            }
        });
    }

    public function childSubjectFormatSubject()
    {
        return $this->hasMany(SubjectFormatSubject::class, 'parent_subject_format_id');
    }

    public function parentSubjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class, 'parent_subject_format_id');
    }

    public function allChildSubjectFormatSubject()
    {
        return $this->childSubjectFormatSubject()->with('allChildSubjectFormatSubject');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function resourceSubjectFormatSubject()
    {
        return $this->hasMany(ResourceSubjectFormatSubject::class);
    }

    public function likes()
    {
        return $this->hasMany(SubjectFormatSubjectLikes::class);
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'subject_format_subject_id');
    }

    public function generalQuizQuestions()
    {
        return $this->hasMany(GeneralQuizQuestionBank::class, 'subject_format_subject_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'subject_format_subject_id');
    }

    public function activeTasks()
    {
        return $this->hasMany(Task::class, 'subject_format_subject_id')->active();
    }

    public function activeReportTasks()
    {
        return $this->hasMany(QuestionReportTask::class, 'subject_format_subject_id')->active();
    }

    public function reports()
    {
        return $this->morphMany('App\OurEdu\Reports\Report','reportable');
    }

    public function reportedSubjectFormatSubject() {

        return $this->belongsToMany(SubjectFormatSubject::class, 'report_subject_format_subject', 'section_parent_id' , 'section_id' );
    }

    public function questionReportSubjectFormatSubject() {

        return $this->belongsToMany(SubjectFormatSubject::class, 'question_report_subject_format_subject', 'section_parent_id', 'section_id');
    }

    public function generalExamQuestionReportSubjectFormatSubject() {

        return $this->belongsToMany(SubjectFormatSubject::class, 'general_exam_question_report_subject_format_subject', 'section_parent_id', 'section_id');
    }

    public function questionReport() {

        return $this->hasMany(QuestionReport::class, 'subject_format_subject_id');
    }

    public function generalExamQuestionReport() {

        return $this->hasMany(GeneralExamReportQuestion::class, 'subject_format_subject_id');
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.subject_id')) {
            if(is_null($this->getOriginal('subject_id'))){
                $data['old_values']['subject_name'] = '';
            }
            else{
                $data['old_values']['subject_name'] = Subject::find($this->getOriginal('subject_id'))->name;
            }
            $data['new_values']['subject_name'] = Subject::find($this->getAttribute('subject_id'))->name;
        }
        return $data;
    }

    public function preparedGeneralExamQuestions()
    {
        return $this->hasMany(PreparedGeneralExamQuestion::class, 'subject_format_subject_id');
    }

    public function preparedExamQuestions()
    {
        return $this->hasMany(PrepareExamQuestion::class, 'subject_format_subject_id');
    }

    public function time()
    {
        return $this->morphMany(SubjectTime::class ,'timable');
    }

}
