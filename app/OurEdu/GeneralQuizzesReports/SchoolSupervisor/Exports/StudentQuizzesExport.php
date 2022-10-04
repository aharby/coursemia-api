<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Exports;


use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentQuizzesExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    private $studentQuizzes;

    /**
     * StudentQuizzesExpor constructor.
     * @param $studentQuizzes
     */
    public function __construct($studentQuizzes)
    {
        $this->studentQuizzes = $studentQuizzes;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows =[];

        foreach ($this->studentQuizzes as $studentQuiz) {
            $averagePerformance = "0%";
            $studentAverage = "0%";

            if ($studentQuiz->generalQuiz && $studentQuiz->generalQuiz->mark >0) {
                $averagePerformance = "" . number_format(($studentQuiz->score / $studentQuiz->generalQuiz->mark)* 100, 2) . "%";
                $studentAverage = "" . number_format(($studentQuiz->generalQuiz->average_scores / $studentQuiz->generalQuiz->mark)* 100,2) . "%";
            }

            $rows[] =  [
                $studentQuiz->subject->name ??'',
                $studentQuiz->generalQuiz->quiz_type ? trans("general_quizzes." . $studentQuiz->generalQuiz->quiz_type) :'',
                $studentQuiz->generalQuiz->title ??'',
                Carbon::parse($studentQuiz->generalQuiz->start_at)->format("Y/m/d") ??'',
                Carbon::parse($studentQuiz->generalQuiz->start_at)->format("h:i a") ??'',
                Carbon::parse($studentQuiz->generalQuiz->end_at)->format("h:i a") ??'',
                $averagePerformance,
                $studentAverage,
            ];
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle("A1:Z1")->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.subject'),
            trans('quiz.type'),
            trans('quiz.name'),
            trans('quiz.time'),
            trans('quiz.started_at'),
            trans('quiz.ended_at'),
            trans("general_quizzes.Average student performance"),
            trans("general_quizzes.The overall average of the students"),
        ];
    }
}
