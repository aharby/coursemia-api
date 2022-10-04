<?php

namespace App\OurEdu\Subjects\Admin\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuccessRateExport extends BaseExport implements WithMapping
{

    /**
     * Mapping Row
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            number_format($row->exams_count) ?? 0,
            number_format($row->exams->average('result'), 2),
            $row->educationalSystem->name,
            $row->country->name,
            $row->gradeClass->title
        ];
    }
}
