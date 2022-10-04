<?php


namespace App\OurEdu\Exams\Student\Transformers;


use Illuminate\Support\Str;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Enums\ExamEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class ListSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'exam_group_recommendation_less_effort',
        'exam_group_recommendation_more_effort'
    ];
    protected array $availableIncludes = [
    
    ];
    public function __construct(public array $params = [])
    {
    
    }
    public function transform(Exam $exam)
    {
        $transformerData = [
            'id' => (int)$exam->id,
        ];
        return $transformerData;
    }

    public function includeExamGroupRecommendationMoreEffort()
    {
        if(isset($this->params['examRecomendationMoreEffort'])){
            return $this->collection(
                collect($this->params['examRecomendationMoreEffort']),
                new ListRecomendationMoreEffortExam(),
                ResourceTypesEnums::EXAM_RECOMMENDATION_MORE_EFFORT
            );
        }
    }

    public function includeExamGroupRecommendationLessEffort()
    {

        if(isset($this->params['examRecomendationLessEffort'])){
            return $this->collection(collect($this->params['examRecomendationLessEffort']), 
            new ListRecommendationLessEffortExam(),
            ResourceTypesEnums::EXAM_RECOMMENDATION_LESS_EFFORT);
        }
    }
}
