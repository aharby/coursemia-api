<?php

namespace App\OurEdu\Payments\UseCases;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\CourseSubscribeUseCaseInterface;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Jobs\NotifyParentAboutChildPayment;
use App\OurEdu\Payments\Models\PayfortCheckout;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Models\UrwayCheckout;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Subjects\UseCases\SubscribeUseCase\SubscribeUseCaseInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCase;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCaseInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MoeenBasra\Payfort\PayfortFacade;
use Ramsey\Uuid\Uuid;

class SubmitTransactionUseCase implements SubmitTransactionUseCaseInterface
{
    protected $user;
    protected UserRepositoryInterface $userRepository;
    protected PaymentTransactionRepositoryInterface $transactionRepository;
    private $intent;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PaymentTransactionRepositoryInterface $transactionRepository
    ) {
        $this->user = Auth::guard('api')->user();
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
        $this->intent = PayfortFacade::configure(config('payfort'));
    }

    /**
     * Add money to student wallet
     * @param object $parent
     * @param $student
     * @param $data
     * @return void
     * @throws ErrorResponseException
     */
    public function addMoney($parent, $student, $data):array
    {
        $transactionData = [
            'sender_id' => $parent->id,
            'receiver_id' => $student->user->id,
            'payment_transaction_for' =>$data->payment_for ?? PaymentEnums::ADD_MONEY_WALLET,
            'subscribable_id' => $data->payment_for_id,

        ];
        $errors = $this->validateSubmitTransaction($transactionData);
        if (count($errors)) {
            return $errors;
        }
        $transaction = $this->transactionRepository->create([
            'amount' => $data->amount,
            'sender_id' => $parent->id,
            'receiver_id' => $student->user->id,
            'merchant_reference' => $data->merchant_reference,
            'customer_email' => $data->customer_email,
            'customer_ip' => $data->customer_ip,
            'payment_transaction_for' => $data->payment_for ?? PaymentEnums::ADD_MONEY_WALLET,
            'payment_transaction_type' => TransactionTypesEnums::DEPOSIT,
            'payment_method' => PaymentEnums::VISA,
        ]);

        if (!empty($data->payment_for) && $data->payment_for != PaymentEnums::ADD_MONEY_WALLET) {
            $subscribable = PaymentEnums::PRODUCTS_MAP[$data->payment_for] ?? null;
            $transaction->detail()->create([
                'subscribable_id' => $data->payment_for_id,
                'subscribable_type' => $subscribable
            ]);
        }

        return [
            'status' => 200,
            'transaction' => $transaction
        ];
    }

    public function prepareToken($merchantReference = null)
    {
        $merchantReference = $merchantReference ?? substr(md5(mt_rand()), 0, 30);
        $response = $this->intent
            ->prepareTokenizationData([
                'token_name' => $merchantReference,
                'merchant_reference' => $merchantReference,
                'return_url' => config('app.url') . '/payfort/tokenization',
            ]);

        $response['url'] = $this->intent->getClient()->getTokenizationUrl();
        $response['merchant_reference'] = $merchantReference;

        return $response;
    }

    public function preparePayment($data)
    {
        $this->intent->verifyResponse($data);

        $transaction = $this->transactionRepository->findBy('merchant_reference', $data['merchant_reference']);

        // prepare payment data
        return $this->intent
            ->authorization([
                'command' => 'PURCHASE',
                'merchant_reference' => $data['merchant_reference'],
                'token_name' => $data['token_name'],
                'amount' => $this->intent->convertAmountToPayfortFormat($transaction->amount),
                'customer_email' => $transaction->customer_email,
                'customer_ip' => $transaction->customer_ip,
                'return_url' => config('app.url') . '/payment/response',
            ]);
    }

    public function payfortData($data)
    {
        $this->intent->verifyResponse($data);
        $paymentStatus = $data['status'];
        $payfortData = PayfortCheckout::create($data);

        $transaction = $this->transactionRepository
            ->updateByOrderNumber(
                $data['merchant_reference'],
                [
                    'status' => $paymentStatus == 14 ? 'Completed' : 'Failed',
                    'methodable_id' => $payfortData->id,
                    'methodable_type' => $payfortData::class
                ]
            );

        if ($paymentStatus == 14 && $transaction->payment_transaction_for == PaymentEnums::ADD_MONEY_WALLET) {
            $this->userRepository->incrementStudentBalance($transaction->receiver->student, $transaction->amount);
        }
        return $transaction;
    }

    public function urWayData($data)
    {
        $paymentStatus = $data['responseCode'];

        $requesetData = [
            'udf1' => $data['udf1'] ?? null,
            'response_code' => $data['responseCode'] ?? null,
            'response_message' => $data['result'] ?? null,
            'amount' => $data['amount'],
            'card_number' => $data['tranid'] ?? null,
            'payment_option' => $data['cardBrand'] ?? null,
            'raw_response' => json_encode($data)
        ];
        $urwayData = UrwayCheckout::create($requesetData);

        $transaction = $this->transactionRepository
            ->updateByOrderNumber(
                $data['udf1'],
                [
                    'status' => $paymentStatus == 000 ? 'Completed' : 'Failed',
                    'methodable_id' => $urwayData->id,
                    'methodable_type' => $urwayData::class,
                    'transaction_id' => $data['tranid'],
                ]
            );

        if ($paymentStatus == 000 && $transaction->payment_transaction_for == PaymentEnums::ADD_MONEY_WALLET) {
            $this->userRepository->incrementStudentBalance($transaction->receiver->student, $transaction->amount);
        }
        return $transaction;
    }

    public function payFor($transaction)
    {
        if ($transaction->payment_transaction_for == PaymentEnums::COURSE) {
            $courseId = $transaction->detail->subscribable_id;
            app(CourseSubscribeUseCaseInterface::class)
                ->subscribeCourse($courseId, $transaction->receiver->student->id, false, false, PaymentEnums::VISA);
        } elseif ($transaction->payment_transaction_for == PaymentEnums::SUBJECT) {
            $subjectId = $transaction->detail->subscribable_id;
            app(SubscribeUseCaseInterface::class)
                ->subscribeSubject($subjectId, $transaction->receiver->student->id, PaymentEnums::VISA);
        } elseif (in_array($transaction->payment_transaction_for, [PaymentEnums::VCR_SUBJECT,PaymentEnums::VCR_SPOT])) {
            $VCRequest = $transaction->detail->subscribable;
            app(VCRRequestUseCase::class)
                ->completeVCRRequestProcess($VCRequest, PaymentEnums::VISA);
        }
        NotifyParentAboutChildPayment::dispatch($transaction);
    }

    public function validateSubmitTransaction($data = []): array
    {
        $errors = [];
        $paymentTransactionQuery = PaymentTransaction::query();

        $pendingPaymentTransaction = $paymentTransactionQuery
            ->where([
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'status' => PaymentEnums::PENDING
            ])
           ->get();
        
        if (count($pendingPaymentTransaction)) {
            $errors = [
                'status' => 422,
                'detail' => trans('payment.please waite , there is a pending process'),
                'title' => 'please waite , there is a pending process',
            ];
        }
       

        if (in_array($data['payment_transaction_for'], [PaymentEnums::COURSE,PaymentEnums::SUBJECT])){
           
            $isPaid =  PaymentTransaction::query()
            ->where([
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'status' => PaymentEnums::COMPLETED,
                'payment_transaction_for' => $data['payment_transaction_for']
            ])->whereHas('detail', function($q)use ($data){
                    $q->where('subscribable_id', $data['subscribable_id']);
                 })
            ->exists();
           
            
            if (isset($isPaid) && $isPaid) {
                $errors = [
                    'status' => 422,
                    'detail' => trans('course.Already subscribed'),
                    'title' => 'Already subscribed',
                ];
            }
        }
       
        return $errors;
    }
}
