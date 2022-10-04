<?php


namespace App\OurEdu\Exams\Student\Transformers;


use Illuminate\Support\Str;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Enums\ExamEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class ExamReportRecommendationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'recommendationSubjectFormat',
        'exam_group_recommendation',
    ];
    protected array $availableIncludes = [
    ];

    public function transform($exam)
    {
        $transformerData = [
            'id' => (int)$exam->id,
        ];
        return $transformerData;
    }

    public function includeRecommendationSubjectFormat($exam)
    {
        $subject_format_subject_ids = $exam->questions()->pluck('subject_format_subject_id')->toArray();
       
        $subjectFormat = SubjectFormatSubject::whereIn('id', $subject_format_subject_ids)->get();
       
        return $this->collection($subjectFormat, new SubjectFormatSubjectRecommendationTransformer($exam), ResourceTypesEnums::EXAM_REPORT_RECOMMENDATION_ITEM);
    }
    
    public function includeExamGroupRecommendation(Exam $exam)
    {
        $params = [];
        $subject_format_subject_ids = $exam->questions()->pluck('subject_format_subject_id')->toArray();
        $subjectFormat = SubjectFormatSubject::whereIn('id', $subject_format_subject_ids)->get();
        foreach($subjectFormat as $section){
            $score = $this->score($section, $exam);
            if ($score >= ExamEnums::RECOMENDATION_PERCENTAGE) {
                $params['examRecomendationLessEffort'][] = $section;
            }else{
                $params['examRecomendationMoreEffort'][] = $section;
            }
           
        }
       
        return $this->item($exam, new ListSubjectFormatSubjectTransformer($params), ResourceTypesEnums::EXAM_GROUP_RECOMMENDATION_ITEM);
    }

    private function score($subjectFormatSubject, $exam)
    {
        $score = 0;
        $countCorrectAnswerInSesction = $exam->questions()->where('is_correct_answer', 1)
                      ->where('subject_format_subject_id', $subjectFormatSubject->id)->count();
        $countQuestionInSesction = $exam->questions()
                      ->where('subject_format_subject_id', $subjectFormatSubject->id)->count();
        
        $score = $countQuestionInSesction  > 0 ? (($countCorrectAnswerInSesction / $countQuestionInSesction ) * 100) : 0;

        return $score;
        
    }
}
