<?php


namespace App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCase;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Instructor\Events\InstructorRejectRequestedSession;
use App\OurEdu\VCRSchedules\Jobs\VCRRequestWaitingDurationValidationJob;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRRequestRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Facades\Log;

class VCRRequestUseCase implements VCRRequestUseCaseInterface
{
    protected $notifierFactory;
    protected $VCRSessionUseCase;
    private $VCRRequestRepository;
    private $VCRSessionRepository;
    private $paymentTransactionRepository;
    private $tokenManager;

    public function __construct(
        VCRRequestRepositoryInterface $VCRRequestRepository,
        VCRSessionRepositoryInterface $VCRSessionRepository,
        NotifierFactoryInterface $notifierFactory,
        VCRSessionUseCaseInterface $VCRSessionUseCase,
        PaymentTransactionRepositoryInterface $paymentTransactionRepository,
        TokenManagerInterface $tokenManager,
        private SubmitTransactionUseCase $submitTransactionUseCase
    ) {
        $this->VCRRequestRepository = $VCRRequestRepository;
        $this->VCRSessionRepository = $VCRSessionRepository;
        $this->notifierFactory = $notifierFactory;
        $this->VCRSessionUseCase = $VCRSessionUseCase;
        $this->tokenManager = $tokenManager;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
    }

    public function request($vcr, $day, $student, $exam = null, string $paymentMethod = PaymentEnums::WALLET)
    {
        $returnArr = $this->validateVcrRequest($vcr, $day, $student , $paymentMethod);
        if (!empty($returnArr)) {
            return $returnArr;
        }
        $data = [
            'student_id' => $student->id,
            'instructor_id' => $vcr->instructor_id,
            'subject_id' => $vcr->subject_id,
            'vcr_schedule_id' => $vcr->id,
            'vcr_day_id' => $day->id??null,
            'price' => $vcr->price,
            'exam_id' => $exam,
            'status' => VCRRequestStatusEnum::WAITING
        ];

        $vcrRequest = $this->VCRRequestRepository->create($data);
        if ($paymentMethod == PaymentEnums::VISA){
            $returnArr['status'] = 200;
            $returnArr['detail'] = trans('vcr.Requested Successfully');
            $returnArr['title'] = trans('vcr.Requested Successfully');
            $returnArr['vcr_request_id'] = $vcrRequest->id;
            return $returnArr;
        }
        $returnCompleteProcess = $this->completeVCRRequestProcess($vcrRequest, $paymentMethod);
        if (!isset($returnCompleteProcess['session_id'])){
            return $returnCompleteProcess;
        }
        $this->withdrawalFromStudentWallet(
            $student,
            $vcrRequest,
            $vcr->price,
        );
        $returnCompleteProcess['status'] = 200;
        $returnCompleteProcess['detail'] = trans('vcr.Requested Successfully');
        $returnCompleteProcess['title'] = trans('vcr.Requested Successfully');
        return $returnCompleteProcess;
    }

    private function validateVcrRequest($vcr, $day, Student $student,$paymentMethod ): array|null
    {
        $returnArr = [];
        if ($student->wallet_amount < $vcr->price && $paymentMethod == PaymentEnums::WALLET) {
            $returnArr['status'] = 422;
            $returnArr['detail'] = trans('vcr.Your wallet does not have enough amount to request a session');
            $returnArr['title'] = 'wallet_amount';
        }
        $studentRequest = $student->vcrRequests()->where(
            [
                'status' => VCRRequestStatusEnum::ACCEPTED,
                'instructor_id' => $vcr->instructor_id,
                'vcr_day_id' => $day
            ]
        )->exists();
        if ($studentRequest) {
            $returnArr['status'] = 422;
            $returnArr['detail'] = trans('vcr.Your request is still being processed');
            $returnArr['title'] = 'wallet_amount';
        }

        $transactionData = [
            'sender_id' => $student->user->id,
            'receiver_id' => $student->user->id,
            'payment_transaction_for' => empty($vcr->exam_id) ? PaymentEnums::VCR_SUBJECT : PaymentEnums::VCR_SPOT,
            'subscribable_id' => null,
        ];
        $studentPendingTransaction= $this->submitTransactionUseCase->validateSubmitTransaction($transactionData);
        if (count($studentPendingTransaction)){
            $returnArr = $studentPendingTransaction;
        }
        return $returnArr;
    }

    public function withdrawalFromStudentWallet(
        Student $student,
        VCRRequest $vcrRequest,
        int $amount
    ) {
        $newWalletAmount = $student->wallet_amount - $amount;
        $transaction = $this->paymentTransactionRepository->create([
            'amount' => $amount,
            'sender_id' => $student->user_id,
            'receiver_id' => $student->user_id,
            'payment_transaction_for' => empty($vcrRequest->exam_id) ? PaymentEnums::VCR_SUBJECT : PaymentEnums::VCR_SPOT,
            'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
            'status' => PaymentEnums::COMPLETED,
            'payment_method' => PaymentEnums::WALLET,
        ]);
        $transaction->detail()->create([
            'subscribable_id' => $vcrRequest->id,
            'subscribable_type' => VCRRequest::class
        ]);
        $student->update(
            [
                'wallet_amount' => $newWalletAmount
            ]
        );
    }

    public function completeVCRRequestProcess(VCRRequest $vcrRequest,$paymentMethod){
        $instructorUser = User::find($vcrRequest->instructor_id);
        $returnAccept = $this->autoAccept($vcrRequest->id, $instructorUser, $vcrRequest->exam_id, $paymentMethod);
        if (!isset($returnAccept['session_id'])){
            return $returnAccept;
        }
        VCRRequestWaitingDurationValidationJob::dispatch($returnAccept['session_id'])
            ->delay(now()->addMinutes(2));
        $returnArr['instructor_url'] = $returnAccept['instructor_url'];
        $returnArr = array_merge(
            $returnArr,
            $returnAccept,
            getStudentSessionUrls($returnAccept['session_id'], 'session', null, $vcrRequest->student->user)
        );
        return $returnArr;
    }

    public function autoAccept($requestId, $instructorUser, $vcrType ,$paymentMethod)
    {
        $returnArr = $this->VCRSessionUseCase->createSession($requestId,$paymentMethod);
        if (!isset($returnArr['session_id'])){
            return $returnArr;
        }
        $this->acceptRequest($requestId);


        // send notification to instructor
        $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $instructorUser);

        //        $url = buildScopeRoute('api.instructor.vcr.acceptVcrRequest', ['requestId' => $requestId]);
        if (!is_null($vcrType)) {
            // go to exam feedback
            $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_VIEW_STUDENT_FEEDBACK, [
                'request_id' => $requestId,
                'portal_url' => env('STUDENT_PORTAL_URL')
            ]);
        } else {
            // go to the vcr session directly
            $url = getDynamicLink(
                DynamicLinksEnum::VCR_INFO,
                [
                    'session_id' => $returnArr['session_id'],
                    'portal_url' => env('STUDENT_PORTAL_URL')
                ]
            );
        }
        $notificationData = [
            'users' => collect([$instructorUser]),
            'mail' => [
                'user_type' => UserEnums::INSTRUCTOR_TYPE,
                'data' => [
                    'url' => $url,
                    'body' => 'notification.ask for vcr session',
                    'lang' => $instructorUser->language
                ],
                'subject' => trans('emails.new vcr request', [], $instructorUser->language),
                'view' => 'acceptVcrRequest'
            ],
            'fcm' => [
                'data' => [
                    'title' => buildTranslationKey('notification.new vcr request'),
                    'body' => buildTranslationKey('notification.ask for vcr session'),
                    'data' => [
                        'session_id' => $returnArr['session_id'],
                        'token' => $token,
                        'screen_type' => NotificationEnum::INSTRUCTOR_VCR_SESSION,
                    ],
                    'url' => $url,
                ]
            ]
        ];
        $this->notifierFactory->send($notificationData);

        return [
            'session_id' => $returnArr['session_id'],
            'instructor_url' => $url
        ];
    }

    public function acceptRequest($requestId)
    {
        $request = $this->VCRRequestRepository->findOrFail($requestId);
        $request->accepted_at = now();
        $request->status = VCRRequestStatusEnum::ACCEPTED;
        $request->save();
        return true;
    }

    public function validateRequestWaitingDuration(VCRSession $vcrSession)
    {
//        Log::error("in vcr request use case to validate waiting duration");
        // update vcrRequest status
        $this->VCRRequestRepository->update($vcrSession->vcrRequest->id, ['status' => VCRRequestStatusEnum::REJECTED]);
        // update vcrSession status
        $this->VCRSessionRepository->update($vcrSession->id, ['status' => VCRSessionsStatusEnum::REJECTED]);
        //notify student
        $this->notifyStudentWithWaitingDurationPassed($vcrSession);
        //notify instructor
        $this->notifyInstructordWithWaitingDurationPassed($vcrSession);
        //broadcast event that instructor rejected the request
        event(new InstructorRejectRequestedSession($vcrSession));
        // refund request price to student wallet
        $this->refundToStudentWallet(
            $vcrSession->student,
            $vcrSession->vcrRequest,
            $vcrSession->vcrRequest->price,
        );
    }

    private function notifyStudentWithWaitingDurationPassed(VCRSession $VCRSession)
    {
        $url =  "";
        $student = User::find($VCRSession->student->user_id);
        $notificationData = [
            'users' => $student,
            'fcm' => [
                'data' => [
                    'title' => buildTranslationKey(
                        "vcr.the instructor didn't accept your request,the session price has been refunded"
                    ),
                    'body' => buildTranslationKey(
                        "vcr.the instructor didn't accept your request,the session price has been refunded"
                    ),
                    'data' => [],
                    'url' => $url,

                ]
            ]
        ];
        $this->notifierFactory->send($notificationData);
    }

    private function notifyInstructordWithWaitingDurationPassed(VCRSession $VCRSession)
    {
        $instructor = User::find($VCRSession->vcrRequest->instructor_id);
        $url = "";
        $notificationData = [
            'users' => $instructor,
            'fcm' => [
                'data' => [
                    'title' => buildTranslationKey(
                        "vcr.student requested a session but waiting duration time has passed",
                        [
                            'student_name' => $VCRSession->student->user->name
                        ]
                    ),
                    'body' => buildTranslationKey(
                        "vcr.student requested a session but waiting duration time has passed",
                        [
                            'student_name' => $VCRSession->student->user->name
                        ]
                    ),
                    'data' => [],
                    'url' => $url,
                ]
            ]
        ];
        $this->notifierFactory->send($notificationData);
    }

    public function refundToStudentWallet(
        Student $student,
        VCRRequest $vcrRequest,
        int $amount,
    ) {
        $parentPaymentTransactionId = null;
            $parentPaymentTransaction = $this->getPaymentTransaction($student->user_id, $vcrRequest->id);
            if (!empty($parentPaymentTransaction)){
                $parentPaymentTransactionId = $parentPaymentTransaction->id;
        }
        $newWalletAmount = $student->wallet_amount + $amount ;
        $transaction = $this->paymentTransactionRepository->create([
            'amount' => $amount,
            'sender_id' => $student->user_id,
            'receiver_id' => $student->user_id,
            'payment_transaction_for' => PaymentEnums::ADD_MONEY_WALLET ,
            'payment_transaction_type' => TransactionTypesEnums::REFUND,
            'status' => PaymentEnums::COMPLETED,
            'payment_method' => PaymentEnums::WALLET,
            'parent_payment_transaction_id' => $parentPaymentTransactionId,
        ]);
        $transaction->detail()->create([
            'subscribable_id' => $vcrRequest->id,
            'subscribable_type' => VCRRequest::class
        ]);
        $student->update(
            [
                'wallet_amount' => $newWalletAmount
            ]
        );
    }

    private function getPaymentTransaction(int $studentId,int $vcrRequestId)
    {
        return PaymentTransaction::query()
            ->where('receiver_id', $studentId)
            ->whereHas('detail', function ($query) use ($vcrRequestId) {
                $query->where('subscribable_id', $vcrRequestId)
                    ->where('subscribable_type', VCRRequest::class);
            })
            ->first();
    }
}
