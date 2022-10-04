<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [  'sections'];
    protected array $availableIncludes = [

    ];

    public function transform(Subject $subject)
    {
        $curencyCode = $subject->educationalSystem->country->currency ?? '';
      
        return [
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
    }

    public function includeSections(Subject $subject)
    {
        $subjectFormatSubjectsData = $subject->subjectFormatSubject()
            ->doesntHave('activeReportTasks')
            ->doesntHave('activeTasks')
            ->whereNull('parent_subject_format_id')
            ->orderBy('list_order_key', 'ASC')->get();

        if (count($subjectFormatSubjectsData)) {
            return $this->collection(
                $subjectFormatSubjectsData,
                new SubjectFormatSubjectTransformer(),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
