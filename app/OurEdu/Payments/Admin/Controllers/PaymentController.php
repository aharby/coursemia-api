<?php

namespace App\OurEdu\Payments\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Payments\Admin\Exports\FailedTransactionsExport;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends BaseController
{
    public function __construct(private PaymentTransactionRepositoryInterface $paymentTransactionRepository)
    {
        $this->module = 'payments';
        $this->parent = ParentEnum::ADMIN;
    }

    public function getFailedTransactions()
    {
        $filters = request()->all();
        if(!isset($filters['status'])){
            $filters['status'] = PaymentEnums::FAILED;
        }
        $transactions = $this->paymentTransactionRepository->all($filters, true);

        $data['rows'] = $transactions->withQueryString();
        $data['page_title'] = trans('payment.failed_transactions');

        return view($this->parent . '.' . $this->module . '.failed_transactions',$data);
    }

    public function exportFailedTransactions(Request $request)
    {
        $filters = request()->all();
        if(!isset($filters['status'])){
             $filters['status'] = PaymentEnums::FAILED;
        }
        $data = $this->paymentTransactionRepository->all($filters, false);

        return Excel::download(new FailedTransactionsExport($data), trans("payment.failed_transactions").".xls");
    }

}
