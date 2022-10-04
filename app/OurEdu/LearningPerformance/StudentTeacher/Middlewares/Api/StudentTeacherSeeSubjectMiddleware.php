<?php


namespace App\OurEdu\LearningPerformance\StudentTeacher\Middlewares\Api;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Closure;

class StudentTeacherSeeSubjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $studentTeacher = auth()->user();
        $student = Student::findOrFail($request->studentId);
        $inRelation = $studentTeacher->supervisedStudents()
            ->where('student_id', $student->user->id)
            ->exists();
        if ($inRelation) {
            if ($request->subjectId) {
                //check if the student sent the invitation to student teacher
                // if true limit the teacher access to student specified subjects in invitation
                $studentSentInvitationToTeacher = Invitation::where('sender_id' , $student->user->id)
                    ->where('invitable_type' , User::class)
                    ->where('invitable_id' , $studentTeacher->id)
                    ->exists();

                if ($studentSentInvitationToTeacher) {
                    $teacherHasAccessToStudentSubject = $studentTeacher
                        ->studentTeacherSubjects()
                        ->where('student_id', $student->user->id)
                        ->where('status', InvitationEnums::ACCEPTED)
                        ->subjects()
                        ->where('subjects.id' , $request->subjectId)
                        ->exists();
                    if (!$teacherHasAccessToStudentSubject){
                        return unauthorize();
                    }
                }
            }
            return $next($request);
        }
        return unauthorize();
    }
}
