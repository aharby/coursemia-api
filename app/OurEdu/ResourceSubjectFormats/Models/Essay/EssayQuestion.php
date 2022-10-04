<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Essay;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestionAudio;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestionMedia;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestionVideo;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoDataMedia;
use App\OurEdu\ResourceSubjectFormats\Observers\TrueFalse\TrueFalseQuestionObserver;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Observers\SubjectObserver;
use App\OurEdu\Users\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EssayQuestion extends BaseModel implements QuestionHeadInterface
{

    protected $table = 'res_essay_questions';

    protected $fillable = [
        'text',
        'perfect_answers',
        'res_essay_data_id',
        'time_to_solve',
        'question_feedback',
        'model',
        'audio_link',
        'video_link',
    ];

    public function parentData()
    {
        return $this->belongsTo(EssayData::class,'res_essay_data_id');
    }


    /*protected static function boot()
    {
        parent::boot();
        EssayQuestion::observe(TrueFalseQuestionObserver::class);
    }*/

    /**
     * @return mixed
     */
    public function questionHead()
    {
        return $this->parentData;
    }

    public function questionBank()
    {
        return $this->morphOne(GeneralQuizQuestionBank::class, "question");
    }

    public function generalQuizStudentAnswers()
    {
        return $this->morphMany(GeneralQuizStudentAnswer::class, "single_question");
    }

    public function media()
    {
        return $this->hasOne(EssayQuestionMedia::class,'res_essay_question_id');
    }
    public function audio()
    {
        return $this->hasOne(EssayQuestionAudio::class,'res_essay_question_id');
    }
    public function video()
    {
        return $this->hasOne(EssayQuestionVideo::class,'res_essay_question_id');
    }
}
