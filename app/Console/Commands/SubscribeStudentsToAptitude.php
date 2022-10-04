<?php

namespace App\Console\Commands;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subscribes\Subscribe;
use App\OurEdu\Users\Models\Student;
use Illuminate\Console\Command;

class SubscribeStudentsToAptitude extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe-students:aptitude';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'subscribe-students:aptitude';
    private $mailNotification;


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
        // subscribe all old registered students to aptitude subject
        $aptitudeSubject = Subject::where('is_aptitude',true)->first();
        if(!$aptitudeSubject)
            exit('no aptitude subject found!');

        // exclude already subscribed students
        $alreadySubscribedStudentsIds = Subscribe::where('subject_id',$aptitudeSubject->id)->pluck('student_id');
        $notSubscribedYetStudentIds = Student::whereNotIn('id', $alreadySubscribedStudentsIds)->pluck('id');
        $aptitudeSubject->students()->attach($notSubscribedYetStudentIds);
        return 0;

    }
}
