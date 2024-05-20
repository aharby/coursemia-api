<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use Stripe\StripeClient;

class PaymentAPIController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => 10000, //amount in cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'transfer_group' => 'ORDER100',
              ]);

            return response()->json([
                'paymentIntent' => $paymentIntent->client_secret,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}