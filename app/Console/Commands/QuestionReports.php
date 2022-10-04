<?php

namespace App\Console\Commands;

use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\QuestionReport\Models\QuestionReportSubjectFormatSubject;
use App\OurEdu\QuestionReport\Notifications\QuestionReportGeneratedNotification;
use DB;
use Illuminate\Console\Command;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;

class QuestionReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate questions report';

    private $notifierFactory;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotifierFactory $notifierFactory)
    {
        parent::__construct();
        $this->notifierFactory =$notifierFactory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $examQuestions = DB::table('exam_questions')
            ->select(DB::raw('count(id) as total_id'), 'question_table_id', 'question_table_type', 'subject_id')
            ->groupBy('question_table_id', 'question_table_type', 'subject_id')
            ->get();
        foreach ($examQuestions as $examQuestion) {
            $examQuestion = ExamQuestion::where(
                'question_table_id',
                $examQuestion->question_table_id
            )->where(
                'question_table_type',
                $examQuestion->question_table_type
            )->where('subject_id', $examQuestion->subject_id)->first();

            $allAnswer = ExamQuestion::where(
                'question_table_id',
                $examQuestion->question_table_id
            )->where(
                'question_table_type',
                $examQuestion->question_table_type
            )->where('subject_id', $examQuestion->subject_id)->count();

            $allCorrectAnswer = ExamQuestion::where(
                'question_table_id',
                $examQuestion->question_table_id
            )->where(
                'question_table_type',
                $examQuestion->question_table_type
            )->where(
                'subject_id',
                $examQuestion->subject_id
            )->where('is_correct_answer', 1)->count();
            if ($examQuestion->questionable()->exists()) {
                $last_update = $examQuestion->questionable->first()->updated_at;

                $checkEquationAnswer = calculateQuestionResult($allCorrectAnswer, $allAnswer);

                $checkIfReportExists = QuestionReport::where(
                    'question_id',
                    $examQuestion->question_table_id
                )->where(
                    'question_type',
                    $examQuestion->question_table_type
                )->where(
                    'subject_id',
                    $examQuestion->subject_id
                )->latest()->first();

                if ($checkIfReportExists) {
                    if ($allAnswer > $checkIfReportExists->total_answer) {
                        $checkEquationAnswer = calculateQuestionResult(
                            $allCorrectAnswer - $checkIfReportExists->correct_answer,
                            $allAnswer - $checkIfReportExists->total_answer
                        );
                    }
                }
                if ($checkEquationAnswer != $examQuestion->exam->difficulty_level) {
                    $header = null;
                    $questionData = $examQuestion->questionable;
                    switch ($examQuestion->slug) {
                        case (LearningResourcesEnums::TRUE_FALSE):
                            $header = $questionData->text;
                            break;
                        case (LearningResourcesEnums::MULTI_CHOICE):
                            $header = $questionData->question;
                            break;
                        case (LearningResourcesEnums::COMPLETE):
                            $header = $questionData->question;
                            break;
                        case (LearningResourcesEnums::MATCHING):
                        case (LearningResourcesEnums::MULTIPLE_MATCHING):
                        case (LearningResourcesEnums::DRAG_DROP):
                            $header = $questionData->description;
                            break;
                    }

                    if (!$checkIfReportExists) {
                        $report = QuestionReport::create([
                            'slug' => $examQuestion->slug,
                            'subject_id' => $examQuestion->subject_id,
                            'subject_format_subject_id' => $examQuestion->subject_format_subject_id,
                            'difficulty_level' => $examQuestion->exam->difficulty_level,
                            'difficulty_level_result_equation' => $checkEquationAnswer,
                            'question_type' => $examQuestion->question_table_type,
                            'question_id' => $examQuestion->question_table_id,
                            'total_answer' => $allAnswer,
                            'correct_answer' => $allCorrectAnswer,
                            'header' => $header,
                            'is_ignored' => 0,
                            'last_update' => $last_update,
                        ]);
                        $this->drawHierarchy($report->subject_format_subject_id , $report->subject_id);
                        $this->notifySmeAboutReport($report);
                    } else {
                        if ($checkIfReportExists->last_update != $last_update && $allAnswer > $checkIfReportExists->total_answer) {
                            $report = QuestionReport::create([
                                'slug' => $examQuestion->slug,
                                'subject_id' => $examQuestion->subject_id,
                                'subject_format_subject_id' => $examQuestion->subject_format_subject_id,
                                'difficulty_level' => $examQuestion->exam->difficulty_level,
                                'difficulty_level_result_equation' => $checkEquationAnswer,
                                'question_type' => $examQuestion->question_table_type,
                                'question_id' => $examQuestion->question_table_id,
                                'total_answer' => $allAnswer - $checkIfReportExists->total_answer,
                                'correct_answer' => ($allCorrectAnswer - $checkIfReportExists->correct_answer) > 0 ? ($allCorrectAnswer - $checkIfReportExists->correct_answer) : 0,
                                'header' => $header,
                                'is_ignored' => 0,
                                'last_update' => $last_update,
                            ]);
                            $this->drawHierarchy($report->subject_format_subject_id , $report->subject_id);
                            $this->notifySmeAboutReport($report);
                        }
                    }
                }
            }
        }

        return 0;

    }

    protected function notifySmeAboutReport($report)
    {
        if ($sme = $report->subject->sme) {
            $sme->notify(new QuestionReportGeneratedNotification($report));
        }
    }

    public function drawHierarchy($sectionId , $subjectID) {

        $parentSection = getSectionParent($sectionId);
        $childId = $sectionId;
        while ($parentSection) {

            QuestionReportSubjectFormatSubject::firstOrCreate([
                'section_id' => $childId,
                'section_parent_id' => $parentSection->id,
                'subject_id' => $subjectID,
            ]);

            $childId = $parentSection->id;
            $parentSection =  getSectionParent($childId);
        }

        QuestionReportSubjectFormatSubject::firstOrCreate([
            'section_id' => $childId,
            'section_parent_id' => null,
            'subject_id' => $subjectID,
        ]);
    }

}
