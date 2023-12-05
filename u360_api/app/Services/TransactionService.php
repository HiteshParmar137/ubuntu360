<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Project;
use App\Models\ProjectCommunity;
use App\Models\ProjectDonation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Stripe;

class TransactionService
{
    protected $currentPath;
    public function __construct()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
    }
    public function index($paramsArr)
    {
        return $paramsArr;
    }
    public function createPayment($parem)
    {
        try {
            $cardResponse = $this->cardSave($parem);
            if ($cardResponse['status'] === true) {
                $chargeArray = [
                    'amount' => ($parem['donation_amount'] + $parem['tips_amount']) * 100,
                    'currency' => config('constants.currency'),
                ];
                if ($parem['type'] == 2) {
                    if (changeDobDate($parem['recurring_date']) > date('Y-m-d')) {
                        $chargeArray['customer'] = $cardResponse['cardToken']->id;
                        $paymentData = Stripe\Charge::create($chargeArray);
                        Log::channel('transaction')->info('success',[
                            'Route' => $this->currentPath,
                            'Request' => $parem,
                            'TransactionDetails' => $paymentData,
                        ]);
                        ProjectDonation::where('id', $parem['donation_id'])
                            ->update([
                                'transaction_id' => $paymentData->id,
                                'status' => 'Paid'
                            ]);
                    } else {
                        $chargeArray['customer'] = $cardResponse['cardToken']->id;
                        $subscription = Stripe\Subscription::create([
                            'customer' => $cardResponse['cardToken']->id,
                            'items' => [
                                ['price' => $cardResponse['cardToken']['price_id']],
                            ],
                            // //'billing_cycle_anchor' => strtotime(changeDobDate($parem['recurring_date'])),
                            // 'trial_end'=>strtotime(changeDobDate($parem['recurring_date'])),
                            // // Set the specific date
                        ]);
                        Log::channel('transaction')->info('success',[
                            'Route' => $this->currentPath,
                            'Request' => $parem,
                            'TransactionDetails' => $subscription,
                        ]);
                        ProjectDonation::where('id', $parem['donation_id'])
                            ->update([
                                'transaction_type' => '2',
                                'is_recurring_start' => '1',
                                'transaction_id' => $subscription->id,
                                'status' => 'Paid'
                            ]);
                    }
                } else {
                    $chargeArray['source'] = $cardResponse['cardToken']->id;
                    $paymentData = Stripe\Charge::create($chargeArray);
                    Log::channel('transaction')->info('success',[
                        'Route' => $this->currentPath,
                        'Request' => $parem,
                        'TransactionDetails' => $paymentData,
                    ]);
                    ProjectDonation::where('id', $parem['donation_id'])
                        ->update([
                            'transaction_id' => $paymentData->id,
                            'status' => 'Paid'
                        ]);
                }

                $response['status'] = true;
                $response['message'] = config('message.front_user.donation_add_successfuly');
                return $response;
            } else {
                return $cardResponse;
            }
        } catch (\Exception $e) {
            Log::channel('transaction')->info('error',[
                'Route' => $this->currentPath,
                'Request' => $parem,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }
    public function cardSave($parem)
    {
        try {
            $expireDate = explode('/', $parem['expire_date']);
            $expMonth = $expireDate[0];
            $expYear = $expireDate[1];
            $cardToken = Stripe\Token::create([
                'card' => [
                    'number' => $parem['card_number'],
                    'exp_month' => $expMonth,
                    'exp_year' => $expYear,
                    'cvc' => $parem['cvv'],
                ],
            ]);
            if (!empty($cardToken->id) && $parem['type'] == 2) {
                $customer = Stripe\Customer::create(array(
                    "email" => $parem['email'],
                ));
                Stripe\Customer::createSource(
                    $customer->id,
                    ['source' => $cardToken->id]
                );
                $cardToken = $customer;
                if (changeDobDate($parem['recurring_date']) == date('Y-m-d')) {
                    $project = Project::where('id', $parem['project_id'])->first();
                    if (empty($project->stripe_product_id)) {
                        $product = Stripe\Product::create([
                            'name' => $project->title . '-' . $project->id, // Set the name of your product
                            'type' => 'service', // Set the type of product (e.g., service, good)
                        ]);

                        $project->stripe_product_id = $product->id;
                        $project->save();
                    }

                    $recurringPrice = Stripe\Price::create([
                        'unit_amount' => ($parem['donation_amount'] + $parem['tips_amount']) * 100,
                        // The amount in cents (e.g., $10.00)
                        'currency' => 'usd',
                        'recurring' => [
                            'interval' => 'day', // Set the desired interval (e.g., month, year)
                        ],
                        'product' => $project->stripe_product_id
                    ]);
                    $cardToken['price_id'] = $recurringPrice->id;
                }
                ProjectDonation::where('id', $parem['donation_id'])
                    ->update([
                        'customer_id' => $customer->id,
                    ]);
            }

            $response['status'] = true;
            $response['cardToken'] = $cardToken;
            return $response;
        } catch (\Exception $e) {
            Log::channel('transaction')->info('error',[
                'Route' => $this->currentPath,
                'Request' => $parem,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }
    public function createTransaction($parem)
    {
        $transaction = new Transaction;
        $transaction->project_id = $parem['project_id'];
        $transaction->sponsor_id = $parem['sponsor_id'];
        $transaction->stripe_customer_id = $parem['stripe_customer_id'];
        $transaction->transaction_id = $parem['transaction_id'];
        $transaction->amount = $parem['amount'];
        $transaction->save();
    }
    public function saveDonation($parem)
    {
        try {
            $donation = new ProjectDonation;
            $donation->project_id = $parem['project_id'];
            $user = jwtAuthUser();
            $donation->user_id = $user->id;
            if (!empty($parem['document'])) {
                $document = fileUploadPublic($parem['document'], config('constants.donation_file_upload_path'));
                $donation->document = $document;
            }
            $donation->comment = (!empty($parem['comment'])) ? $parem['comment']  : '';
            $donation->donation_amount = (!empty($parem['donation_amount'])) ? $parem['donation_amount'] : '';
            //1=>One Time, 2=>Recurring
            $donation->donation_type = (!empty($parem['type'])) ? $parem['type'] : '';
            if ($parem['type'] == 2) {
                $donation->month_end_date = (!empty($parem['recurring_date'])) ?
                    changeDobDate($parem['recurring_date']) : '';
            }

            $donation->tips_amount = (!empty($parem['tips_amount'])) ? $parem['tips_amount'] : '';
            $donation->email = (!empty($parem['email'])) ? $parem['email'] : '';
            $donation->save();

            $projectcommunity = new ProjectCommunity;
            $projectcommunity->user_id = $donation->user_id;
            $projectcommunity->project_id = $donation->project_id;
            $projectcommunity->donation_id = $donation->id;
            $projectcommunity->type = '1';
            $projectcommunity->save();
            $response['donation_id'] = $donation->id;
            $response['status'] = true;
            $response['message'] = config('message.front_user.donation_add_successfuly');
            return $response;
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $parem,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }
    public function getDonationDetails($projArr)
    {
        if (empty($projArr)) {
            return $dataArr['success'] = false;
        }
        $donation = ProjectDonation::query();
        (!empty($projArr['search_by']) && $projArr['search_by'] == 'my_donation')
            ? $donation =  $donation->where('user_id', $projArr['user']) : '';

        (!empty($projArr['search_by']) &&
            $projArr['search_by'] == 'my_project_donation' &&
            isset($projArr['project_ids']) &&
            !empty($projArr['project_ids'])
        ) ? $donation = $donation->whereIn('project_id', $projArr['project_ids']) : '';

        $donation = $donation->orderBy('created_at', 'DESC');
        $donation = $donation->paginate(env('PAGINATION_COUNT'));
        $response = [];
        if (!empty($donation)) {
            $i = 0;
            $page = $projArr['page'];
            foreach ($donation as $k => $item) {
                $response[$i]['sr_no'] = (($page - 1) * env('PAGINATION_COUNT')) + ($k + 1);
                $response[$i]['id'] = encryptId($item->id);
                $response[$i]['user_id'] = $item->user_id;
                $response[$i]['amount'] = $item->donation_amount;
                $response[$i]['tips'] = ($item->tips_amount != null) ? $item->tips_amount : 0.00;

                $user = User::find($item->user_id);
                $response[$i]['donor_name'] = $user->name;
                $response[$i]['profile_image'] = (isset($user->image) && $user->image != '') ?
                    $user->image : asset(config('constants.no_user_image_path'));

                $project = Project::find($item->project_id);
                $response[$i]['project_title'] = $project->title;
                $response[$i]['project_description'] = $project->description;
                $response[$i]['project_id'] = $project->id;

                $response[$i]['comment'] = $item->comment;
                $response[$i]['comment'] = $item->comment;
                $response[$i]['email'] = $item->email;
                $response[$i]['donation_type_name'] = !empty($item->donation_type) && $item->donation_type == '2' ?
                    'Recurring' : 'One Time';
                $response[$i]['donation_type'] = !empty($item->donation_type) ? $item->donation_type : '';
                $response[$i]['is_recurring_stop'] = !empty($item->is_recurring_stop) ? $item->is_recurring_stop : '0';
                $response[$i]['created_at'] = $item->created_at;
                $response[$i]['month_end_date'] = $item->month_end_date;
                $i++;
            }
        }
        $paginateData = $donation->toArray();
        $pagination =  pagination($paginateData);
        $dataArr = [
            'response' => $response,
            'pagination' => $pagination,
            'success' => true
        ];
        if ($donation->count() == 0) {
            $dataArr['success'] = false;
        }
        return $dataArr;
    }
    // This funtion  fro stop recurring donation
    public function stopRecurringDonation($parems)
    {
        try {
            $user = jwtAuthUser();
            $donation = ProjectDonation::where('user_id', $user->id)->where('id', $parems['donation_id'])->first();

            if (!empty($donation)) {
                if ($donation->transaction_type == 1) {
                    $childDonation = ProjectDonation::where('recurring_donation_id', $parems['donation_id'])->first();
                    if (!empty($childDonation)) {
                        $subscription = Stripe\Subscription::retrieve($childDonation->transaction_id);
                        $subscription->cancel();
                    }
                } else {
                    $subscription = Stripe\Subscription::retrieve($donation->transaction_id);
                    $subscription->cancel();
                }
                $donation->is_recurring_stop = '1';
                $donation->save();
                $response['status'] = true;
                $response['message'] = config('message.front_user.donation_recurring_stop');
            } else {
                $response['status'] = false;
                $response['message'] = config('message.front_user.donation_data_not_found');
            }
        } catch (\Exception $e) {
            Log::channel('transaction')->info('error', [
                'Route' => $this->currentPath,
                'Request' => $parems,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $response['status'] = false;
            $response['message'] = config('message.front_user.donation_data_not_found');
        }
        return $response;
    }

    //
    public function chargeWithCustomerId($donation)
    {
        try {
            $chargeArray = [
                'amount' => $donation['donation_amount'] * 100,
                'currency' => config('constants.currency'),
                'customer' => $donation['customer_id']
            ];
            $paymentData = Stripe\Charge::create($chargeArray);
            Log::channel('transaction')->info('Info', [
                'Route' => $this->currentPath,
                'Request' => $donation,
                'Info' => $paymentData
            ]);
            DB::beginTransaction();
            $projectDonation = new ProjectDonation;
            $projectDonation->project_id = $donation['project_id'];
            $projectDonation->user_id = $donation['user_id'];
            $projectDonation->recurring_donation_id = $donation['id'];
            $projectDonation->donation_amount = $donation['donation_amount'];
            $projectDonation->donation_type = '1';
            $projectDonation->email = $donation['email'];
            $projectDonation->name = $donation['name'];
            $projectDonation->transaction_id = $paymentData->id;
            $projectDonation->customer_id = $donation['customer_id'];
            $projectDonation->status = 'Paid';
            $projectDonation->save();
            ProjectDonation::where('id', $donation['id'])
                ->update([
                    'last_recurring_donation' => date('Y-m-d'),
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $donation,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
        }
    }
}
