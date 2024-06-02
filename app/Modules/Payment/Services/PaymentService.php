<?php

namespace APP\Modules\payment\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;

use App\Modules\CartItems\Models\CartItem;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function getStripeCustomer()
    {
        $user = auth('api')->user();

        $customer = $this->getStripeCustomerByEmail($user->email);

        if(!$customer)
            $customer = $this->createStripeCustomer($user->email, $user->full_name);

        return $customer;
    }

    public function getStripeCustomerByEmail($email)
    {
        $customers = Customer::all();

        foreach ($customers->data as $customer) {
            if ($customer->email === $email) {
                return $customer;
            }
        }

        return null;
    }

    public function createStripeCustomer($email, $name)
    {
        $customer = Customer::create([
            'email' => $email,
            'name' => $name,
        ]);

        return $customer;
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
}