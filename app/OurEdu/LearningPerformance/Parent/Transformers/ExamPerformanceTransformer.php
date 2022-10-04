<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\ExamFeedBackTransformer;
use App\OurEdu\Exams\Student\Transformers\ExamReportRecommendationTransformer;
use App\OurEdu\Exams\Student\Transformers\QuestionTransformer;
use League\Fractal\TransformerAbstract;

class ExamPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions',
        'recommendation',
        'feedback',
    ];
    protected array $availableIncludes = [
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Exam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string)$exam->title,
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'student' => $exam->student->user->first_name,
            'difficulty_level' => trans('difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'result' => $exam->result,

            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => round($exam->time_to_solve),
            'student_time_to_solve' => round($exam->student_time_to_solve),
        ];


        return $transformerDatat;
    }



    public function includeRecommendation(Exam $exam)
    {
        $total = $exam->questions()->count();
        $correctAnswers = $exam->questions()->where('is_correct_answer', 1)->count();
        $percent = $total > 0 ? (($correctAnswers / $total) * 100) : 0;
        if (!$percent == 100) {
            return $this->item($exam, new ExamReportRecommendationTransformer(), ResourceTypesEnums::EXAM_REPORT_RECOMMENDATION);
        }
    }

    public function includeFeedback(Exam $exam)
    {
        return $this->item($exam, new ExamFeedBackTransformer(), ResourceTypesEnums::EXAM_FEEDBACK);
    }

    public function includeQuestions(Exam $exam)
    {
        $questions = $exam->questions;
        $params = [
            'is_answer' => true,
            'disable_actions' => true,
            'disable_exam' => true,
        ];

        return $this->collection($questions, new QuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION);
    }
}
