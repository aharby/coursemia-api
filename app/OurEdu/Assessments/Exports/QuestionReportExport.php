<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuestionReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    private $questions;
    private array $params;

    /**
     * QuestionReportExport constructor.
     * @param $questions
     * @param array $params
     */
    public function __construct($questions, array $params=[])
    {
        $this->questions = $questions;
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $serialNumber = 1;

        foreach ($this->questions as $question) {
            $row = [
                $serialNumber++,
                strip_tags($question->question->question?? ""),
            ];

            if ($question->slug != QuestionTypesEnums::ESSAY_QUESTION){
                $score_percentage = $question->question_grade > 0 ? ($this->getScore($question)/$question->question_grade)*100 : 0;
                $row[] = number_format($score_percentage, 2);
            }

            $row[] = $question->category?->title ?? '';

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans("assessment.serial_number"),
            trans("assessment.question text"),
            trans("assessment.question_average_score"),
            trans("assessment.category")
        ];
    }

    private function getScore(AssessmentQuestion $assessmentQuestion):float
    {
        if ($this->params["hasBranch"]) {
            return $assessmentQuestion->branchScores[0]->pivot->score ?? 00.0;
        }

        return $assessmentQuestion->average_score;
    }
}
