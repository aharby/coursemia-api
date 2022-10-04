<?php

namespace App\OurEdu\Reports;

abstract class ReportEnum
{
    const SUBJECT_TYPE = 'subject',
        EXAM_QUESTION_TYPE = 'exam_question',
        SECTION_TYPE = 'section',
        RESOURCE_TYPE = 'resource',
        SUBJECT_FORMAT_SUBJECT_MODEL = 'App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject',
        RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL = 'App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject',
        SUBJECT_MODEL = 'App\OurEdu\Subjects\Models\Subject';

    /**
     * @param string $type
     * @return string|null
     */
    public static function getType(string $type): ?string
    {
        if (isset(self::getTypes()[$type])) {
            return self::getTypes()[$type];
        }
        return null;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::SUBJECT_TYPE => 'App\OurEdu\Subjects\Models\Subject',
            self::SECTION_TYPE => 'App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject',
            self::RESOURCE_TYPE => 'App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject',
            self::EXAM_QUESTION_TYPE => 'App\OurEdu\Exams\Models\ExamQuestion'
        ];
    }

    /**
     * @return array
     */
    public static function availableTypes()
    {
        return [
            self::SUBJECT_TYPE => self::SUBJECT_TYPE,
            self::SECTION_TYPE => self::SECTION_TYPE,
            self::RESOURCE_TYPE => self::RESOURCE_TYPE,
            self::EXAM_QUESTION_TYPE => self::EXAM_QUESTION_TYPE,
        ];
    } public static function getAvailableTypesTrans($type)
    {
        $data= [
            self::SUBJECT_MODEL=>trans('reports.subject'),
            self::SUBJECT_FORMAT_SUBJECT_MODEL=>trans('reports.section'),
            self::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL=>trans('reports.resource'),
        ];
        return $data[$type] ??'';

    }
}
