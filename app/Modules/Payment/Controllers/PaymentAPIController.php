<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use App\Modules\Payment\Services\PaymentService;

use  APP\Modules\Offers\Models\Offer;

class PaymentAPIController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function createPaymentIntent(Request $request)
    {
        $promoCode = $request->promo_code;
        
        // if promocode is passed, check if it is valid
        if(!is_null($promoCode) && !Offer::whereRaw('LOWER(offer_code) = ?', [$promoCode])->count())
            return customResponse((object)[], "The selected promo code is invalid.", 442, StatusCodesEnum::FAILED);

        $amount = $this->paymentService->getTotalCost($promoCode) * 100; // amount in cents, as stripe accepts it

        if($amount == 0)
            return customResponse(null, "Nothing to pay!", 200, StatusCodesEnum::DONE);

        $customerId = $this->paymentService->getStripeCustomerId();

        $ephemeralKey = $this->paymentService->getStripeCustomerEphemeralKey($customerId);

        try {
            $paymentIntent = $this->paymentService->createPaymentIntent($customerId, $amount);

            return customResponse([
                "paymentIntent" => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customer' => $customerId,
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