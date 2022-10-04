<?php

namespace App\OurEdu\Payments\Parent\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Gateways\PaymentGatewayRegistry;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Parent\Exports\ListExpensesExport;
use App\OurEdu\Payments\Parent\Exports\ListPaymentTransactionExport;
use App\OurEdu\Payments\Parent\Requests\AddMoneyToWalletRequest;
use App\OurEdu\Payments\Parent\Transformer\TransactionsByPeriodTransformer;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\Transformers\ChildrenOwnTransactionTransformer;
use App\OurEdu\Payments\Transformers\ExpensesTransactionTransformer;
use App\OurEdu\Payments\Transformers\PaymentTransactionTransformer;
use App\OurEdu\Payments\Transformers\SpendingTransactionTransformer;
use App\OurEdu\Payments\Transformers\TotalStudentsTransactionTransformer;
use App\OurEdu\Payments\Transformers\TransactionDetailsTransformer;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCaseInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Throwable;

class PaymentsApiController extends BaseApiController
{
    protected $filters;
    private $user;

    public function __construct(
        private ParserInterface $parserInterface,
        private SubmitTransactionUseCaseInterface $submitTransactionUseCase,
        protected PaymentTransactionRepositoryInterface $paymentTransactionRepository,
        protected PaymentTransactionRepositoryInterface $paymentTransaction,
        protected UserRepositoryInterface $userRepository,
        private StudentRepositoryInterface $studentRepository,
        private PaymentGatewayRegistry $gatewayRegistry
    ) {
        $this->user = Auth::guard('api')->user();
        $this->middleware('type:parent|student')
            ->only(['submitTransaction']);
    }

    public function index()
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        $transactions = $this->paymentTransactionRepository->sentPaymentTransactions($this->user);
        return $this->transformDataModInclude(
            $transactions,
            'actions',
            new PaymentTransactionTransformer(),
            ResourceTypesEnums::PAYMENT_TRANSACTION,
            $meta
        );
    }

    protected function setFilters()
    {
        $children = $this->user->students->pluck('name', 'id')->toArray();

        $this->filters[] = [
            'name' => 'user_id',
            'type' => 'select',
            'data' => $children,
            'trans' => false,
            'value' => request()->get('user_id'),
        ];

        $this->filters[] = [
            'name' => 'date_from',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('date_from'),
        ];

        $this->filters[] = [
            'name' => 'date_to',
            'type' => 'date',
            'data' => '',
            'trans' => false,
            'value' => request()->get('date_to'),
        ];
    }

    public function expenses()
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransaction->getChildrenExpensesTransactions($studentUsersId);

        return $this->transformDataModInclude(
            $transactions,
            'user',
            new ExpensesTransactionTransformer(),
            ResourceTypesEnums::TRANSACTION,
            $meta
        );
    }
    /**
     * @throws Exception
     */
    /**
     * @throws OurEduErrorException
     */
    public function submitTransaction(AddMoneyToWalletRequest $request, $student_id)
    {
        try {
            $data = $request->getContent();
            $data = $this->parserInterface->deserialize($data);
            $data = $data->getData();

            $parent = $this->user;
            $student = $this->studentRepository->findOrFail($student_id);
            $merchantReference = Uuid::uuid4()->toString();
            $response['web_view_url'] = buildScopeRoute(
                'payment.frame',
                ['merchant_reference' => $merchantReference]
            );
            $data->merchant_reference = $merchantReference;
            $data->customer_email = $request->user()->email;
            $data->customer_ip = $request->ip();

            $useCase = $this->submitTransactionUseCase->addMoney($parent, $student, $data);

            if ($useCase['status'] != 200 ){
                return formatErrorValidation($useCase);
            }
            $meta = [
                'message' => trans('api.Transaction done')
            ];
            return $this->transformDataModInclude(
                $useCase['transaction'],
                'receiver.student',
                new PaymentTransactionTransformer($response),
                ResourceTypesEnums::PAYMENT_TRANSACTION,
                $meta
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function paymentFrame()
    {
        $frameData = $this->gatewayRegistry->get(config('app.payment_gateway'))->frameData(request()->all());
        $transaction = PaymentTransaction::where('merchant_reference', $frameData['merchant_reference'])
            ->where('status', PaymentEnums::PENDING)->first();
        if (!$transaction) {
            return redirect()->away($this->dynamicLink());
        }

        return view('payment-frame', $frameData);
    }

    public function expensesExport()
    {
        $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransaction->getChildrenExpensesTransactions($studentUsersId, false);

        return Excel::download(new ListExpensesExport($transactions), "List-expenses.xls");
    }

    public function getTransactionsByPeriod(string $period)
    {
        $transactions = $this->paymentTransactionRepository->paymentTransactionsCountByPeriod($this->user, $period);

        $data['data'] = $transactions;

        return $this->transformDataModInclude(
            $data,
            '',
            new TransactionsByPeriodTransformer(),
            ResourceTypesEnums::TRANSACTION_REPORT
        );
    }

    public function transactionFeedback(Request $request)
    {
        Log::error('PayFort Feedback:', $request->all());

        return response()->setStatusCode(204);
    }

    public function handleTokenResponse(Request $request)
    {
        $input = $request->all();

        Log::error('tokenization response received from payfort:' . PHP_EOL . print_r($input, 1));
        try {
            $data = $this->submitTransactionUseCase->preparePayment($input);
            if (isset($data['3ds_url'])) {
                return redirect()->away($data['3ds_url']);
            }
            $transaction = $this->submitTransactionUseCase->payfortData($data);
            return redirect()->away($this->dynamicLink($transaction));
        } catch (Exception $exception) {
            return redirect()->away($this->dynamicLink());
        }
    }

    private function dynamicLink($transaction = null)
    {
        switch ($transaction?->payment_transaction_for){
            case PaymentEnums::VCR_SUBJECT:
                $payForId =$transaction?->detail?->subscribable->subject_id;
                $sessionId =$transaction?->detail?->subscribable?->vcrSession?->id;
                break;
            case PaymentEnums::VCR_SPOT:
                $payForId =$transaction?->detail?->subscribable->exam_id;
                $sessionId =$transaction?->detail?->subscribable?->vcrSession?->id;
                break;
            default:
                $payForId =$transaction?->detail?->subscribable_id;
                $sessionId =null;
                break;
        }
        return getDynamicLink(
            DynamicLinksEnum::STUDENT_DYNAMIC_URL,
            [
                'link_name' => 'payment',
                'firebase_url' => env('FIREBASE_URL_PREFIX'),
                'portal_url' => env('STUDENT_PORTAL_URL'),
                'query_param' => 'transaction_status%3D' . ($transaction?->status ?? 'Failed') .
                    '%26target_screen%3D' . $transaction?->payment_transaction_for .
                    '%26pay_for_id%3D' . $payForId . '%26session_id%3D' . $sessionId,
                'android_apn' => env('ANDROID_APN', 'com.ouredu.students')
            ]
        );
    }

    public function handleResponse(Request $request)
    {
        $input = $request->all();

        try {
            Log::channel('slack')->error(
                'purchase response received from '
                . config('app.payment_gateway'),
                $input
            );
            DB::beginTransaction();
            $transaction = $this->gatewayRegistry->get(config('app.payment_gateway'))->response($input);
            if (
                $transaction->status == PaymentEnums::COMPLETED &&
                $transaction->payment_transaction_for != PaymentEnums::ADD_MONEY_WALLET
            ) {
                $this->submitTransactionUseCase->payFor($transaction);
            }
            DB::commit();
            return redirect()->away($this->dynamicLink($transaction));
        } catch (Exception $exception) {
            DB::rollBack();
            Log::channel('slack')->error('purchace error', $input);
            Log::channel('slack')->error(
                'purchace error',
                [
                    'message' => $exception->getMessage(),
                    'line' => $exception->getLine(),
                    'file' => $exception->getFile(),
                    'trace' => $exception->getTraceAsString()
                ]
            );
            return redirect()->away($this->dynamicLink());
        }
    }

    public function spending()
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        if (request()->has('user_id')){
            $studentUsersId = [\request()->get('user_id')];
        }else{
            $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        }
        $userTransactions = $this->paymentTransaction->getUsersTransactionsSpending($studentUsersId);
        return $this->transformDataModInclude(
            $userTransactions,
            '',
            new SpendingTransactionTransformer(),
            ResourceTypesEnums::TRANSACTION,
            $meta
        );
    }

//    public function getTransactionsByPeriod(string $period)

    public function exportPaymenent()
    {
        $transactions = $this->paymentTransactionRepository->sentPaymentTransactions($this->user, false);

        return Excel::download(new ListPaymentTransactionExport($transactions), "List-paymentTransActions.xls");
    }

    public function sharePayments($parentId)
    {
        $user = User::findOrFail($parentId);
        $transatctions = $this->paymentTransactionRepository->sentPaymentTransactions($user, false);

        return Excel::download(new ListPaymentTransactionExport($transatctions), "List-paymentTransActionsShared.xls");
    }

    public function shareExpenses($parentId)
    {
        $childIdPram = \request()->input('child_id');
        $user = User::findOrFail($parentId);

        if ($childIdPram) {
            $studentUsersId = $user->students()->where('id', '=', $childIdPram)->pluck('users.id')->toArray();
            $transactions = $this->paymentTransaction->getChildrenExpensesTransactions($studentUsersId, false);

            return Excel::download(new ListExpensesExport($transactions), "List-expensesTransactionsShared.xls");
        }
        $studentUsersId = $user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransaction->getChildrenExpensesTransactions($studentUsersId, false);

        return Excel::download(new ListExpensesExport($transactions), "List-expensesTransactionsShared.xls");
    }

    public function details(PaymentTransaction $transaction)
    {
        return $this->transformDataModInclude(
            $transaction,
            '',
            new TransactionDetailsTransformer(),
            ResourceTypesEnums::TRANSACTION,
        );
    }


    public function totalSpending()
    {
        $studentUsersIds = $this->user->students()->pluck('users.id')->toArray();
        $userTransactions = $this->paymentTransaction->getTotalUsersTransactionsSpending($studentUsersIds);
        $userTransactions->currency_code = $this->user->country?->currency_code;
        return $this->transformDataModInclude(
            $userTransactions,
            '',
            new TotalStudentsTransactionTransformer(),
            ResourceTypesEnums::TRANSACTION
        );
    }

    public function childrenPurchases()
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransactionRepository->childrenOwnTransactions($studentUsersId);

        return $this->transformDataModInclude(
            $transactions,
            'user',
            new ChildrenOwnTransactionTransformer(),
            ResourceTypesEnums::TRANSACTION,
            $meta
        );
    }

    public function listChildrenReceivedTransactions()
    {
        $this->setFilters();

        $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransactionRepository->studentReceivedPaymentTransactions($studentUsersId);

        $meta = ['filters' => formatFiltersForApi($this->filters)];

        return $this->transformDataModInclude(
            $transactions,
            'actions',
            new PaymentTransactionTransformer(),
            ResourceTypesEnums::PAYMENT_TRANSACTION,
            $meta
        );
    }
    public function exportChildrenReceivedTransactions()
    {
        $studentUsersId = $this->user->students()->pluck('users.id')->toArray();
        $transactions = $this->paymentTransactionRepository->studentReceivedPaymentTransactions($studentUsersId, false);
        return Excel::download(new ListPaymentTransactionExport($transactions), "List-Children-Payment-Transactions.xls");
    }
}
