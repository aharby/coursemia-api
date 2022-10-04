<?php


namespace App\OurEdu\Instructors\Admin\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class InstructorsExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($instructor): array
    {
        return [
            'id' => (int)$instructor->id,
            'name' => (string)$instructor->user->name,
            'country' => (string)$instructor->user->country ? $instructor->user->country->name : '',
            'subject' => (string)implode(", " , $instructor->user->subjects->pluck('name')->toArray()),
            'instructor_total_hours' => (string)$instructor->instructor_total_hours,
            'rate' => (string) round($instructor->user->ratings->avg('rating'),1)
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('instructors.id'),
            trans('instructors.Instructor Name'),
            trans('instructors.Country'),
            trans('instructors.Subject'),
            trans('instructors.total hours'),
            trans('instructors.Rate'),
        ];
    }

}
