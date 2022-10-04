<?php

namespace App\OurEdu\PaymentReport\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\PaymentReport\Admin\Exports\PaymentReportExport;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\PaymentMethodsEnum;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\DB;
use Throwable;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class PaymentReportController extends BaseController
{

    private $module;
    private $paymentTransactionRepository;
    private $title;
    private $parent;
    public function __construct(PaymentTransactionRepositoryInterface $paymentTransactionRepository)
    {
        $this->module = 'payment_report';
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->title = trans('reports.Report');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $transactions = $this->paymentTransactionRepository->paymentTransactionsReport();
        $clonedQuery = clone $transactions;
        $data['total_students'] = count(array_unique($clonedQuery->pluck('receiver_id')->toarray()));
        $data['total_spent'] = $this->paymentTransactionRepository->getTotalWithdraw(clone $transactions);
        $data['rows'] = $transactions->paginate()->withQueryString();
        $data['total_deposit'] = $this->paymentTransactionRepository->getTotalDeposit();
        $data['total_transactions'] = $data['rows']->total();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        $data['product_types'] = PaymentEnums::getProducts();
        $data['payment_methods'] = PaymentMethodsEnum::getPaymentMethods();
        $data['transaction_types'] = TransactionTypesEnums::getTransactionTypes();
        return view($this->parent . '.' . $this->module . '.index',$data);
    }

    public function getProducts()
    {
        $productType = request()->get('product_type');
        $data = [];
        switch($productType){
            case PaymentEnums::COURSE:
                if(request()->has('product_id') && request('product_id') != "" ) {
                    $data = Course::where('is_top_qudrat',1)
                    ->where('instructor_id',request('product_id'))
                    ->withTrashed()
                    ->select('name','id')->get()->pluck('name','id')->toArray();
                    return response()->json([
                        "status" => 200,
                        "courses" => $data
                    ]);
                }else{
                    $data = User::where('type',UserEnums::INSTRUCTOR_TYPE)
                        ->withTrashed()
                        ->select(DB::raw("CONCAT(users.first_name,' ',users.last_name) AS full_name"),'id')
                        ->get()->pluck('full_name','id')->toArray();
                }
                break;
            case PaymentEnums::VCR_SPOT:
                $data = User::where('type',UserEnums::INSTRUCTOR_TYPE)
                    ->withTrashed()
                    ->select(DB::raw("CONCAT(users.first_name,' ',users.last_name) AS full_name"),'id')->get()->pluck('full_name','id')->toArray();
                break;
            case PaymentEnums::SUBJECT:
                $data = Subject::where('is_top_qudrat',1)
                    ->withTrashed()
                    ->select('name','id')->get()->pluck('name','id')->toArray();
                break;
            default:
                break;
        }

        return response()->json([
            "status" => 200,
            "products" => $data
        ]);
    }

    public function indexExport(Request $request)
    {
        $data = $this->paymentTransactionRepository->paymentTransactionsReport()->get();

        return Excel::download(new PaymentReportExport($data), trans("navigation.payment_report").".xls");
    }

    public function getDetails($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] =  trans('app.Details');
        $data['breadcrumb'] = [$this->title => route('admin.paymentReport.get.index')];
        $data['row'] = $this->paymentTransactionRepository->findByWith('id',$id,['receiver']);
        $data['currencyCode'] = $data['row']->receiver->student->educationalSystem->country->currency ?? '';

        return view($this->parent . '.' . $this->module . '.details', $data);
    }
}
