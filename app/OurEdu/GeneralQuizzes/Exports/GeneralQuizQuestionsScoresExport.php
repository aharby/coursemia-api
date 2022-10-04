<?php

namespace App\OurEdu\GeneralQuizzes\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class GeneralQuizQuestionsScoresExport extends BaseExport implements WithMapping, ShouldAutoSize , WithEvents
{
    private $index = 0;
    public function __construct(private Collection $collection, private GeneralQuiz $generalQuiz)
    {
        parent::__construct($collection);
    }

    public function map($row): array
    {
        $this->index++;
        $grades = [];
        $grades['index'] = (int)$this->index;
        $grades['student_name'] = (string)$row->user->name ?? '';
        $grades['is_active'] = $row->user->is_active ? "✓" : "X";
        $answers = $row->user->generalQuizAnswers->keyBy('general_quiz_question_id')->toArray();

         $scoreSum = 0;
        foreach ($this->generalQuiz->questions as $key => $data) {
             $score = 0;
            if (array_key_exists($data->id, $answers)) {
                $answer = $answers[$data->id];
                $score = $answer['score'];
            }
            $scoreSum += $score;
            $grades['question_num_'.$key] =  (string)  $score;
        }

        $grades['sum'] = (string) $scoreSum;

        return $grades;
    }

    public function headings(): array
    {
        $heading[] = trans('general_quizzes.Id');
        $heading[] =trans('general_quizzes.student_name');
        $heading[] =trans('app.Is active');

        $questions = $this->generalQuiz->questions()->count();
        for ($x = 1; $x <= $questions; $x++) {
            $heading[] = trans('general_quizzes.Question Number', ['num'=>$x]);
        }

        $heading[] =trans('general_quizzes.full_score');

        return $heading;
    }

    public function registerEvents(): array
    {
        if (lang() == 'ar') {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                },
            ];
        }

        return [];
    }
}
