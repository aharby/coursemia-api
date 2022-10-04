<?php


namespace App\OurEdu\Payments\Parent\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ListPaymentTransactionExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    private $current_row =1;
    
    public function map($transaction): array
    {
        $currencyCode = $transaction->receiver->student->educationalSystem->country->currency ?? '';
        return [
            'id' => $this->current_row++,
            'date' => (string)$transaction->created_at,
            'child' => (string)$transaction->receiver ? $transaction->receiver->name : '',
            'father' => (string)$transaction->sender ? $transaction->sender->name : '',
            'amount' => (float) $transaction->amount . " " . $currencyCode,
                ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            trans('payment.date'),
            trans('payment.child Name'),
            trans('payment.father Name'),
            trans('payment.amount'),
        ];
    }

}
