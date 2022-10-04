<?php

namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SectionTransformer extends TransformerAbstract
{
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'sections',
        'parent'
    ];

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        $parentExists = $subjectFormatSubject->parentSubjectFormatSubject()->exists();
        return [
            'id' => (int) $subjectFormatSubject->id,
            'title' => (string) $subjectFormatSubject->title,
            'has_sections' => (bool) $subjectFormatSubject->childSubjectFormatSubject()->exists(),
            'has_parent' => (bool) $subjectFormatSubject->parentSubjectFormatSubject()->exists(),
            'parent_id' => $parentExists?$subjectFormatSubject->parentSubjectFormatSubject->id:null,
            'list_order_key' =>  $subjectFormatSubject->list_order_key,
        ];
    }

    /**
     * @param $subject
     * @return \League\Fractal\Resource\Collection
     */
    public function includeActions($subjectFormatSubject)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if ($user->type == UserEnums::STUDENT_TYPE) {
                if ($subjectFormatSubject->childSubjectFormatSubject()->count()) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.subjects.view-section-sections', [ 'sectionId' => $subjectFormatSubject->id]),
                        'label' => trans('subject.View Section units'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_CHILDS
                    ];
                }
            }
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSections(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('list_order_key', 'ASC')->get();;

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new static($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
    public function includeParent(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->parentSubjectFormatSubject;

        if ($subjectFormatSubjects) {
            return $this->item(
                $subjectFormatSubjects,
                new static($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
