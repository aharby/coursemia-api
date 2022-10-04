<?php

namespace App\OurEdu\Payments\UseCases;

use Illuminate\Database\Eloquent\Model;

interface SubmitTransactionUseCaseInterface
{
    /**
     * Add money to student wallet
     * @param $parent
     * @param $student
     * @param $data
     * @return Model
     */
    public function addMoney($parent, $student, $data):array;

    public function payfortData($data);
}
