<?php

namespace App\Console\Commands;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CountPracticesNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'practices:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'practices:count';

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
        $examRepo = new ExamRepository(new Exam());
        $practices = $examRepo->practicesWithSubjectIds();
        $subjectRepo = new SubjectRepository(new Subject());

        if ($practices->isNotEmpty()){
            $subjectRepo->setPracticesNumber($practices);
        }

        return 0;

    }
}
