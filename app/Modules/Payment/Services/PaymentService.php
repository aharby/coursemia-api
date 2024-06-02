<?php

namespace APP\Modules\payment\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;

use App\Modules\CartItems\Models\CartItem;

use App\Modules\Users\Models\User;
class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function getStripeCustomerId()
    {
        $user = auth('api')->user();

        $customerId = $user->strip_customer_id;

        if(!$customerId)
        {
            $stripe_customer = Customer::create([
                'email' => $user->email,
                'name' => $user->full_name,
                ]);

            $customerId = $stripe_customer->id;

            $user->stripe_customer_id = $customerId;
            $user->save();
        }

        return $customerId;
    }

    public function getStripeCustomerEphemeralKey($customerId)
    {
        return EphemeralKey::create([
            'customer' => $customerId
        ]);
    }

    public function getTotalCost($promoCode)
    {
        $user = auth('api')->user();

        $cartItems = CartItem::where('user_id', $user->id)
        ->with('course')
        ->get();

        // Calculate the total price
        $totalPrice = $cartItems->map(function ($cartItem) {
            $course = $cartItem->course;
            return $course->price_after_discount ?? $course->price;
        })->sum();
    }

    public function createPaymentIntent($customerId, $amount, $currency = 'usd')
    {
        return PaymentIntent::create([
            'customer' => $customerId,
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card']
        ]);
    }

    public function processSuccessfulPaymentForCustomer($customerId)
    {
        $stripCustomer = Customer::retrieve($customerId);

        $user = User::where('stripe_customer_id', $customerId);
    }

}