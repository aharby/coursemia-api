<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;

use APP\Enums\StatusCodesEnum;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

use APP\Modules\Payment\Services\PaymentService;

class StripeWebhookController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function updatePaymentStatus(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_PAYMENT_INTENT_WEBHOOK_SECRET');
        
        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        $responseMeassage = "";

        $paymentIntent = $event->data->object;

        if ($event->type === 'payment_intent.succeeded') {

            $this->paymentService->processSuccessfulPayment($paymentIntent);

            $responseMeassage = "Payment Intent Succeeded";
        }

        else if ($event->type === 'payment_intent.created')
            $responseMeassage = "Payment Intent Created";

        else if ($event->type === 'payment_intent.payment_failed') {

            $responseMeassage = "Payment Intent Failed" . ['payment_intent' => $paymentIntent];
            
            Log::error($responseMeassage, ['payment_intent' => $paymentIntent]);
        }

        else if ($event->type === 'payment_intent.requires_action') 
            $responseMeassage = "Payment Intent Requires Action";


            
        else if ($event->type === 'payment_intent.canceled') {
            
            $responseMeassage = "Payment Intent Canceled";

            Log::warning($responseMeassage, ['payment_intent' => $paymentIntent]);
        }

        else if ($event->type === 'payment_intent.partially_funded') 
            $responseMeassage = "Payment Intent Partially Funded";

        else if ($event->type === 'payment_intent.processing') 
            $responseMeassage = "Payment Intent Processing";

        else {
            $responseMeassage = "Unhandled event type: " . $event->type;

            Log::error($responseMeassage, ['event_type' => $event->type]);
        }
        

        return customResponse(
            ['message' => $responseMeassage],
            "Webhook processed successfully",
            200,
            StatusCodesEnum::DONE
        );
    }
}