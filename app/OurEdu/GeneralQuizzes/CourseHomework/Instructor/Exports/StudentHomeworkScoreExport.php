<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentHomeworkScoreExport extends BaseExport implements WithMapping, ShouldAutoSize
{
    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;

    /**
     * StudentPeriodicTestScoreExport constructor.
     * @param Collection $collection
     * @param GeneralQuiz $generalQuiz
     */
    public function __construct(Collection $collection, GeneralQuiz $generalQuiz)
    {
        parent::__construct($collection);
        $this->generalQuiz = $generalQuiz;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.student ID'),
            trans('quiz.student name'),
            trans('general_quizzes.attends'),
            trans('general_quizzes.score'),
            trans('quiz.percentage'),
        ];
    }

    /**
     * @param mixed $student
     *
     * @return array
     */
    public function map($student): array
    {
        $generalQuizStudent = GeneralQuizStudent::query()
            ->where('student_id',$student->id)
            ->where('general_quiz_id',$this->generalQuiz->id)
            ->first();

        return [
            'id' => (int)$student->id,
            'name' => trim($student->name),
            'is_attend' => $student->is_active ?
                (isset($generalQuizStudent) ? "✓" : "X")
                : trans('general_quizzes.inactive'),
            'score' => $generalQuizStudent ? $generalQuizStudent->score : 0,
            'score_percentage' => $generalQuizStudent ? $generalQuizStudent->score_percentage : 0,
        ];
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
}
