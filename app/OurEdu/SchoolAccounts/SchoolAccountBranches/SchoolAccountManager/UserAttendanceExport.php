<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class UserAttendanceExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    /**
     * @var array
     */
    private $users;

    /**
     * UserAttendanceExport constructor.
     * @param array $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        foreach ($this->users as $user) {

            $row = [
                $user->name,
                $user->username,
            ];

            if (($user->branches->count()>0)) {
                $row[] = implode(',',$user->branches->pluck('name')->toArray());
            } else {
                $row[] = $user->branch->name ?? $user->schoolAccountBranchType->name ?? '';
            }

            $row[] = trans('app.'.$user->type);
            $row[] = $user->v_c_r_sessions_presence_count;

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('school-account-branches.User Name'),
            trans('school-account-branches.User ID'),
            trans('school-account-branches.Branch'),
            trans('school-account-branches.User Type'),
            trans('school-account-branches.attends'),
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle("A1:Z1")->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }];
    }
}
