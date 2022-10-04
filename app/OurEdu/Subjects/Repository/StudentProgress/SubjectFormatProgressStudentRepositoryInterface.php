<?php

declare(strict_types=1);

namespace App\OurEdu\Subjects\Repository\StudentProgress;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubjectFormatProgressStudentRepositoryInterface
{
    public function incrementPoints($data,$points);

}
