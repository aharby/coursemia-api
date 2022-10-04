<?php


namespace App\OurEdu\SchoolAccounts\Reports\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClassroomAttendanceExport implements WithColumnFormatting, FromArray, WithHeadings
{
    /**
     * @var array
     */
    private $classrooms;

    /**
     * ClassroomAttendanceExport constructor.
     * @param array $classrooms
     */
    public function __construct(array $classrooms)
    {
        $this->classrooms = $classrooms;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        foreach ($this->classrooms as $classroom) {
            foreach ($classroom->students as $student) {
                $rows[] = [
                    $student->user->name ?? '',
                    $student->user->username ?? '',
                    $classroom->name ?? '',
                    (int)$student->user->v_c_r_sessions_presence_count ?? 0,
                    ($classroom->sessions_count ?? 0) - ($student->user->v_c_r_sessions_presence_count ?? 0),
                ];
            }
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('students.student name'),
            trans('students.ID'),
            trans('students.classroom'),
            trans('reports.attends'),
            trans('reports.absents'),
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
