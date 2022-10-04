<?php

namespace App\OurEdu\Subscribes\UseCases;

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\SubjectPackages\Package;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Subscribes\Repository\SubscriptionRepositoryInterface;

class SubscriptionUseCase implements SubscriptionUseCaseInterface
{
    protected $user;
    protected $userRepository;
    protected $courseReposity;
    protected $subscriptionRepository;
    protected $studentRepo;
    protected $transactionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseReposity,
        SubscriptionRepositoryInterface $subscriptionRepository,
        StudentRepositoryInterface $studentRepo,
        PaymentTransactionRepositoryInterface $transactionRepository
    )
    {
        $this->user = Auth::guard('api')->user();
        $this->userRepository = $userRepository;
        $this->courseReposity = $courseReposity;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->studentRepo = $studentRepo;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     *  Parent subscribe to course for children
     * @param  integer $userId
     * @param  mixed $subscriptionSubject
     * @return App\OurEdu\Subscribes\Subscription
     */
    public function subscripeSubject($studentId, $subscriptionSubject)
    {
        $student = $this->studentRepo->findOrFail($studentId);

        if ($subscriptionSubject->subscriptions()->where('user_id', $student->user->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscriped'));
        }

        if ($subscriptionSubject->students()->where('students.id', $student->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscriped'));
        }
        if ($student->wallet_amount < $subscriptionSubject->subscription_cost) {
            throw new ErrorResponseException(trans('api.Not enough balance in student wallet'));
        }

        $wallet = $student->wallet_amount - $subscriptionSubject->subscription_cost;

        $this->studentRepo->update($student, ['wallet_amount' => $wallet]);

        $transaction = $this->transactionRepository->create([
            'amount' => $subscriptionSubject->subscription_cost,
            'sender_id' => $student->user_id,
            'receiver_id' => $student->user_id,
            'payment_transaction_for' => PaymentEnums::SUBJECT,
            'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
            'status' => PaymentEnums::COMPLETED,
            'payment_method' => PaymentEnums::WALLET
        ]);
        $transaction->detail()->create([
            'subscribable_id' => $subscriptionSubject->id,
            'subscribable_type' => Subject::class
        ]);

        $subscribeData = [
            'subject_id' => $subscriptionSubject->id,
            'date_of_purchase' => date('Y-m-d H:i:s')
        ];

        $this->studentRepo->createSubscribe($student, $subscribeData);

        return $this->subscriptionRepository->create([
            'user_id'   =>  $student->user->id
        ], $subscriptionSubject);
    }

    /**
     * Student pays the course to register
     * @param  integer $id
     * @return App\OurEdu\Subscribes\Subscription
     */
    public function subscriptionPayment($id)
    {
        $subscription = $this->subscriptionRepository->findOrFail($id);

        $student = $subscription->user->student;
        $subscriptionSubject = $subscription->subscripable;

        if ($subscriptionSubject->students()->where('students.id', $student->id)->exists() || $subscription->payment_done) {
            throw new ErrorResponseException(trans('api.Student already paid for this subscription'));
        }

        if ($student->wallet_amount < $subscriptionSubject->subscription_cost) {
            throw new ErrorResponseException(trans('api.Not enough balance in student wallet'));
        }

        $walletBalance = $student->wallet_amount - $subscriptionSubject->subscription_cost;

        $this->studentRepo->update($student, ['wallet_amount' => $walletBalance]);

        if ($subscriptionSubject instanceof Subject) {
            $data = [
                'subject_id' => $subscriptionSubject->id,
                'date_of_purchase' => now()
            ];

            $this->studentRepo->createSubscribe($student, $data);
        }

        if ($subscriptionSubject instanceof Course) {
            $this->studentRepo->subscripeOnCourse($student, $subscriptionSubject);
        }

        // temporary untill payment proccessing
        $this->subscriptionRepository->update($subscription, [
            'payment_done'  =>  true,
            'order_id'  =>  $this->user->orders()->create(['order_key' => str_random(6)])->id
        ]);

        return $subscription;
    }

    // Parent subscribe to course for children
    public function subscripeCourse($studentId, $subscriptionCourse)
    {
        $student = $this->studentRepo->findOrFail($studentId);

        if ($subscriptionCourse->subscriptions()->where('user_id', $student->user->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscribed'));
        }

        if ($subscriptionCourse->students()->where('students.id', $student->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscribed'));
        }
        if ($student->wallet_amount < $subscriptionCourse->subscription_cost) {
            throw new ErrorResponseException(trans('api.Not enough balance in student wallet'));
        }

        $wallet = $student->wallet_amount - $subscriptionCourse->subscription_cost;

        $this->studentRepo->update($student, ['wallet_amount' => $wallet]);

        $transaction = $this->transactionRepository->create([
            'amount' => $subscriptionCourse->subscription_cost,
            'sender_id' => $student->user_id,
            'receiver_id' => $student->user_id,
            'payment_transaction_for' => PaymentEnums::COURSE,
            'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
            'status' => PaymentEnums::COMPLETED,
            'payment_method' => PaymentEnums::WALLET
        ]);
        $transaction->detail()->create([
            'subscribable_id' => $subscriptionCourse->id,
            'subscribable_type' => Course::class
        ]);

        $subscribeData = [
            'course_id' => $subscriptionCourse->id,
            'date_of_pruchase' => date('Y-m-d H:i:s'),
            'instructor_id' => $subscriptionCourse->instructor_id
        ];

        $this->studentRepo->createCourseSubscribe($student, $subscribeData);

        return $this->subscriptionRepository->create([
            'user_id'   =>  $student->user->id
        ], $subscriptionCourse);
    }

    /**
     * @param $studentId
     * @param $subjectPackage
     * @return mixed|void
     * @throws ErrorResponseException
     */
    public function subscribeSubjectPackage($studentId, $subjectPackage)
    {
        $student = $this->studentRepo->findOrFail($studentId);


        if ($subjectPackage->subscriptions()->where('user_id', $student->user->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscribed'));
        }

        if ($subjectPackage->students()->where('students.id', $student->id)->exists()) {
            throw new ErrorResponseException(trans('api.Student already subscribed'));
        }
        if ($student->wallet_amount < $subjectPackage->price) {
            throw new ErrorResponseException(trans('api.Not enough balance in student wallet'));
        }

        $wallet = $student->wallet_amount - $subjectPackage->price;

        $this->studentRepo->update($student, ['wallet_amount' => $wallet]);

        $this->transactionRepository->create(
            [
                'user_id' => $student->user_id,
                'subscribable_id' => $subjectPackage->id,
                'subscribable_type' => Package::class,
                'amount' => $subjectPackage->price,
            ]
        );

        $subscribeData = [
            'package_id' => $subjectPackage->id,
            'date_of_purchase' => date('Y-m-d H:i:s')
        ];

        $this->studentRepo->subscribePackage($student, $subscribeData);

        $studentSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();
        foreach ($subjectPackage->subjects()->whereNotIn('id',$studentSubjectIds)->get() as $subject) {
            $subjectSubscriptionData = [
                'subject_id' => $subject->id,
                'date_of_purchase' => date('Y-m-d H:i:s')
            ];
            $this->studentRepo->createSubscribe($student, $subjectSubscriptionData);
        }

        return $this->subscriptionRepository->create([
            'user_id'   =>  $student->user->id
        ], $subjectPackage);
    }
}
