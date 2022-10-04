<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

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
        if (auth()->user()->type == UserEnums::STUDENT_TYPE) {
            $student = auth()->user();
        } else {
            $student = $this->params['studentUser'];
        }

        $progress = calculateSectionProgress($subjectFormatSubject, $student);

        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'description' => (string)$subjectFormatSubject->description,
            'progress' => $progress,
            'list_order_key' => $subjectFormatSubject->list_order_key,
            'direction'=>$subjectFormatSubject->subject->direction,
            'has_children' => $subjectFormatSubject->childSubjectFormatSubject->count() > 0 ? true :false,
            'has_resources' => $subjectFormatSubject->resourceSubjectFormatSubject->count() > 0 ? true : false
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
                if ($subjectFormatSubject->subject->students()->where('subject_subscribe_students.student_id', auth()->user()->student->id)->exists()) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.subjects.viewSubjectFormatSubjectDetails', ['sectionId' => $subjectFormatSubject->id]),
                        'label' => trans('subject.View Section Details'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
                    ];
                }
            }
        }
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
