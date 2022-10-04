<?php

namespace App\Console\Commands;

use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Illuminate\Console\Command;

class MakeCoursesAndSubjectsOutOfDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coursesAndSubjects:outOfDate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update the out_of_date column to true after the end_date passed';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    // local variables
    private $subjectRepository;
    private $courseRepository;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        CourseRepositoryInterface $courseRepository
    )
    {
        parent::__construct();
        $this->subjectRepository = $subjectRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->subjectRepository->makeSubjectsOutOfDate();

        $this->courseRepository->makeCoursesOutOfDate();

        return 0;

    }


}
