<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentAverageQuestionReport;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AssessmentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Assessment $assessment)
    {

        $transformedData = [
            'id' => (int)$assessment->id,
            'title' => (string)$assessment->title,
            'introduction' => (string)$assessment->introduction,
            'assessor_type' =>(string)$assessment->assessor_type,
            'assessee_type' =>(string)$assessment->assessee_type,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'start_time' => (string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_time' => (string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessment_questions_count' => (int) $assessment->questions_count,
            'number_of_assessees' =>(int) $assessment->assessment_users_count
        ];
        $assessmentQuestionsAverage = 0;
        $assessmentQuestions = [];
        foreach ($assessment->questions as $key => $assessmentQuestion) {
            $data = [];
            $data['id'] = Str::uuid();
            $data['order'] = $key;
            $questionGrade = $assessmentQuestion->question_grade;
            $questionAverage = 0;
            if (count($assessmentQuestion->assessorsAnswers) && $questionGrade != 0) {
                $answers = $assessmentQuestion->assessorsAnswers;
                if (!$assessmentQuestion->skip_question) {
                    $questionAverage = $answers->sum('score') / ($questionGrade * $assessment->assessment_users_count);
                }else{
                    $questionAverage = $answers->average('score') / $questionGrade;
                }
            }
            $data['question_average'] = (string)number_format($questionAverage * 100, 2) . '%';
            $assessmentQuestionsAverage += $questionAverage;
            $assessmentQuestions[] = $data;
        }
        $assessmentQuestionsAverage = $assessment->questions->count() > 0 ? number_format((($assessmentQuestionsAverage / $assessment->questions->count()) * 100), 2) : 0;
        $transformedData['assessment_question_average'] = $assessmentQuestionsAverage . '%';
        $transformedData['questions'] = $assessmentQuestions;

        return $transformedData;
    }
}
