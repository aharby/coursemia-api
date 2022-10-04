<?php

namespace App\Console\Commands;


use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamStudentAnswer;
use App\OurEdu\StaticPages\Enums\DistinguishedStudentsEnum;
use App\OurEdu\StaticPages\Models\DistinguishedStudent;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDistinguishedStudentsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generalExam:distinguished-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate List Distinguished Students';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $endDate = new Carbon();
        $startDate =  (new Carbon())->subDays(DistinguishedStudentsEnum::EXAM_DATE_LIMIT_IN_DAYS);

        $exams = GeneralExam::whereNotNull('published_at')
//            ->where('date' , '>=' , $startDate)
//            ->where('date' , '<' , $endDate)
            ->get();

        foreach ($exams as $exam) {
            $answers = GeneralExamStudentAnswer::where('is_correct' , 1)
                ->where('general_exam_id' , $exam->id)
                ->select(DB::raw('count(is_correct) as count , student_id'))
                ->groupBy('student_id')
                ->orderBy('count' , 'desc')
                ->limit(DistinguishedStudentsEnum::EXAM_STUDENT_LIMIT_PER_EXAM)
                ->get();

            foreach ($answers as $answer) {
                DistinguishedStudent::create([
                    'student_id' => $answer->student_id,
                    'general_exam_id' => $exam->id,
                    'subject_id' => $exam->subject_id,
                    'total_correct' => $answer->count,
                    'total_questions' => $exam->questions()->count(),
                    'virtual_classes' => VCRSession::where('student_id', $answer->student_id)->count()
                ]);
            }
        }
        return 0;

    }
}
