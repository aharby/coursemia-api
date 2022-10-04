<?php


namespace App\OurEdu\Subjects\Instructor\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class SubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'sections'
    ];
    private $params;

    public function __construct($params = [] )
    {
        $this->params = $params;
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {
        $curencyCode = $subject->educationalSystem->country->currency ?? '';
      
        $transformedData =  [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'subscription_cost' => $subject->subscription_cost . " " . $curencyCode,
            'subject_image' => (string) imageProfileApi($subject->image, 'large'),
            'color' => (string)$subject->color,
            'subject_library_text' => $subject->subject_library_text,
        ];

        return $transformedData;
    }

    public function includeSections(Subject $subject)
    {
        $sections = $subject->subjectFormatSubject()
            ->doesntHave('activeReportTasks')
            ->doesntHave('activeTasks')
            ->where('parent_subject_format_id', null)
            ->orderBy('list_order_key')
            ->get();

        if (count($sections)) {
            return $this->collection(
                $sections,
                new SectionTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
