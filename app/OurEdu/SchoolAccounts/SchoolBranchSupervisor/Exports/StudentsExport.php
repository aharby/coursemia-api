<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport extends BaseExport implements WithMapping
{

    /**
     * Mapping Row
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->user->first_name.' '.$row->user->last_name,
            $row->user->username,
            $row->password,
            $row->classroom->name??'',
            $row->gradeClass->title??'',
            $row->educationalSystem->name??'',
        ];
    }
}
