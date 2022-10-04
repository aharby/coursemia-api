<?php

namespace App\OurEdu\Notifications\Enums;

use BenSampo\Enum\Enum;

final class NotificationEnum extends Enum
{
    // subject related notifications
    const NOTIFY_PARENT_SUBJECT_PROGRESS_50 = 'notify_parent_by_50';
    const NOTIFY_PARENT_SUBJECT_PROGRESS_100 = 'notify_parent_by_100';
    const ACTIVATION_MAIL = 'activation_mail';
    const FORGOT_PASSWORD = 'forgot_password';
    const INSTRUCTOR_VCR_SESSION = 'instructor_vcr_session';
    const NOTIFY_PARENT_ABOUT_EXAM_RESULT = 'notify_parent_about_exam_result';
    const NOTIFY_PARENT_ABOUT_VISA_PAYMENT = 'notify_parent_about_visa_payment';
    const SUPERVISE_INVITATION = 'supervise_invitation';
    const NOTIFY_SME_ABOUT_REPORT = 'notify_sme_about_report';
    const LIVE_SESSION_DELETED = 'live_session_deleted';
    const LIVE_SESSION_UPDATED = 'live_session_updated';
    const ADMIN_ASSIGN_SME_TO_SUBJECT = 'admin_assign_sme_to_subject';
    const STUDENT_VCR_EXAM = 'student_vcr_exam';
    const NOTIFY_PARENT_ABOUT_STUDENT_ABSENCE = 'notify_parent_about_student_absence';
    const NOTIFY_PARENT_ABOUT_STUDENT_LEAVE_SESSION = 'notify_parent_about_student_leave_session';
    const NOTIFY_SUPERVISOR_ABOUT_SCHOOL_INSTRUCTOR_ABSENCE = 'notify_supervisor_about_school_instructor_absence';
    const NOTIFY_INSTRUCTOR_ABOUT_NEW_SESSION_ASSIGNED = 'notify_instructor_about_new_session_assigned';
    const INSTRUCTOR_VIEW_STUDENT_VCR_EXAM_FEEDBACK = 'instructor_view_student_exam_feedback';
    const NOTIFY_INSTRUCTOR_VCR_AGORA_SESSION = 'notify_instructor_vcr_agora_session';
    const NOTIFY_INSTRUCTOR_VCR_ZOOM_SESSION = 'notify_instructor_vcr_zoom_session';
    const NOTIFY_STUDENT_VCR_AGORA_SESSION = 'notify_student_vcr_agora_session';
    const NOTIFY_STUDENT_VCR_ZOOM_SESSION = 'notify_student_vcr_zoom_session';
    const RECEIVED_INVITATION = 'received_invitation';
    const STUDENT_HOMEWORK = 'student_home_work';
    const SESSION_QUIZ ='session_quiz';
    const STUDENT_PERIODIC_TEST = 'student_periodic_test';
    const VCR_SCHEDULED = 'vcr_scheduled';
    const COMPETITION_FEEDBACK_INSTRUCTOR = 'competition_feedback_instructor';
    const NOTIFY_STUDENT_VCR_SESSION = 'notify_student_vcr_session';
    public const VIEW_CHALLNGED_RESULTS = 'view_challnged_results';

}
