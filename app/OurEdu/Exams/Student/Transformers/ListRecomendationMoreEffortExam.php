<?php


namespace App\OurEdu\Exams\Student\Transformers;


use Illuminate\Support\Str;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Enums\ExamEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class ListRecomendationMoreEffortExam extends TransformerAbstract
{
    protected array $defaultIncludes = [
        
    ];
    protected array $availableIncludes = [
    
    ];
   
    public function transform($subjectFormat )
    {
        $transformerData = [
            'id' => (int)$subjectFormat['id'] ,
            'title' => $subjectFormat['title']
        ];
        return $transformerData;
    }


}
