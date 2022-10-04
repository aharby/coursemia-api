<?php

namespace App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase;

use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;
use App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase\StudentSubscribeUseCaseInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;


class StudentSubscribeUseCase implements StudentSubscribeUseCaseInterface
{
    private $studentRepository;
    private $subjectPackageRepository;
    private $transactionRepository;

    public function __construct(
        StudentRepositoryInterface $studentRepository,
        SubjectPackageRepositoryInterface $subjectPackageRepository,
        TransactionRepositoryInterface $transactionRepository
    )
    {
        $this->studentRepository = $studentRepository;
        $this->subjectPackageRepository = $subjectPackageRepository;
        $this->transactionRepository = $transactionRepository;

    }

    /**
     * @param int $packageId
     * @param int $studentId
     * @return array
     */
    public function subscribePackage(int $packageId, int $studentId): array
    {
        $returnArr = [];
        $student = $this->studentRepository->findOrFail($studentId);
        $package = $this->subjectPackageRepository->findOrFail($packageId);

        if ($student->wallet_amount < $package->price) {
            $returnArr['status'] = 422;
            $returnArr['title'] = 'Wallet amount';
            $returnArr['detail'] = trans('subject_package.Your wallet does not have enough amount to subscribe this package');
            return $returnArr;
        }

        if ($student->wallet_amount >= $package->price) {
            $wallet = $student->wallet_amount - $package->price;

            $this->studentRepository->update($student, ['wallet_amount'=>$wallet]);

            $packageSubscriptionData = [
                'package_id' => $packageId,
                'date_of_purchase' => date('Y-m-d H:i:s')
            ];

            $this->studentRepository->subscribePackage($student, $packageSubscriptionData);
            $studentSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();
            foreach ($package->subjects()->whereNotIn('id',$studentSubjectIds)->get() as $subject) {
                $subjectSubscriptionData = [
                    'subject_id' => $subject->id,
                    'date_of_purchase' => date('Y-m-d H:i:s')
                ];
                $this->studentRepository->createSubscribe($student, $subjectSubscriptionData);
            }
            $this->transactionRepository->create([
                'user_id' => $student->user_id,
                'subscribable_id' => $package->id,
                'subscribable_type' => Package::class,
                'amount' =>  $package->price,
            ]);
            $returnArr['status'] = 200;
            $returnArr['package'] = $package;
            return $returnArr;
        }
        $returnArr['status'] = 500;
        $returnArr['detail'] = 'Oops Something is broken';
        $returnArr['title'] = trans('app.Oopps Something is broken');
        return $returnArr;
    }
}
