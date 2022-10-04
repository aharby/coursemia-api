<?php

namespace App\OurEdu\Subscribes\Repository;

use App\OurEdu\Subscribes\Subscription;

/**
 * Payment transaction Repository
 */
class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function create($data, $subscriptionSubject)
    {
        return $subscriptionSubject->subscriptions()->create($data);
    }

    public function getUserSubscriptions($userId)
    {
        return Subscription::where('user_id', $userId)
            ->with('user', 'creator')
            ->latest()
            ->paginate();
    }

    public function findOrFail($id)
    {
        return Subscription::findOrFail($id);
    }

    public function markAsPaid(Subscription $subscription)
    {
        return $subscription->update(['payment_done' => true]);
    }

    public function update(Subscription $subscription, array $data)
    {
        return $subscription->update($data);
    }
}
