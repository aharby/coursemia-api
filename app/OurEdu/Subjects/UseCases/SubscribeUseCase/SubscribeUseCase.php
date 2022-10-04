<?php

namespace App\OurEdu\Subjects\UseCases\SubscribeUseCase;

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCase;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;

class SubscribeUseCase implements SubscribeUseCaseInterface
{
    public function __construct(
        private StudentRepositoryInterface $studentRepo,
        private SubjectRepositoryInterface $subjectRepo,
        private PaymentTransactionRepositoryInterface $paymentTransactionRepository,
        private SubmitTransactionUseCase $submitTransactionUseCase
    )
    {
    }

    /**
     * @param int $subjectId
     * @param int $studentId
     * @return array
     */
    public function subscribeSubject(
        int $subjectId,
        int $studentId,
        string $paymentMethod = PaymentEnums::WALLET
    ): array
    {
        try {
            DB::beginTransaction();
            $returnArr = [];
            $student = $this->studentRepo->findOrFail($studentId);
            $subject = $this->subjectRepo->findOrFail($subjectId);
            $transactionData = [
                'sender_id' => $student->user->id,
                'receiver_id' => $student->user->id,
                'payment_transaction_for' => PaymentEnums::SUBJECT,
                'subscribable_id' => $subject->id,
    
            ];
            if ($paymentMethod == PaymentEnums::WALLET) {
                $errors = $this->submitTransactionUseCase->validateSubmitTransaction($transactionData);
                if (count($errors)){
                    return $errors;
                }
                if ($student->wallet_amount < $subject->subscription_cost) {
                    $returnArr['status'] = 422;
                    $returnArr['detail'] = trans(
                        'subject.Your wallet does not have enough amount to subscribe this subject'
                    );
                    $returnArr['title'] = 'Wallet amount';
                    return $returnArr;
                }
                $wallet = $student->wallet_amount - $subject->subscription_cost;
                $this->studentRepo->update($student, ['wallet_amount' => $wallet]);
                $transaction = $this->paymentTransactionRepository->create([
                    'amount' => $subject->subscription_cost,
                    'sender_id' => $student->user->id,
                    'receiver_id' => $student->user->id,
                    'payment_transaction_for' => PaymentEnums::SUBJECT,
                    'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
                    'status'=>PaymentEnums::COMPLETED,
                    'payment_method' => $paymentMethod
                ]);
                $transaction->detail()->create([
                    'subscribable_id' => $subject->id,
                    'subscribable_type' => Subject::class
                ]);
            }
            $subscribeData = [
                'subject_id' => $subjectId,
                'date_of_purchase' => date('Y-m-d H:i:s')
            ];
            $this->studentRepo->createSubscribe($student, $subscribeData);
            DB::commit();
            $returnArr['status'] = 200;
            $returnArr['subject'] = $subject;
            return $returnArr;
        } catch (\Exception $exception) {
            DB::rollBack();
            $returnArr['status'] = 500;
            $returnArr['detail'] = trans('app.Oopps Something is broken');
            $returnArr['title'] = 'Oopps Something is broken';
            return $returnArr;
        }
    }
}
