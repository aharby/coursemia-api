<?php


namespace App\OurEdu\GeneralQuizzesReports\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;

class ExportQuestionsPercentageReportDetails extends BaseExport implements WithMapping, ShouldAutoSize
{
    public $generalQuiz;
    private $index;

    public function __construct(Collection $collection, GeneralQuiz $generalQuiz)
    {
        parent::__construct($collection);
        $this->generalQuiz = $generalQuiz;
        $this->index = 1;
    }

    public function map($question): array
    {
      return [
          'id' => $this->index++,
          'question'=> strip_tags(html_entity_decode(htmlspecialchars_decode($this->getquestiontext($question)))),
          'question_percentage_section' => $question->section->title ??'',
          'question grade' => $question->grade,
          'Average Score'=> $this->getAvgScore($question) .'%'

      ];

    }

    public function  headings(): array
    {
        return [
        trans ('reports.id'),
        trans('general_quizzes.question'),
        trans('quiz.question_percentage_section') ,
        trans('quiz.question grade') ,
        trans('general_quizzes.Average Score')

        ];

    }

    private function getquestiontext($question)
    {
        $text =  $question->questions->text ??  $question->questions->question ?? "" ;
        if($question->slug == "drag_drop_text" or $question->slug == "drag_drop_image"){
            $text =  $question->questions->description ?? "" ;
        }

        return $text;
    }

    private function getAvgScore($question)
    {
       $avg='0';
       $generalQuiz = $this->generalQuiz;
       if( $question->grade > 0 and isset($generalQuiz->attend_students)and $generalQuiz->attend_students > 0) {
       $avg=  number_format((($question->groupStudentAnswersByQuestion[0]->total_score ?? 0)/($question->grade*$generalQuiz->attend_students)) * 100, 2);
       }

        return $avg;
    }


}