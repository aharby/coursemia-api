<?php


namespace App\OurEdu\Exams\Student\Transformers;


use Illuminate\Support\Str;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Enums\ExamEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectRecommendationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        
    ];
    public function __construct(public Exam $exam)
    {

    }
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        $score = $this->score($subjectFormatSubject);
    
        $title = $score >= ExamEnums::RECOMENDATION_PERCENTAGE ? trans('exam.you are creative ') : trans('exam.you need effort');
        
        $transformerData = [
            'id' => (int)$subjectFormatSubject->id,
            'title' => $title . ' ' . (string)$subjectFormatSubject->title ,
            'score' => $this->score($subjectFormatSubject) . '%'
        ];
        return $transformerData;
    }


    private function score($subjectFormatSubject)
    {
        $score = 0;
        $countCorrectAnswerInSesction = $this->exam->questions()->where('is_correct_answer', 1)
                      ->where('subject_format_subject_id', $subjectFormatSubject->id)->count();
        $countQusetionInSesction = $this->exam->questions()
                      ->where('subject_format_subject_id', $subjectFormatSubject->id)->count();
        
        $score = $countQusetionInSesction  > 0 ? (($countCorrectAnswerInSesction / $countQusetionInSesction ) * 100) : 0;

        return $score;
        
    }

}
