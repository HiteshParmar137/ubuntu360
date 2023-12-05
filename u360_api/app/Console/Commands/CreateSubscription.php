<?php

namespace App\Console\Commands;

use App\Jobs\ThankYouRecurringJob;
use App\Models\Project;
use App\Models\ProjectDonation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe;

class CreateSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateSubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron for create recurring subscription.';

    /**
     * Execute the console command.
     *
     * @return int
     */

    //This cron created for new subscription create which user is selected on recurring donation form.

    public function handle()
    {
        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $projectDonation = ProjectDonation::where('donation_type', '2')
                ->where('month_end_date', date('Y-m-d'))
                ->where('is_recurring_start', '0')
                ->where('is_recurring_stop', '0')
                ->get();
            if (!empty($projectDonation)) {
                foreach ($projectDonation as $oldDonation) {
                    $project = Project::where('id', $oldDonation->project_id)->first();
                    if (empty($project->stripe_product_id)) {
                        $product = Stripe\Product::create([
                            'name' => $project->title . '-' . $project->id, // Set the name of your product
                            'type' => 'service', // Set the type of product (e.g., service, good)
                        ]);

                        $project->stripe_product_id = $product->id;
                        $project->save();
                    }

                    $recurringPrice = Stripe\Price::create([
                        'unit_amount' => ($oldDonation->donation_amount + $oldDonation->tips_amount) * 100,
                        // The amount in cents (e.g., $10.00)
                        'currency' => 'usd',
                        'recurring' => [
                            'interval' => 'daily', // Set the desired interval (e.g., month, year)
                        ],
                        'product' => $project->stripe_product_id
                    ]);
                    $subscription = Stripe\Subscription::create([
                        'customer' => $oldDonation->customer_id,
                        'items' => [
                            ['price' => $recurringPrice->id],
                        ],
                        // //'billing_cycle_anchor' => strtotime(changeDobDate($parem['recurring_date'])),
                        // 'trial_end'=>strtotime(changeDobDate($parem['recurring_date'])),
                        // // Set the specific date
                    ]);
                    Log::channel('transaction')->info('success', [
                        'Request' => 'create subscription cron',
                        'response' => $subscription,
                    ]);
                    $donation = new ProjectDonation;
                    $donation->project_id = $oldDonation->project_id;
                    $donation->user_id = $oldDonation->user_id;
                    $donation->recurring_donation_id = $oldDonation->id;
                    $donation->comment = $oldDonation->comment;
                    $donation->donation_amount = $oldDonation->donation_amount;
                    $donation->donation_type = $oldDonation->donation_type;
                    $donation->tips_amount = $oldDonation->tips_amount;
                    $donation->email = $oldDonation->email;
                    $donation->transaction_id = $subscription->id;
                    $donation->customer_id = $oldDonation->customer_id;
                    $donation->status = config('constants.paid_status');
                    $donation->is_recurring_stop = '1';
                    $donation->transaction_type = '2';
                    $donation->save();
                    ProjectDonation::where('id', $oldDonation->id)
                        ->update([
                            'is_recurring_start' => '1',
                        ]);
                    dispatch(new ThankYouRecurringJob($oldDonation->project_id));
                }
            }
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::channel('transaction')->info('error', [
                'Request' => 'create subscription cron',
                'Error' => $e->getMessage(),
                'Line' => $e->getLine(),
            ]);
        }
    }
}
