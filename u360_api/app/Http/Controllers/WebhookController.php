<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Stripe;

class WebhookController extends Controller
{
    /**
     * subscription webhook handle events
     *
     * update data based on events
     * Developed by : Hitesh Parmar
     * Timestamp : 25-01-2023/16:00
     */
    protected $currentPath;
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->transactionService = $transactionService;
    }

    public function update(Request $request)
    {
        // Replace this endpoint secret with your endpoint's unique secret
        // If you are testing with the CLI, find the secret by running 'stripe listen'
        // If you are using an endpoint defined with the API or dashboard, look in your webhook settings
        // at https://dashboard.stripe.com/webhooks
        $endpointSecret = 'whsec_6a3f6a20faa5fd571ac74ad530d5757fa719ff8ce30c9c2721a27881d0978ea0';

        $payload = @file_get_contents('php://input');
        $event = null;
        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            // echo '⚠️  Webhook error while parsing basic request.';
            // http_response_code(400);
            Log::channel('transactionlog')->info('log', [
                'Route' => $this->currentPath,
                'Request' => [],
                'Error' => 'Webhook error while parsing basic request.',
                'Line' => ''
            ]);
            exit;
        }
        if ($endpointSecret) {
            // Only verify the event if there is an endpoint secret defined
            // Otherwise use the basic decoded event
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                // echo '⚠️  Webhook error while validating signature.';
                // http_response_code(400);
                // exit();
                Log::info([
                    'Route' => $this->currentPath,
                    'Request' => [],
                    'Error' => '⚠️  Webhook error while validating signature.',
                    'Line' => ''
                ]);
                exit();
            }
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                // handlePaymentIntentSucceeded($paymentIntent);
                Log::info([
                    'Route' => $this->currentPath,
                    'Request' => $paymentIntent,
                    'Error' => 'Success',
                    'Line' => ''
                ]);
                break;
            case 'invoice.payment_succeeded':
                $invoiceIntent = $event->data->object;
                $subscriptionId = $invoiceIntent->subscription;
                if ($invoiceIntent->billing_reason == 'subscription_cycle' && $invoiceIntent->status == 'paid') {

                    $oldTransaction = Transaction::where('transaction_id', $subscriptionId)->with(['hasDonation'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($oldTransaction) {
                        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
                        $subscriptionData = $stripe->subscriptions->retrieve($subscriptionId, []);
                        try {
                            if ($subscriptionData->plan) {
                                $subscriptionPlan = $subscriptionData->plan;
                                $parem['project_id'] = $oldTransaction->project_id;
                                $parem['donation_id'] = $oldTransaction->donation_id;
                                $parem['stripe_customer_id'] = $oldTransaction->customer_id;
                                $parem['transaction_id'] = $subscriptionId;
                                $parem['amount'] = $subscriptionPlan->amount;
                                $parem['type'] = $subscriptionPlan->amount;
                                $parem['status'] = $invoiceIntent->status;
                                $tipsTransaction = $this->transactionService->createTransaction($parem);
                                Log::channel('transactionlog')->error('webhook_save_transaction_data', [
                                    'data' => $tipsTransaction
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::channel('transactionlog')->error('log', [
                                'Route' => $this->currentPath,
                                'Request' => $invoiceIntent,
                                'Error' => $e->getMessage(),
                                'Line' => $e->getLine()
                            ]);
                        }
                    }
                }
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                // handlePaymentMethodAttached($paymentMethod);
                break;
            default:
                // Unexpected event type
                error_log('Received unknown event type');
        }
    }
}
