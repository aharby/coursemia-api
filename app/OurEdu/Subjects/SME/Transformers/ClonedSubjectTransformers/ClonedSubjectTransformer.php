<?php

namespace App\OurEdu\Subjects\SME\Transformers\ClonedSubjectTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\SME\Transformers\SubjectMediaTransformer;
use League\Fractal\TransformerAbstract;

class ClonedSubjectTransformer extends TransformerAbstract
{
    private $params;
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects',
        'actions',
        'subjectMedia'
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param Subject $clonedSubject
     * @return array
     */
    public function transform(Subject $clonedSubject)
    {
        return [
            'id' => (int)$clonedSubject->id,
            'name' => (string)$clonedSubject->name,
            'educational_system' => (string)($clonedSubject->educationalSystem->name ?? ''),
            'educational_term' => (string)($clonedSubject->educationalTerm->title ?? ''),
            'academical_years' => (string)($clonedSubject->academicalYears->title ?? ''),
            'grade_class' => (string)($clonedSubject->gradeClass->title ?? ''),
            'start_date' => (string)$clonedSubject->start_date,
            'end_date' => (string)$clonedSubject->end_date,
            'is_active' => (boolean)$clonedSubject->is_active,
            'section_type' => is_null($clonedSubject->section_type) ? 'section' : $clonedSubject->section_type,
            'subject_library_text' => $clonedSubject->subject_library_text,
        ];
    }

    public function includeSubjectFormatSubjects(Subject $clonedSubject)
    {
        $clonedSubjectFormatSubjectId = request('subject_format_subject_id');
        $clonedSubjectFormatSubjects = $clonedSubject->subjectFormatSubject();
        $clonedSubjectFormatSubjectsData = $clonedSubjectFormatSubjects->orderBy('list_order_key', 'asc')
            ->whereNull('parent_subject_format_id')->get();

        return $this->collection(
            $clonedSubjectFormatSubjectsData,
            new ClonedSubjectFormatSubjectTransformer($this->params),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }

    public function includeActions($clonedSubject)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.post.clone-subject', ['id' => $clonedSubject->id]),
            'label' => trans('subject.Clone'),
            'method' => 'POST',
            'key' => APIActionsEnums::CLONE_SUBJECT
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
