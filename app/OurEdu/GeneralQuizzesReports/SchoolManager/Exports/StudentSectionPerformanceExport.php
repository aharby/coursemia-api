<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentSectionPerformanceExport implements FromArray, WithEvents, ShouldAutoSize, WithHeadings
{
    private $studentSectionsGrade;
    /**
     * @var GeneralQuizStudent
     */
    private $generalQuizStudent;
    private $sectionsStudentsGrade;
    private $sectionGrades;

    /**
     * StudentSectionPerformanceExport constructor.
     * @param $studentSectionsGrade
     * @param GeneralQuizStudent $generalQuizStudent
     * @param $sectionsStudentsGrade
     * @param $sectionGrades
     */
    public function __construct($studentSectionsGrade, GeneralQuizStudent $generalQuizStudent, $sectionsStudentsGrade, $sectionGrades)
    {
        $this->studentSectionsGrade = $studentSectionsGrade;
        $this->generalQuizStudent = $generalQuizStudent;
        $this->sectionsStudentsGrade = $sectionsStudentsGrade;
        $this->sectionGrades = $sectionGrades;
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        $rows = [];
        $rowNumber =0;

        foreach($this->studentSectionsGrade as $section => $studentAnswer) {
            $studentScore = "0%";
            $generalAverage = "0%";

            if(isset($this->sectionGrades[$section]) && $this->sectionGrades[$section]->sum('grade') > 0) {
                $studentScore =  "" . round(($studentAnswer->sum('total_score')/$this->sectionGrades[$section]->sum('grade')) * 100 ,2) . "%";

                if($this->generalQuizStudent->generalQuiz->studentsAnswered->count() >0) {
                    $generalAverage = "" . round((($this->sectionsStudentsGrade[$section]->sum('total_score')/$this->generalQuizStudent->generalQuiz->studentsAnswered->count())/$this->sectionGrades[$section]->sum('grade')) * 100 ,2) . "%";
                }
            }

            $rows[] = [
                ++$rowNumber,
                $section ??'',
                $studentScore,
                $generalAverage,

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
            trans('students.NO'),
            trans('quiz.section'),
            trans('general_quizzes.Student score percentage (per section)'),
            trans('general_quizzes.General  Average Score percentage (per section)'),
        ];
    }
}
