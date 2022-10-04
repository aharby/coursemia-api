<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\SME\Transformers\SubjectMediaTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class SubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects',
        'subjectMedia'
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'educational_term' => (string)($subject->educationalTerm->title ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'start_date' => (string)$subject->start_date,
            'end_date' => (string)$subject->end_date,
            'is_active' => (boolean)$subject->is_active,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_library_text' => $subject->subject_library_text,
            'direction'=>$subject->direction,
        ];
    }

    /**
     * @param $subject
     * @return \League\Fractal\Resource\Collection
     */
    public function includeActions($subject)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.pause-unpause.subject', ['id' => $subject->id]),
            'label' => $subject->is_active ? trans('api.pause') : trans('api.un pause'),
            'method' => 'POST',
            'key' => $subject->is_active ? APIActionsEnums::PAUSE : APIActionsEnums::UN_PAUSE
        ];
       if (count($actions) > 0) {
           return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
       }
    }


    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjects = $subject->subjectFormatSubject();

        $subjectFormatSubjectsData = $subjectFormatSubjects->orderBy('list_order_key')
            ->whereNull('parent_subject_format_id')->get();

        if (count($subjectFormatSubjectsData)) {
            return $this->collection(
                $subjectFormatSubjectsData,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
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
