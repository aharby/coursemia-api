<?php

namespace App\OurEdu\Payments\Repository;

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Models\Transaction;
use App\OurEdu\Users\User;
use stdClass;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    private $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

//    public function create($data)
//    {
//        return $this->model->create($data);
//    }

//   public function transactionsSpending(array $usersIds) {
//
//    if (request()->has('user_id')){
//
//        return  User::query()->with(['student.educationalSystem.country'])
//                ->whereHas('payments' , function($query) {
//                    $query->where('receiver_id' , request()->get('user_id'))
//                     ->where('status',PaymentEnums::COMPLETED);
//                })
//                ->withSum(
//                    'payments','amount'
//                )
//                ->get();
//        }
//
//        return User::query()->with(['student.educationalSystem.country', 'payments'])
//                ->whereHas('payments' , function($query) use ($usersIds){
//                    $query->whereIn('receiver_id' , $usersIds)
//                    ->where('status',PaymentEnums::COMPLETED);
//                })
//                ->withSum(
//                   'payments','amount'
//                )->orderBy('id' , 'desc')->jsonPaginate();
//}

//    public function getUsersTransactions(array $usersIds , bool $isPaginate = true)
//    {
//        $query = $this->model->orderBy('id', 'desc');
//
//        if (!request()->has('user_id')) {
//            $query =   $query->whereIn('user_id', $usersIds);
//        }
//        if (request()->has('user_id')) {
//            $query =   $query->where('user_id', request()->get('user_id'));
//        }
//        if (request()->has('from_date')) {
//            $query = $query->whereDate(
//                'created_at',
//                '>=',
//                Carbon::parse(request()->get('from_date'))
//                    ->format('Y-m-d')
//            );
//        }
//        if (request()->has('to_date')) {
//            $query = $query->whereDate(
//                'created_at',
//                '<=',
//                Carbon::parse(request()->get('to_date'))
//                    ->format('Y-m-d')
//            );
//        }
//
//        return $isPaginate ? $query->jsonPaginate() : $query->get();
//
//    }

//    public function getTotalUsersTransactionsSpending(array $usersIds)
//    {
//        $totalSpending = 0;
//        $walletAmount = 0;
//        $users = User::query()->with(['student', 'transactions'])
//            ->whereIn('id', $usersIds)
//            ->withSum('transactions', 'amount')
//            ->orderBy('id', 'desc')
//            ->get();
//
//        foreach ($users as $user) {
//            $totalSpending += $user->transactions_sum_amount;
//            $walletAmount += $user->student?->wallet_amount;
//        }
//
//        $userTransactionsObject = new stdClass;
//        $userTransactionsObject->total_spending = $totalSpending;
//        $userTransactionsObject->wallet_amount = $walletAmount;
//        return $userTransactionsObject;
//    }
}
