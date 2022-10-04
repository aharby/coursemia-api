<?php

namespace App\OurEdu\Subjects\Admin\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubjectsExport extends BaseExport implements WithMapping
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
            $row->exams->where('type', \App\OurEdu\Exams\Enums\ExamTypes::PRACTICE)->count(),
            $row->exams->where('type', \App\OurEdu\Exams\Enums\ExamTypes::EXAM)->count(),
            round($row->exams->avg->result, 2),
            $row->created_at,
        ];
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('subject.name'),
            trans('subjects.Practices Number'),
            trans('subjects.Number of exams'),
            trans('subjects.average results'),
            trans('subjects.created on'),
        ];
    }
}
