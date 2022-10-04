<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\GeneralExams\SME\Transformers\ResourceSubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCase;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'resourceSubjectFormatSubjects'
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
        ];
    }

    public function includeSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('id', 'asc')->get();
        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeResourceSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $resources = $subjectFormatSubject->resourceSubjectFormatSubject()->orderBy('id', 'asc')->get();
        if (count($resources) > 0) {
            return $this->collection(
                $resources,
                new ResourceSubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeActions($subjectFormatSubject)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if ($user->type == UserEnums::SME_TYPE) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.sme.general_exams.getSectionQuestions', ['section' => $subjectFormatSubject]),
                    'label' => trans('general_exams.Section Questions'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::AVAILABLE_SECTION_QUESTIONS
                ];
            }
        }

        if (! count($actions)) {
            return;
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
