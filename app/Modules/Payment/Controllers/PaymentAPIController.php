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

        $amount = $this->getTotalCost() * 100; // amount in cents, as stripe accepts it

        if($amount == 0)
            return customResponse(null, "Nothing to pay!", 200, StatusCodesEnum::DONE);

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['card']
              ]);

            return customResponse([
                "stripe_client_secret" => $paymentIntent->client_secret
            ], "Payment Intent Created successfully", 200, StatusCodesEnum::DONE);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}