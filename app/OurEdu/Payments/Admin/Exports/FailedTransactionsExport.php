<?php

namespace App\OurEdu\Payments\Admin\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Payments\Enums\UrWayErrorCodesDescEnum;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class FailedTransactionsExport extends BaseExport implements WithMapping, ShouldAutoSize
{
    private $index = 0;

    public function map($row): array
    {
        return [
            $this->index++,
            $row->sender?->name,
            $row->amount,
            $row->methodable ? UrWayErrorCodesDescEnum::translatableKeys(
                $row->methodable->response_code
            ) : "---",
            $row->detail ? resolveSubscribableName($row)['type'] : trans('payment.money added to wallet'),
            $row->detail ? resolveSubscribableName($row)['name'] : $row->receiver?->name,
            Carbon::parse($row->created_at)->toDateString()
        ];
    }

    public function headings(): array
    {
        return [
            trans('payment.id'),
            trans('payment.Name'),
            trans('payment.amount'),
            trans('payment.reason for failure'),
            trans('payment.Product Type'),
            trans('payment.Product'),
            trans('payment.date'),
        ];
    }

}
