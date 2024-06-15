<?php

namespace APP\Modules\payment\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;
use Stripe\Transfer;

use App\Modules\Users\Models\User;
use App\Modules\Payment\Models\CartCourse;

use App\Modules\Courses\Models\CourseUser;

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

        $courses = $user->cartCourses->pluck('course');

        // Calculate the total price
        $totalPrice = $courses->map(function ($course) {
            return $course->price_after_discount ?? $course->price;
        })->sum();

        return $totalPrice;
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

    public function processSuccessfulPayment(PaymentIntent $paymentIntent)
    {
        $user = User::where('stripe_customer_id', $paymentIntent->customer);

        //create order
        $order = $user->orders()->create([
            'total_price' => $paymentIntent->amount/100, // price in dollars
            'stripe_invoice_id' => $paymentIntent->invoice
        ]);

        //add courses to user
        $courses = $user->cartCourses;

        foreach ($courses as $course){
            $course_user = new CourseUser;
            $course_user->user_id = $user->id;
            $course_user->course_id = $course->id;
            $course_user->save();

            $order->courses()->attach($course->id);

            // payout connected account

            $stripeConnectedAccountId = $course->admin->stripe_connected_account_id;

            $transfer = Transfer::create([
                'amount' => 7000,
                'currency' => 'usd',
                'destination' => $stripeConnectedAccountId,
                'transfer_group' => $paymentIntent->transfer_group,
              ]);
        }

        // empty cart
        CartCourse::where('user_id', $user->id)->delete();
       
    }

}