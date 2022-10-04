<?php


namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\QuestionTransformer;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRScheduleTransformer;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRSessionTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;

class ChallengeTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($student)
    {
        $user = $student->user;

        $transformerData = [
            'id' => (int)$user->id,
            'name' => (string)$user->name,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
            'exam_id' => (int)$student->exam->id,
        ];

        return $transformerData;
    }

    public function includeActions($student)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.exams.get.viewChallengeExam', ['examId' => $student->exam->id]),
            'label' => trans('exam.view exam'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_EXAM
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

}
