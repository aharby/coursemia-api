<?php


namespace App\OurEdu\LearningPerformance\StudentTeacher\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\LearningPerformance\LearningPerformance;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentSubjectPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'examsPerformance',
        'activityLog',
    ];


    public function transform(LearningPerformance $learningPerformance)
    {
        return [
            'id' => Str::uuid(),
            'student_order' => (int) $learningPerformance->student_order,
            'solving_speed_percentage_order' =>  $learningPerformance->solving_speed_percentage_order, // according to all students
            'subject_progress_percentage_order' =>  $learningPerformance->subject_progress_percentage_order, // according to all students
            'exams_count_order' =>  $learningPerformance->exams_count_order, // according to all students
            'number_of_taken_exams' => (int) $learningPerformance->student->exams()
                ->where('subject_id', $learningPerformance->subject->id)
                ->where('type', ExamTypes::EXAM)->count(),
            'success_rate' =>  $learningPerformance->success_rate,
            'activity_pagination' =>  $learningPerformance->student->events()
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->where('event_properties->subject_attributes->subject_id', $learningPerformance->subject->id)
                ->latest()->paginate(10, ['id'], 'activity-page'),
            'exams_pagination' =>  $learningPerformance->subject->exams()->where('type', ExamTypes::EXAM)
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->paginate(10, ['id'], 'exams-page')
            ];
    }


    public function includeExamsPerformance(LearningPerformance $learningPerformance)
    {
        $exams = $learningPerformance->subject->exams()
                ->where('type', ExamTypes::EXAM)
                ->where('student_id', $learningPerformance->student->id)
                ->paginate(10, ['*'], 'exams-page');

        if (count($exams) > 0) {
            return $this->collection($exams, new ExamsPerformanceTransformer(), ResourceTypesEnums::Exam);
        }
    }

    public function includeActivityLog(LearningPerformance $learningPerformance)
    {
        $activities = $learningPerformance->student->events()
            ->when(request('date_from'), function ($q)  {
                $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->where('event_properties->subject_attributes->subject_id', $learningPerformance->subject->id)
            ->latest()->paginate(10, ['*'], 'activity-page');
        if (count($activities) > 0) {
            return $this->collection($activities, new ActivitiesLogTransformer(), ResourceTypesEnums::Exam);
        }
    }
}

