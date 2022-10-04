<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions;


use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCase;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class CompetitionReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [

    ];

    public function __construct()
    {
    }

    public function transform($exam)
    {
        $finishExamUseCase = new FinishExamUseCase(new ExamRepository(new Exam()), new ExamQuestionRepository(new ExamQuestion()));
        $studentResults = $finishExamUseCase->finishExamCompetition($exam->id);


        $results=[];
        foreach ($studentResults as $studentsResult) {
            $results[] = [
                "id" => $studentsResult['id'],
                "name" => $studentsResult['name'],
                "avg" => $studentsResult['avg'],
            ];
        }
        $transformerData = [
            'id' => Str::uuid(),
            'results'=>$results
        ];
        return $transformerData;
    }


}
