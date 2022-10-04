<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ParentsExport implements FromView
{

    public function __construct($parents)
    {
        $this->parents = $parents;
    }


    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('school_supervisor.students.exportParent', [
            'rows' => $this->parents
        ]);
    }
}
