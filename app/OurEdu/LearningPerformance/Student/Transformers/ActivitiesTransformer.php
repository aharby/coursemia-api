<?php

namespace App\OurEdu\LearningPerformance\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ActivitiesTransformer extends TransformerAbstract
{
    private $student;
    private $studentExams;
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [];

    public function transform(LearningPerformance $learningPerformance)
    {
        $this->student = Student::findOrFail($learningPerformance->student_id);
        $this->studentExams = $this->student->exams();
        if ($learningPerformance->subject_id) {
            $this->studentExams = $this->studentExams->where('subject_id' , $learningPerformance->subject_id);
        }
        $transformedData = [
            'id' => Str::uuid(),
            'numberOfExams' => $this->studentExams->where('type', ExamTypes::EXAM)->count(),
            'numberOfPractices' => $this->studentExams->where('type', ExamTypes::PRACTICE)->count(),
            'numberOfCompetitions' => $this->studentExams->where('type', ExamTypes::COMPETITION)->count(),
        ];
        return $transformedData;
    }

    public function includeActions(Student $student)
    {
        $actions = [];
        // return the exams list
        if ($this->studentExams->where('type', ExamTypes::EXAM)->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.get.list-exams'),
                'label' => trans('exam.list exams'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_EXAMS
            ];
        }

        // return the practices list
        if ($this->studentExams->where('type', ExamTypes::PRACTICE)->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.practices.get.list-practices'),
                'label' => trans('exam.list practices'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_PRACTICES
            ];
        }

        // return the competitions list
        if ($this->studentExams->where('type', ExamTypes::COMPETITION)->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.competitions.get.list-competitions'),
                'label' => trans('exam.list competitions'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_COMPETITIONS
            ];
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
