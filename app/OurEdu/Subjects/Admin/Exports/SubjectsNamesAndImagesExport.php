<?php

namespace App\OurEdu\Subjects\Admin\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubjectsNamesAndImagesExport extends BaseExport implements WithMapping
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
            viewImage($row->image, 'large')

        ];
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('subject.name'),
            trans('subject.image url'),

        ];
    }
}
