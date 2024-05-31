<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use App\Modules\CartItems\Models\CartItem;

use Stripe\StripeClient;

class PaymentAPIController extends Controller
{
    public function getTotalCost()
    {
        $user = auth('api')->user();

        return CartItem::where('user_id', $user->id)
        ->with('course')
        ->get()
        ->pluck('course.price')
        ->sum();
    }
    
    public function createPaymentIntent(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $this->getTotalCost(), //amount in cents
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