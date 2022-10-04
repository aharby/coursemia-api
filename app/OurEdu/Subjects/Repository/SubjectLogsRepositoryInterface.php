<?php

namespace App\OurEdu\Subjects\Repository;

interface SubjectLogsRepositoryInterface
{
    public function getSubjectLogs($subjectId);
    public function findOrFail($logId);
}
