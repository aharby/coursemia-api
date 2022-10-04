<?php

namespace App\OurEdu\Profile\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ActivitiesTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [];

    public function transform(Student $student)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'numberOfExams' => $student->exams->where('type', ExamTypes::EXAM)->count(),
            'numberOfPractices' => $student->exams->where('type', ExamTypes::PRACTICE)->count(),
            'numberOfCompetitions' => $student->exams->where('type', ExamTypes::COMPETITION)->count(),
        ];
        return $transformedData;
    }

    public function includeActions(Student $student)
    {
        $actions = [];
        // return the exams list
        if ($student->exams->where('type', ExamTypes::EXAM)->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.get.list-exams'),
                'label' => trans('exam.list exams'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_EXAMS
            ];
        }

        // return the practices list
        if ($student->exams->where('type', ExamTypes::PRACTICE)->count() > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.practices.get.list-practices'),
                'label' => trans('exam.list practices'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_PRACTICES
            ];
        }

        // return the competitions list
        if ($student->exams->where('type', ExamTypes::COMPETITION)->count() > 0) {
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
