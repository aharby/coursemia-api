<?php

namespace App\Providers;

use App\Listeners\AcademicYear\AcademicYearCreatedSyncListener;
use App\Listeners\AcademicYear\CreateAcademicYearListener;
use App\Listeners\AcademicYear\UpdateAcademicYearListener;
use App\Listeners\EducationalSystem\CreateEducationalSystemListener;
use App\Listeners\EducationalSystem\EducationalSystemCreatedSyncListener;
use App\Listeners\EducationalSystem\UpdateEducationalSystemListener;
use App\Listeners\EducationalTerm\CreateEducationalTermListener;
use App\Listeners\EducationalTerm\EducationalTermCreatedSyncListener;
use App\Listeners\EducationalTerm\UpdateEducationalTermListener;
use App\Listeners\GradeClass\GradeCreatedListener;
use App\Listeners\GradeClass\GradeCreatedSyncListener;
use App\Listeners\GradeClass\GradeUpdatedListener;
use App\Listeners\Subject\CreateSubjectListener;
use App\Listeners\Subject\SubjectCreatedSyncListener;
use App\Listeners\Subject\UpdateSubjectListener;
use App\OurEdu\EducationalSystems\EducationalSystem;

class RabbitEventsServiceProvider extends \RabbiteventsMod\Events\RabbitEventsServiceProvider
{

    protected $listen = [
        'dashboard:educational_system.create' => [
            CreateEducationalSystemListener::class,
        ],
        'dashboard:educational_system.update' => [
            UpdateEducationalSystemListener::class,
        ],
        "dashboard:educational_system.create.sync" => [
            EducationalSystemCreatedSyncListener::class
        ],
        "dashboard:grade.create" => [
            GradeCreatedListener::class
        ],
        "dashboard:grade.create.sync" => [
            GradeCreatedSyncListener::class
        ],
        "dashboard:grade.update" => [
            GradeUpdatedListener::class
        ],
        'dashboard:academic_year.create' => [
            CreateAcademicYearListener::class,
        ],
        'dashboard:academic_year.update' => [
            UpdateAcademicYearListener::class,
        ],
        "dashboard:academic_year.create.sync" => [
            AcademicYearCreatedSyncListener::class
        ],
        'dashboard:semester.create' => [
            CreateEducationalTermListener::class,
        ],
        'dashboard:semester.update' => [
            UpdateEducationalTermListener::class,
        ],
        "dashboard:semester.create.sync" => [
            EducationalTermCreatedSyncListener::class
        ],
        'dashboard:subject.create' => [
            CreateSubjectListener::class,
        ],
        'dashboard:subject.update' => [
            UpdateSubjectListener::class,
        ],
        "dashboard:subject.create.sync" => [
            SubjectCreatedSyncListener::class
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
