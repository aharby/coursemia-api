<?php

namespace App\OurEdu\Subscribes\Repository;

interface SubscriptionRepositoryInterface
{
    public function create($data, $subscriptionSubject);
}
