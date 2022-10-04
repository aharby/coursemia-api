<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'subjectFormatSubjects',
    ];
    protected array $availableIncludes = [
    ];
    private $params;
    private $exam;
    private $preparedQuestionsQuery;

    public function __construct(GeneralExam $exam, $params = [])
    {
        $this->params = $params;
        $this->exam = $exam;
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {
        $this->preparedQuestionsQuery = PreparedGeneralExamQuestion::where('subject_id', $subject->id)
            ->when(
                request('difficulty_level_id'),
                function ($query) {
                    $query->where('difficulty_level_id', request('difficulty_level_id'));
                }
            );
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
            'questions_count' => $this->preparedQuestionsQuery->count()
        ];
    }

    public function includeSubjectFormatSubjects(Subject $subject)
    {

        $sections = json_decode($this->exam->subject_format_subjects);
        $sections = getSectionsOfSections($sections);

        $subjectFormatSubjects = $subject->
        subjectFormatSubject()
            ->whereIn('id', $sections)
            ->orderBy('list_order_key', 'asc')
//                                ->whereNull('parent_subject_format_id')
            ->with('childSubjectFormatSubject')
            ->get();

        if (count($subjectFormatSubjects)) {
            return $this->collection(
                $subjectFormatSubjects,
                new ListSubjectFormatSubjectTransformer($this->exam, $this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
