<?php

namespace Database\Seeders;

use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDatabase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vcr_sessions')->delete();
        DB::statement("ALTER TABLE vcr_sessions AUTO_INCREMENT = 1");

        DB::table('quizzes')->delete();
        DB::statement("ALTER TABLE quizzes AUTO_INCREMENT = 1");

        DB::table('ratings')->delete();
        DB::statement("ALTER TABLE ratings AUTO_INCREMENT = 1");

        DB::table('recorded_vcr_sessions')->delete();
        DB::statement("ALTER TABLE recorded_vcr_sessions AUTO_INCREMENT = 1");

        DB::table('resource_progress_student')->delete();
        DB::statement("ALTER TABLE resource_progress_student AUTO_INCREMENT = 1");

        DB::table('quiz_questions')->delete();
        DB::statement("ALTER TABLE quiz_questions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');
        DB::table('quiz_questions_answers')->delete();
        DB::statement("ALTER TABLE quiz_questions_answers AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');
        DB::table('quiz_questions_options')->delete();
        DB::statement("ALTER TABLE quiz_questions_options AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');
        DB::table('all_student_quiz')->delete();
        DB::statement("ALTER TABLE all_student_quiz AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('student_reports')->delete();
        DB::statement("ALTER TABLE student_reports AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('student_teachers')->delete();
        DB::statement("ALTER TABLE student_teachers AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('student_student_teacher')->delete();
        DB::statement("ALTER TABLE student_student_teacher AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');


        DB::table('student_quiz')->delete();
        DB::statement("ALTER TABLE student_quiz AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('course_sessions')->delete();
        DB::statement("ALTER TABLE course_sessions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('course_student')->delete();

        dump(now()->format('H:i:s') . ' ');


        //        DB::table('notifications')->delete();
        DB::table('parent_student')->delete();
        DB::table('teacher_student_subject')->delete();
        //        DB::table('telescope_entries')->delete();
        //        DB::table('telescope_entries_tags')->delete();
        //        DB::table('telescope_monitoring')->delete();
        DB::table('tracked_subject_notifications')->delete();
        DB::statement("ALTER TABLE tracked_subject_notifications AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        //        DB::table('tracked_vcr_notifications')->delete();
        //        DB::statement("ALTER TABLE tracked_vcr_notifications AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_session_media')->delete();
        DB::statement("ALTER TABLE vcr_session_media AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_schedule_instructor')->delete();
        DB::statement("ALTER TABLE vcr_schedule_instructor AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_schedule_instructor_days')->delete();
        DB::statement("ALTER TABLE vcr_schedule_instructor_days AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_requests')->delete();
        DB::statement("ALTER TABLE vcr_requests AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_reminder')->delete();
        DB::statement("ALTER TABLE vcr_reminder AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('transactions')->delete();
        DB::statement("ALTER TABLE transactions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('distinguished_students')->delete();
        DB::table('subject_school_instructor')->delete();
        DB::statement("ALTER TABLE distinguished_students AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('students')->delete();
        DB::statement("ALTER TABLE students AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('subscriptions')->delete();
        DB::statement("ALTER TABLE subscriptions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('subject_times')->delete();
        DB::statement("ALTER TABLE subject_times AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');


        DB::table('subject_subscribe_students')->delete();
        DB::statement("ALTER TABLE subject_subscribe_students AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('subject_instructor')->delete();
        DB::statement("ALTER TABLE subject_instructor AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('students_feedback')->delete();
        DB::statement("ALTER TABLE students_feedback AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('psychological_results')->delete();
        DB::statement("ALTER TABLE psychological_results AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('psychological_answers')->delete();
        DB::statement("ALTER TABLE psychological_answers AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('preparation_media')->delete();
        DB::statement("ALTER TABLE preparation_media AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('payment_transactions')->delete();
        DB::statement("ALTER TABLE payment_transactions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('packages_subscribed_students')->delete();
        DB::statement("ALTER TABLE packages_subscribed_students AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('parent_data')->delete();
        DB::statement("ALTER TABLE parent_data AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('orders')->delete();
        DB::statement("ALTER TABLE orders AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_finish_log')->delete();
        DB::statement("ALTER TABLE vcr_finish_log AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('invitations')->delete();
        DB::statement("ALTER TABLE invitations AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('import_job_errors')->delete();
        DB::statement("ALTER TABLE import_job_errors AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('instructors')->delete();
        DB::statement("ALTER TABLE instructors AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('import_jobs')->delete();
        DB::statement("ALTER TABLE import_jobs AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('instructor_competition_student')->delete();
        DB::statement("ALTER TABLE instructor_competition_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');


        DB::table('instructor_competition_question_student')->delete();
        DB::statement("ALTER TABLE instructor_competition_question_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('instructor_student')->delete();
        DB::statement("ALTER TABLE instructor_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('live_session_participants')->delete();
        DB::statement("ALTER TABLE live_session_participants AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('instructor_competition_question_student')->delete();
        dump(now()->format('H:i:s') . ' ');

        DB::table('invitation_subject')->delete();
        dump(now()->format('H:i:s') . ' ');

        DB::table('general_exam_student_answers')->delete();
        DB::statement("ALTER TABLE general_exam_student_answers AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('general_exam_student_answers_details')->delete();
        DB::statement("ALTER TABLE general_exam_student_answers_details AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_schedule_instructor_days')->delete();
        DB::statement("ALTER TABLE vcr_schedule_instructor_days AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('educational_term_school_account')->delete();
        dump(now()->format('H:i:s') . ' ');

        DB::table('edu_supervisors_subjects')->delete();
        DB::statement("ALTER TABLE edu_supervisors_subjects AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('exams')->delete();
        DB::statement("ALTER TABLE exams AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('firebase_tokens')->delete();
        DB::statement("ALTER TABLE firebase_tokens AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('general_exam_student')->delete();
        DB::statement("ALTER TABLE general_exam_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('classroom_class_sessions')->delete();
        DB::statement("ALTER TABLE classroom_class_sessions AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('classroom_classes')->delete();
        DB::statement("ALTER TABLE classroom_classes AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('classroom_student')->delete();
        DB::statement("ALTER TABLE classroom_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('competition_student')->delete();
        DB::statement("ALTER TABLE competition_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('competition_question_student')->delete();
        DB::statement("ALTER TABLE competition_question_student AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_sessions_presence')->delete();
        DB::statement("ALTER TABLE vcr_sessions_presence AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('classrooms')->delete();
        DB::statement("ALTER TABLE classrooms AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('branch_educational_system')->delete();
        DB::statement("ALTER TABLE branch_educational_system AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('vcr_sessions_participants')->delete();
        DB::statement("ALTER TABLE vcr_sessions_participants AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('branch_educational_system_grade_class')->delete();
        DB::table('school_account_educational_system')->delete();
        DB::table('school_account_grade_class')->delete();
        DB::statement("ALTER TABLE branch_educational_system_grade_class AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('school_account_branches')->update(['supervisor_id' => null, 'leader_id' => null]);
        DB::table('school_account_branches')->delete();
        DB::statement("ALTER TABLE school_account_branches AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');


        DB::table('session_preparations')->delete();
        DB::statement("ALTER TABLE session_preparations AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('academic_year_school_account')->delete();
        dump(now()->format('H:i:s') . ' ');

        DB::table('school_accounts')->delete();
        DB::statement("ALTER TABLE school_accounts AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('school_accounts')->delete();
        DB::statement("ALTER TABLE school_accounts AUTO_INCREMENT = 1");
        dump(now()->format('H:i:s') . ' ');

        DB::table('users')
            ->whereNotIn(
                'type',
                [
                    UserEnums::SME_TYPE,
                    UserEnums::SUPER_ADMIN_TYPE,
                    UserEnums::CONTENT_AUTHOR_TYPE
                ]
            )
            ->delete();
    }
}
