<?php

namespace App\OurEdu\Payments\Parent\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListExpensesExport extends BaseExport implements WithMapping, ShouldAutoSize
{
    private $index  = 1;

    public function map($transaction): array
    {
        $subscribableName = $this->resolveSubscribableName($transaction);
        $data = [
            'id' => $this->index++,
            'student_name' => (string)$transaction->receiver->name ,
            'date_time' => $transaction->created_at->format('Y-m-d H:i:s'),
            'amount' => $transaction->amount . " " . trans("app.SAR"),
            'description' => trans('app.spent') . " "
                . " " . trans('app.on'). " " . trans('app.Subscribe') . " "
                . $subscribableName
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            trans('payments.id'),
            trans('payments.student_name'),
            trans('payments.date_time'),
            trans('payments.amount'),
            trans('payments.description')
        ];
    }

    private function resolveSubscribableName($transaction): string
    {
        $subscribable = $transaction->detail->subscribable;
        $subscribableType = $transaction->detail->subscribable_type;
        $type = "";
        $name = "";

        switch ($subscribableType) {
            case Subject::class:
                $type = trans('app.Subject');
                $name =  $subscribable->name;
                break;
            case Course::class:
                $type = trans('app.'. CourseEnums::getFormattedTypes($subscribable->type));
                $name =  $subscribable->name;
                break;
            case Package::class:
                $type = trans('app.SubjectPackage');
                $name =  $subscribable->name;
                break;
        }

        return $type . " " . $name;
    }
}
