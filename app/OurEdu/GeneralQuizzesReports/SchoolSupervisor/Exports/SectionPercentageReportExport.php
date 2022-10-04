<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SectionPercentageReportExport implements FromArray, WithEvents, WithHeadings, ShouldAutoSize
{
    private $sections;
    private $generalQuiz;
    private $sectionGrades;

    /**
     * SectionPercentageReportExport constructor.
     * @param $sections
     * @param $generalQuiz
     * @param $sectionGrades
     */
    public function __construct($sections, $generalQuiz, $sectionGrades)
    {
        $this->sections = $sections;
        $this->generalQuiz = $generalQuiz;
        $this->sectionGrades = $sectionGrades;
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->sections as $key => $section) {
            $rows[] = [
                $key ??'',
                $this->generalQuiz->studentsAnswered->count() ?? 0 ,
                (isset($this->sectionGrades[$key]) && $this->sectionGrades[$key]->sum('grade') > 0 && $this->generalQuiz->studentsAnswered->count() >0) ? round((($section->sum('total_score')/$this->generalQuiz->studentsAnswered->count())/$this->sectionGrades[$key]->sum('grade')) * 100 ,2) : 0 ,
                $section->min('total_score') ?? 0,
                $section->max('total_score') ?? 0,
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
            }];    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.section'),
            trans('quiz.students count'),
            trans('general_quizzes.Average Score'),
            trans('quiz.min score'),
            trans('quiz.max score'),
        ];
    }
}
