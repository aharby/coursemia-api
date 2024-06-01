<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use App\Modules\CartItems\Models\CartItem;

use Stripe\StripeClient;

use  APP\Modules\Offers\Models\Offer;

class PaymentAPIController extends Controller
{
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
    
    public function createPaymentIntent(Request $request)
    {
        $promoCode = $request->promo_code;
        
        // if promocode is passed, check if it is valid
        if(!is_null($promoCode) && !Offer::whereRaw('LOWER(offer_code) = ?', [$promoCode])->count())
            return customResponse((object)[], "The selected promo code is invalid.", 442, StatusCodesEnum::FAILED);

        $amount = $this->getTotalCost($promoCode) * 100; // amount in cents, as stripe accepts it

        if($amount == 0)
            return customResponse(null, "Nothing to pay!", 200, StatusCodesEnum::DONE);

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['card']
              ]);

            return customResponse([
                "stripe_client_secret" => $paymentIntent->client_secret,
                "total_amount_in_cents" => $amount
            ], "Payment Intent Created successfully", 200, StatusCodesEnum::DONE);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function isPromoCodeValid($promoCode)
    {
        $promoCodeExists = Offer::whereRaw('LOWER(offer_code) = ?', [$promoCode])->count();

        if ($promoCodeExists)
            return customResponse([
                "is_valid" => true
            ], "Promocode is valid", 200, StatusCodesEnum::DONE);     
        else
            return customResponse([
                "is_valid" => false
            ], "Invalid Promocode", 200, StatusCodesEnum::DONE);
    }
}