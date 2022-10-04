<?php

namespace App\Console\Commands;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Users\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddSubjectToResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:updateSubject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resource:updateSubject';

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
        ResourceSubjectFormatSubject::chunk(100, function ($resources) {
            foreach ($resources as $resource) {
                if (isset($resource->subjectFormatSubject->subject_id)) {
                    $resource->update(['subject_id' => $resource->subjectFormatSubject->subject_id]);

                }


            }
        });

        return 0;

    }
}
