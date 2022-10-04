<?php


namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SkillPercentageLevelReportExport implements FromArray, WithEvents, ShouldAutoSize, WithHeadings
{
    private $generalQuizzes;

    /**
     * SkillPercentageLevelReportExport constructor.
     * @param $generalQuizzes
     */
    public function __construct($generalQuizzes)
    {
        $this->generalQuizzes = $generalQuizzes;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $classrooms = '';

        foreach ($this->generalQuizzes as $generalQuizz) {

            foreach ($generalQuizz->classrooms as $classroom) {
                $classrooms .=  "({$classroom->name})";
            }

            $rows[] = [
                $generalQuizz->branch->name ?? '',
                $generalQuizz->gradeClass->title ?? '',
                $classrooms,
                $generalQuizz->subject->name ?? '',
                trans('quiz.' . $generalQuizz->quiz_type) ?? '',
                $generalQuizz->title,
                \Carbon\Carbon::parse($generalQuizz->start_at)->format('Y/m/d') ?? '',
            ];

            $classrooms = "";
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
            }
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.Branch Name'),
            trans('quiz.gradeClass'),
            trans('quiz.classroom'),
            trans('quiz.subject'),
            trans('quiz.type'),
            trans('quiz.name'),
            trans('quiz.quiz date'),
        ];
    }
}
