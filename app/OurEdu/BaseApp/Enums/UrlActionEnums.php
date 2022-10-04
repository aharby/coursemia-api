<?php

namespace App\OurEdu\BaseApp\Enums;

class UrlActionEnums
{
    public static function getExamResultUrl($examId)
    {
        return  buildScopeRoute('api.parent.learningPerformance.get.examPerformance',
            ['examId' => $examId]);
    }

    public static function getUpdatedLiveSessionUrl($liveSession)
    {
        return env('APP_URL') . '/live-sessions/' . $liveSession['id'];
    }

    public static function getQuestionReportUrl($questionReport)
    {
        return env('APP_URL') . '/question-reports/' . $questionReport['id'];
    }

    public static function getSubjectProgressForParentUrl($studentId, $subjectId)
    {
        return buildScopeRoute('api.parent.learningPerformance.get.studentSubjectPerformance',[
            'studentId' => $studentId,
            'subjectId' => $subjectId,
        ]);
    }

}
