<?php

namespace App\Modules\BaseApp\Enums;

use App\Modules\Notifications\Enums\NotificationEnum;

class DynamicLinksEnum
{

    const STUDENT_DYNAMIC_URL = '{firebase_url}/?link={portal_url}/dynamic-link%3F{query_param}&apn={android_apn}';


    const studentFinishExam = '{portal_url}/ar/subject-activities/parent/exam-results/{examId}';
    public const STUDENTPAIDBYVISA = '{portal_url}/ar/parent/dashboard?purchases=true&user_id={child_id}';
    const GENERALEXAM_PUBLISH = '{firebase_url}/?link={portal_url}/ar/student/exams/general/{id}&apn=com.Modules.students';
    const STUDENT_JOIN_COMPETITION = '{firebase_url}/studentJoinCompetition?link={portal_url}/student/competitions/join/share%3Fcompetition_id%3D{competition_id}%26target_screen%3D'.DynamicLinkTypeEnum::JOIN_COMPETITION.'&apn=com.Modules.students';
    const INSTRUCTOR_JOIN_ROOM = '{portal_url}/#/home?osession={session_id}&otoken={token}&otype={type}';
    const SUPERVISOR_JOIN_ROOM = '{portal_url}/#/home?osession={session_id}&otoken={token}&otype={type}';
    const INSTRUCTOR_VIEW_STUDENT_FEEDBACK= '{portal_url}/ar/vcr/instructor/requests/{request_id}';
    const ADMIN_ASSIGN_SME_SUBJECT= '{portal_url}/#/subjects/{subject_id}/edit';
    const STUDENT_SHARE_LIVE_SESSION= '{portal_url}/ar/student/live-lessons/{live_session_id}/share';
    const INSTRUCTOR_GENERATE_EXAM= '{portal_url}/ar/exams?session_id={session_id}';
    const STUDENT_START_VCR_EXAM= '{portal_url}/ar/student/exams/{exam_id}/take';
    // TODO change it
    const STUDENT_GET_QUIZ= '{portal_url}/ar/student/quiz/{quiz_id}/take';
    const INSTRUCTOR_VIEW_STUDENT_VCR_EXAM_FEEDBACK= '{portal_url}/ar/instructor/live-lessons/{session_id}/exams/{exam_id}/feedback';
    const STUDENT_CHALLENGE_STUDENT= '{firebase_url}/?link={portal_url}/ar/student/exam/{exam_id}/challenge?exam_id={exam_id}&apn={android_apn}';
    const NOTIFY_INSTRUCTOR_SUPERVISOR_ABSENT= 'school-branch-supervisor/sessions/{classroom_session_id}/edit';
    const STUDENT_HOMEWORK = '{portal_url}/ar/student/homework/{homeworkId}';
    const STUDENT_PERIODIC_TEST = '{portal_url}/ar/student/periodic-test/{periodicTestId}';
    const STUDENT_JOIN_COURSE_COMPETITION = '{firebase_url}/studentJoinCompetition?link={portal_url}/student/competitions/join/share%3Fcompetition_id%3D{competition_id}%26target_screen%3D'.DynamicLinkTypeEnum::JOIN_COURSE_COMPETITION.'&apn=com.Modules.students';
    const INSTRUCTOR_COURSE_COMPETITION_FEEDBACK = '{portal_url}/ar/instructor/competitions-report/feedback/{exam_id}';
    const STUDENT_EXAM_FEEDBACK = '{portal_url}/ar/student/exam/{exam_id}/feedback';
    const INSTRUCTOR_CHALLENGE_RESULTS = '{portal_url}/ar/student/challenge/results/{exam_id}';
    const STUDENT_SUBJECT_VIEW = '{portal_url}/ar/student/subjects/{subject_id}?show=subject';
    const VCR_INFO = '{portal_url}/ar/vcr/info/{session_id}';
}
