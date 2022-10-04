<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    private $params;
    protected array $defaultIncludes = [
        'subjectMedia'
    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects',
        'actions'
    ];

    private $student;

    public function __construct($params = [], Student $student = null)
    {
        $this->params = $params;
        $this->student = $student;
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {
      $currencyCode = $this->student->educationalSystem->country->currency ?? '';

//        $ext = pathinfo($path, PATHINFO_EXTENSION);

//        if(isset($this->params['minimal_data']) && $this->params['minimal_data']){
//            return [
//                'id' => (int)$subject->id,
//                'name' => (string)$subject->name,
//                'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
//            ];
//        }
        $transformedData = [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'educational_term' => (string)($subject->educationalTerm->title ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'start_date' => (string)$subject->start_date,
            'end_date' => (string)$subject->end_date,
            'is_active' => (boolean)$subject->is_active,
            'subscription_cost' => $subject->subscription_cost . " " . $currencyCode,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_library_text' => $subject->subject_library_text,
            'subject_media_rules' => getResourceRules("subject_media"),
            'direction'=>$subject->direction,


        ];

        if (!is_null($this->student)) {
            $transformedData['is_subscribe'] = is_student_subscribed($subject , $this->student->user);
        }
        return $transformedData;
    }

    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjectId = request('subject_format_subject_id');
        $subjectFormatSubjects = $subject->subjectFormatSubject();
//
//        if (!empty($subjectFormatSubjectId)) {
//            $subjectFormatSubject = $subjectFormatSubjects->where('id', $subjectFormatSubjectId)->first();
//        } else {
//            $subjectFormatSubject = $subjectFormatSubjects->orderBy('order', 'asc')->first();
//        }
        $subjectFormatSubjectsData = $subjectFormatSubjects->orderBy('list_order_key', 'asc')
            ->whereNull('parent_subject_format_id')->get();

        if (count($subjectFormatSubjectsData) > 0) {
            return $this->collection(
                $subjectFormatSubjectsData,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeActions($subject)
    {
        if (isset($this->params['subjectListAction'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.get.clone-subject', ['id' => $subject->id]),
                'label' => trans('subject.get clone'),
                'method' => 'GET',
                'key' => APIActionsEnums::GET_CLONE_SUBJECT
            ];
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.get.subject', ['id' => $subject->id]),
                'label' => trans('subject.view subject structure'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_DETAILS
            ];
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.get.subjectDetails', ['id' => $subject->id]),
                'label' => trans('subject.view subject details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_DETAILS
            ];
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.update-structural.subject', ['id' => $subject->id]),
            'label' => trans('subject.Save'),
            'method' => 'PUT',
            'key' => APIActionsEnums::SAVE_SUBJECT_STRUCTURAL
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.update-structural.subject', ['id' => $subject->id, 'is_generate' => 'true']),
            'label' => trans('subject.Save and Generate'),
            'method' => 'PUT',
            'key' => APIActionsEnums::SAVE_AND_GENERATE_SUBJECT_STRUCTURAL
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeSubjectMedia(Subject $subject)
    {
        $subjectMedia = $subject->media;

        if (count($subjectMedia) > 0) {
            return $this->collection(
                $subjectMedia,
                new SubjectMediaTransformer(),
                ResourceTypesEnums::SUBJECT_MEDIA
            );
        }
    }
}
