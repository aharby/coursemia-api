<?php


namespace App\OurEdu\PaymentReport\Admin\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
class PaymentReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $product = '';
        if($row->detail && $row->detail->subscribable){
            if($row->payment_transaction_for == \App\OurEdu\Payments\Enums\PaymentEnums::VCR_SPOT){
                $product = $row->detail->subscribable->instructor->name;
            }else{
                $product = $row->detail->subscribable->name;
            }
        }
        return [
            'transaction_id' =>$row->id,
            'student_name'=>$row->receiver->name,
            'transaction_date'=>$row->created_at->format('Y-m-d H:i:s'),
            'product_type'=>PaymentEnums::getProducts()[$row->payment_transaction_for] ?? '',
            'product' => $product,
            'amount'=>$row->amount,
            // 'transaction_type'=>TransactionTypesEnums::getTransactionTypes()[$row->payment_transaction_type],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('payments.id'),
            trans('payments.student_name'),
            trans('payments.date_time'),
            trans('payments.product_type'),
            trans('payments.product'),
            trans('payments.amount'),
            // trans('payments.transaction_type'),
        ];
    }

}
