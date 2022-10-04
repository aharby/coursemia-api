<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Users\UserEnums;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AssessmentAverageQuestionReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    protected $assessments;
    public function __construct(Collection $assessments)
    {
        $this->assessments = $assessments;
    }
    /**
     * @param mixed $row
     *
     * @return array
     */

    public function collection()
    {
        $this->assessments->each(function ($assessment) {
            $this->map($assessment);
        });
        return $this->assessments;
    }


    public function map($assessment): array
    {
        $data = [
            'title' => (string)$assessment->title,
            'assessor_type' =>(string)$assessment->assessor_type ? trans('app.' . $assessment->assessor_type) : '',
            'assessee_type' =>(string)$assessment->assessee_type ? trans('app.' . $assessment->assessee_type) : '',
            'number_of_assessees' =>(int) $assessment->assessment_users_count
        ];

        $assessmentQuestionAverage = 0;
        $questionAverages = [];
        foreach($assessment->questions as $key => $assessmentQuestion){
            $questionGrade = $assessmentQuestion->question_grade; 
            $questionAverage = 0;
            if(count($assessmentQuestion->assessorsAnswers) && $questionGrade != 0){
                $answerGradeSum = $assessmentQuestion->assessorsAnswers;
                if(!$assessmentQuestion->skip_question){
                    $questionAverage = $answerGradeSum->sum('score') / ($questionGrade * $assessment->assessment_users_count);
                }else{
                    $questionAverage = $answerGradeSum->average('score')/$questionGrade;
                }
            }
            $assessmentQuestionAverage += $questionAverage;
            $questionAverages['#'.$key+1] = (string)number_format($questionAverage * 100, 2).'%';
        }
        $assessmentQuestionAverage = $assessment->questions->count() >0 ? number_format((($assessmentQuestionAverage / $assessment->questions->count())*100), 2):0;
        $data['assessment_question_average'] = $assessmentQuestionAverage.'%';
        
        return array_merge($data,$questionAverages);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $data = [
            trans('assessment.title'),
            trans('assessment.assessor_type'),
            trans('assessment.assessee_type'),
            trans('assessment.number_of_assessees_on_this_assessment'),
            trans('assessment.assessment_question_average'),
        ];

        $count = $this->assessments->max('questions_count');
        for($i=1; $i <= $count; $i++){
            $data[] = '#'.$i; 
        }
        return $data;
    }

}
