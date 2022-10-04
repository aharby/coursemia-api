<?php


namespace App\OurEdu\StaticPages\Transformers\DistinguishedStudents;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class DistinguishedStudentsListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'student',
        'subject',
    ];

    protected array $availableIncludes = [
    ];

    /**
     * @param $distinguishedStudent
     * @return array
     */
    public function transform($distinguishedStudent)
    {
        return [
            'id' => $distinguishedStudent->id,
            'general_exam_id' => $distinguishedStudent->general_exam_id,
            'subject_id' => $distinguishedStudent->subject_id,
            'total_correct' => $distinguishedStudent->total_correct,
            'total_questions' => $distinguishedStudent->total_questions,
            'virtual_classes' => $distinguishedStudent->virtual_classes
        ];
    }

    public function includeStudent($distinguishedStudent) {

        $student = $distinguishedStudent->student;
        if ($student) {

            return $this->item($student, new StudentTransformer(), ResourceTypesEnums::STUDENT);
        }
    }

    public function includeSubject($distinguishedStudent) {

        $subject = $distinguishedStudent->subject;
        if ($subject) {
            return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }
}
