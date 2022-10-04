<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [];

    public function transform($transaction)
    {
        $subscribableName = $this->resolveSubscribableName($transaction);
        $transformedData = [
            'id' => $transaction->id,
            'description' => trans('app.spent') . " "
                . " " . trans('app.on'). " " . trans('app.Subscribe') . " "
                . $subscribableName
        ];
        return $transformedData;
    }

    private function resolveSubscribableName($transaction){
        $subscribable = $transaction->detail->subscribable;
        $subscribableType = $transaction->detail->subscribable_type;
        $type = "";
        $name = "";

        switch ($subscribableType) {
            case Subject::class:
                $type = trans('app.Subject');
                $name =  $subscribable->name;
                break;
            case Course::class:
                $type = trans('app.'. CourseEnums::getFormattedTypes($subscribable->type));
                $name =  $subscribable->name;
                break;
            case Package::class:
                $type = trans('app.SubjectPackage');
                $name =  $subscribable->name;
                break;
        }

        return $type . " " . $name;
    }
}
