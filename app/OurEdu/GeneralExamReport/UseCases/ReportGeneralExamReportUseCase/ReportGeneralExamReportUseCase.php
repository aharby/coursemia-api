<?php


namespace App\OurEdu\GeneralExamReport\UseCases\ReportGeneralExamReportUseCase;

use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportRepositoryInterface;
use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportTaskRepositoryInterface;

class ReportGeneralExamReportUseCase implements ReportGeneralExamReportUseCaseInterface
{
    private $generalExamReportRepository;
    private $generalExamReportTaskRepository;


    public function __construct(
        GeneralExamReportRepositoryInterface $generalExamReportRepository,
        GeneralExamReportTaskRepositoryInterface $generalExamReportTaskRepository
    ) {
        $this->generalExamReportRepository = $generalExamReportRepository;
        $this->generalExamReportTaskRepository = $generalExamReportTaskRepository;
    }

    public function report($questionReportId, $user, $note, $due_date)
    {

        //Mark Question As Reported IN Question Reports Table
        $question = $this->generalExamReportRepository->findQuestionOrFail($questionReportId);
        $this->generalExamReportRepository->reportQuestion($question->id);


        //Generate Task For Reported Question

        $this->generateTask($question, $user, $note, $due_date);
    }

    public function generateTask($question, $user, $note, $due_date)
    {
        $this->generalExamReportTaskRepository->create([
            'title' => __('general_exams.Check Question'). ' ' . $question->slug,
            'note' => $note,
            'slug' => $question->generalExamQuestion->question_type,
            'due_date' => $due_date,
            'subject_id' => $question->generalExamQuestion->exam->subject_id,
            'general_exam_report_question_id' => $question->id,
            'question_type' => $question->generalExamQuestion->questionable_type,
            'question_id' => $question->generalExamQuestion->questionable_id,
            'subject_format_subject_id' => $question->subject_format_subject_id,
            'created_by' => $user->id,
            'is_active' =>  true
        ]);
    }
}
