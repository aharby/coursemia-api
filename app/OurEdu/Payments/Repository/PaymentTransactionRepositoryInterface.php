<?php

namespace App\OurEdu\Payments\Repository;

use App\OurEdu\Users\User;

interface PaymentTransactionRepositoryInterface
{
    public function create($data, $method = null);

    public function findBy($key, $value);

    public function findByWith($key,$value,$with);

    public function updateByOrderNumber($reference, $data);

    public function sentPaymentTransactions($user);

    public function paymentTransactionsCountByPeriod(User $user, string $period);

    public function all($filters = [], $paginated = false);

    public function studentReceivedPaymentTransactions(array $children, bool $isPaginate = true);

    public function getTotalUsersTransactionsSpending(array $usersIds);

    public function getChildrenExpensesTransactions(array $usersIds, bool $isPaginate);

    public function getUsersTransactionsSpending(array $usersIds);

    public function getTotalDeposit();

    public function getTotalWithdraw();

    public function paymentTransactionsReport();
}

