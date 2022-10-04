<?php


namespace App\OurEdu\GeneralQuizzes\SchoolManager\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class GeneralQuizStudentScoreExport extends BaseExport implements WithMapping, ShouldAutoSize
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
            trans('quiz.student name'),
            trans('quiz.student ID'),
            trans('students.classroom'),
            trans('quiz.result'),
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
            'name' => (string)trim($student->name),
            'username' => (string)$student->username,
            'classroom' => (string)$student->student->classroom->name ?? "",
            'score' => $student->is_active ? ($generalQuizStudent ? $generalQuizStudent->score . "/" . $this->generalQuiz->mark : trans(
                'general_quizzes.did not attend'
            )) : trans('general_quizzes.inactive'),
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
