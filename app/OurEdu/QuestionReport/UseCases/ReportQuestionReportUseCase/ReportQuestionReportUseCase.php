<?php


namespace App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase;

use App\OurEdu\QuestionReport\Repository\QuestionReportRepository;
use App\OurEdu\QuestionReport\Repository\QuestionReportRepositoryInterface;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepositoryInterface;

class ReportQuestionReportUseCase implements ReportQuestionReportUseCaseInterface
{
    private $questionReportRepository;
    private $questionReportTaskRepository;


    public function __construct(
        QuestionReportRepositoryInterface $questionReportRepository,
        QuestionReportTaskRepositoryInterface $questionReportTaskRepository
    ) {
        $this->questionReportRepository = $questionReportRepository;
        $this->questionReportTaskRepository = $questionReportTaskRepository;
    }

    public function report($questionReportId, $user, $note, $due_date)
    {

        //Mark Question As Reported IN Question Reports Table
        $question = $this->questionReportRepository->findOrFail($questionReportId);
        $questionReportRepo = new QuestionReportRepository($question);
        $questionReportRepo->report();

        //Generate Task For Reported Question

        $this->generateTask($question, $user, $note, $due_date);
    }

    public function generateTask($question, $user, $note, $due_date)
    {
        $this->questionReportTaskRepository->create([
            'title' => __('question_reports.Check Question'). ' ' . $question->slug,
            'note' => $note,
            'slug' => $question->slug,
            'due_date' => $due_date,
            'subject_id' => $question->subject_id,
            'question_report_id' => $question->id,
            'question_type' => $question->question_type,
            'question_id' => $question->question_id,
            'subject_format_subject_id' => $question->subject_format_subject_id,
            'resource_subject_format_subject_id' => $question->resource_subject_format_subject_id,
            'created_by' => $user->id,
            'is_active' =>  true
        ]);
    }
}
