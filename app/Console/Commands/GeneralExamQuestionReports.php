<?php

namespace App\Console\Commands;

use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\GeneralExamReport\Models\GeneralExamQuestionReportSubjectFormatSubject;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamStudent;
use App\OurEdu\GeneralExams\Models\GeneralExamStudentAnswer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\QuestionReport\Notifications\QuestionReportGeneratedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneralExamQuestionReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generalExam:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate General Exam questions report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $exams = GeneralExam::whereNotNull('published_at')
            ->whereNotIn('id' , GeneralExamReport::pluck('general_exam_id')->toArray())
            ->where(DB::raw("concat(date , ' ' , end_time)") , '<=' , date('Y-m-d H:i:s'))
            ->get();

        foreach($exams as $exam) {
            $totalQuestions = $exam->questions()->count();
            $studentsGroupedByAnswers = GeneralExamStudentAnswer::where('general_exam_id' , $exam->id)
                ->where('is_correct' , true)
                ->select(DB::raw('count(id) as correct_answers , student_id'))
                ->groupBy('student_id')
                ->get();

            $studentsGroupedByAnswers->each(function ($student) use ($totalQuestions) {
                $student->answersRatio = $student->correct_answers/$totalQuestions;
            });
            $twentySevenPercentCount = ceil(count($studentsGroupedByAnswers) * .27);

            $studentsGroupedByAnswers = $studentsGroupedByAnswers->sortBy('answersRatio');

            $downTwentySevenPercent = $studentsGroupedByAnswers->take($twentySevenPercentCount);
            $topTwentySevenPercent = $studentsGroupedByAnswers->take($twentySevenPercentCount * -1);

            $generalExamReport = GeneralExamReport::create(['general_exam_id' => $exam->id]);

            foreach ($exam->questions as $question){
                $totalAnswers = $question->studentAnswers()->count();
                $correctAnswers = $question->studentAnswers()->where('is_correct' , true)->count();
                $wrongAnswers = $question->studentAnswers()->where('is_correct' , false)->count();
                $difficultyParameter = $correctAnswers / ($totalAnswers > 0 ? $totalAnswers : 1);
                $easyParameter = $wrongAnswers / ($totalAnswers > 0 ? $totalAnswers : 1);
                $stabilityParameter = $difficultyParameter * $easyParameter;
                $trustParameter = pow($stabilityParameter , 2);

                $topTwentySevenPercentAnswersCount = $question->studentAnswers()
                    ->whereIn('student_id' , $topTwentySevenPercent->pluck('student_id')->toArray())
                    ->where('is_correct' , true)->count();
                $downTwentySevenPercentAnswersCount = $question->studentAnswers()
                    ->whereIn('student_id' , $downTwentySevenPercent->pluck('student_id')->toArray())
                    ->where('is_correct' , true)->count();

                $preferenceParameter =
                    (
                        ($topTwentySevenPercentAnswersCount - $downTwentySevenPercentAnswersCount) /
                        ((count($studentsGroupedByAnswers) > 0 ? count($studentsGroupedByAnswers) : 1) / 2)
                    ) * 100;

                 $generalExamReport->reportQuestion()->create([
                    'total_answers' => $totalAnswers,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $wrongAnswers,
                    'difficulty_parameter' => $difficultyParameter,
                    'easy_parameter' => $easyParameter,
                    'stability_parameter' => $stabilityParameter,
                    'trust_parameter' => $trustParameter,
                    'preference_parameter' => $preferenceParameter,
                    'general_exam_id' => $exam->id,
                    'general_exam_question_id' => $question->id,
                    'subject_format_subject_id' => $question->subject_format_subject_id
                ]);
                $this->drawHierarchy($question->subject_format_subject_id , $exam->subject_id);
            }
        }
    }


    public function drawHierarchy($sectionId , $subjectID) {

        $parentSection = getSectionParent($sectionId);
        $childId = $sectionId;
        while ($parentSection) {

            GeneralExamQuestionReportSubjectFormatSubject::firstOrCreate([
                'section_id' => $childId,
                'section_parent_id' => $parentSection->id,
                'subject_id' => $subjectID,
            ]);

            $childId = $parentSection->id;
            $parentSection =  getSectionParent($childId);
        }

        GeneralExamQuestionReportSubjectFormatSubject::firstOrCreate([
            'section_id' => $childId,
            'section_parent_id' => null,
            'subject_id' => $subjectID,
        ]);
        return 0;

    }
}
