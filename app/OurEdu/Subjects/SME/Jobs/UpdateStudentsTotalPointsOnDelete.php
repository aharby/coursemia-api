<?php

namespace App\OurEdu\Subjects\SME\Jobs;

use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use App\OurEdu\Users\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStudentsTotalPointsOnDelete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $points, private $section, private $resource = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sectionParents = $this->getAllParentSubjectFormatSubject($this->section);
        $subject = $this->section->subject;

        $students =  $subject->students()->whereHas(
            'subjectFormatSubjectProgress',
            function ($section) use ($sectionParents) {
                $section->whereIn('subject_format_id', $sectionParents);
            }
        )->with(['subjectFormatSubjectProgress']);

        if (!is_null($this->resource)) {
            $students = $students->whereHas(
                'resourceSubjectProgress',
                function ($resource) {
                    $resource->where('resource_id', $this->resource->id);
                }
            );
        }

        $students = $students->get();

        foreach ($students as $student) {
            $student->subjectFormatSubjectProgress()->decrement('points', $this->points);
            $student->subscribe()->where('subject_id', $subject->id)
                ->where('subject_progress', '>=', $this->points)->update(
                    [
                        'subject_progress' => \DB::raw("subject_progress - $this->points"),
                        'subject_progress_percentage' => \DB::raw(
                            "(subject_progress  - $this->points )/ ( $subject->total_points  - $this->points) * 100 "
                        )
                    ]
                );
        }

        if (!is_null($this->resource)) {
            $this->resource->students()->where('resource_id', $this->resource->id)->delete();
        }
    }

    public function getAllParentSubjectFormatSubject($subjectFormatSubject, $allParentsArray = [])
    {
        $allParentsArray[] = $subjectFormatSubject->id;
        if ($subjectFormatSubject->parentSubjectFormatSubject) {
            $allParentsArray[] = $this->getAllParentSubjectFormatSubject(
                $subjectFormatSubject->parentSubjectFormatSubject
            );
        }

        return array_flatten($allParentsArray);
    }
}
