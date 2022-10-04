<?php


namespace App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolInstructorsExport implements  ShouldAutoSize, FromView
{
    /**
     * @var array
     */
    private $instructors;

    /**
     * SchoolInstructorsExport constructor.
     * @param array $instructors
     */
    public function __construct($instructors)
    {
        $this->instructors = $instructors;
    }

    public function view(): View
    {
        return view('exports.exportInstructor',['rows'=>$this->instructors]);
    }
}
