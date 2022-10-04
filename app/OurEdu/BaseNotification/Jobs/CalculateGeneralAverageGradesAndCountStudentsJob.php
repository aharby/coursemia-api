<?php


namespace App\OurEdu\BaseNotification\Jobs;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\Models\Student;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateGeneralAverageGradesAndCountStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;


    /**
     * CalculateGeneralAverageGradesAndCountStudentsJob constructor.
     * @param GeneralQuiz $generalQuiz
     */
    public function __construct(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
    }

    public function handle()
    {
        $endTime = new Carbon($this->generalQuiz->end_at);
        if ($endTime->isFuture()) {
            $this->release($endTime->addMinute()->diffInSeconds(Carbon::now()));

            return;
        }

        $allStudentsCounts = $this->generalQuiz->students()->count();

        // saving each classroom related data
        // if (!$allStudentsCounts) {
        //     $studentsAttendedQuiz = $allStudentsCounts = 0;
        //     $classrooms = $this->generalQuiz->classrooms()->pluck("id")->toArray();
        //     foreach($classrooms as $classroom){
        //         $classroomStudentIds = Student::query()
        //             ->whereHas("user",function($query){
        //                 $query->where('is_active',1)->where('confirmed', 1);
        //             })
        //             ->where("classroom_id", $classroom)
        //             ->pluck('user_id')->toArray();

        //         $classroomStudentsCount = count($classroomStudentIds);

        //         $classroomAttendedStudents = $this->generalQuiz->studentsAnswered()->whereIn('student_id',$classroomStudentIds);

        //         $classroomAttendedStudentsCount = $classroomAttendedStudents->count();

        //         $classroomAverageScore = $classroomAttendedStudents->average("score") ?? 0;

        //         $classroomAbsentQuizCount  =  $classroomStudentsCount - $classroomAttendedStudentsCount;

        //         $allStudentsCounts += $classroomStudentsCount;

        //         $this->generalQuiz->classrooms()->where('classroom_id',$classroom)->update([
        //             'average_score'=>$classroomAverageScore,
        //             "total_students" => $classroomStudentsCount,
        //             "attend_students" => $classroomAttendedStudentsCount,
        //             "absent_students" => $classroomAbsentQuizCount,
        //         ]);
        //     }
        // }


        if (!$allStudentsCounts) {
            $classrooms = $this->generalQuiz->classrooms()->pluck("id")->toArray();

            $allStudentsCounts = Student::query()
                ->whereHas("user")
                ->whereIn("classroom_id", $classrooms)
                ->count();
        }

        $studentsAttendedQuiz = $this->generalQuiz->studentsAnswered()->count();
        $averageScore = $this->generalQuiz->studentsAnswered()->average("score") ?? 0;
        $studentsTotalMarks = $this->generalQuiz->studentsAnswered()->sum("score") ?? 0;
        $studentsAbsentQuiz = $allStudentsCounts - $studentsAttendedQuiz;
        $data = [
            "average_scores" => $averageScore,
            "students_total_marks" => $studentsTotalMarks,
            "total_students" => $allStudentsCounts,
            "attend_students" => $studentsAttendedQuiz,
            "absent_students" => $studentsAbsentQuiz,
        ];

        GeneralQuiz::query()->where("id", "=", $this->generalQuiz->id)->update($data);
    }
}
