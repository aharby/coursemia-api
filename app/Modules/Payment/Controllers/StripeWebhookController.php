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

    public function updatePaymentIntentStatus(Request $request)
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
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object; // contains a StripePaymentIntent
            // Handle the successful payment intent.
            Log::info('Payment Intent Succeeded', ['payment_intent' => $paymentIntent]);
            // You can add your own business logic here, e.g., updating order status in your database.

            $this->paymentService->processSuccessfulPayment($paymentIntent);
        }

        return response()->json(['status' => 'success']);
    }
}