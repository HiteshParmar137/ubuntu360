<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Jobs\ThankYouRecurringJob;
use App\Models\Project;
use App\Models\ProjectDonation;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

class TransactionController extends Controller
{
    protected $currentPath;
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->transactionService = $transactionService;
    }
    /* this funcation is used for add donation
     */
    public function addDonation(Request $request)
    {
        try {
            $validationArray = [
                'email' => 'required|email',
                //'name' => 'required',
                'card_number' => 'required',
                'expire_date' => 'required',
                'cvv' => 'required',
                'project_id' => 'required',
                'donation_amount' => 'required',
            ];
            if ($request->type == 2) {
                $validationArray['recurring_date'] = 'required_if:donation_recurring_type,1';
            }
            $validator = Validator::make($request->all(), $validationArray);
            if ($validator->fails()) {
                return response()->json(apiResponse(200, false, $validator->errors(), null), 200);
            }
            $postData = $request->all();
            DB::beginTransaction();

            $postData['project_id'] = decryptId($postData['project_id']);
            $response = $this->transactionService->saveDonation($postData);
            if ($response['status'] === true) {
                $postData['donation_id'] = $response['donation_id'];
                $transactionResponse = $this->transactionService->createPayment($postData);
                if ($transactionResponse['status'] === true) {
                    DB::commit();
                    dispatch(new ThankYouRecurringJob($postData['project_id']));
                    return response()->json(
                        apiResponse(
                            200,
                            $transactionResponse['status'],
                            $transactionResponse['message'],
                            null
                        ),
                        200
                    );
                } else {
                    $response = $transactionResponse;
                }
            }
            DB::rollBack();
            return response()->json(
                apiResponse(
                    200,
                    $response['status'],
                    $response['message'],
                    null
                ),
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            $message = config('message.common_message.exception_error');
            return response()->json(apiResponse(500, false, $message, null), 500);
        }
    }
    /* this funcation is used for get donation list
     */
    public function index(Request $request)
    {
        try {
            $userId = jwtAuthUser()->id;
            $paramsArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'specific_user_data' => $userId ?? 0,
                'title' => $request->title ?? '',
            ];
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }
    /* this funcation is used for get donation details
     */
    public function getDonationDetails(Request $request)
    {
        try {
            $userId = jwtAuthUser()->id;
            $paramsArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'user' => $userId ?? 0,
                'search_by' => !empty($request->list) ? $request->list : 'my_donation'
            ];
            $paramsArr['project_ids'] = [];
            if (!empty($userId)) {
                $project = Project::select('id')->where('user_id', $userId)->get()->toArray();
                if (!empty($project)) {
                    foreach ($project as $key => $value) {
                        $paramsArr['project_ids'][$key] = $value['id'];
                    }
                }
            }
            $transactions = $this->transactionService->getDonationDetails($paramsArr);
            if ($transactions['success'] === false) {
                return response()->json([
                    'status_code' => 200,
                    'success' => false,
                    'message' => config('message.common_message.error_message'),
                    'data' => null
                ], 200);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => config('message.transactions.fetch_success'),
                'data' => $transactions
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this funcation is used for stop recurring donation
     * send type as request
     */
    public function stopRecurringDonation(Request $request)
    {
        try {
            $validationArray = [
                'donation_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validationArray);
            if (!$validator->fails()) {
                $paramsArr = [
                    'donation_id' => decryptId($request->donation_id)
                ];
                $paramsArr['project_ids'] = [];
                $donationResponse = $this->transactionService->stopRecurringDonation($paramsArr);
                return response()->json([
                    'status_code' => 200,
                    'success' => $donationResponse['status'],
                    'message' => $donationResponse['message'],
                    'data' => null
                ], 200);
            } else {
                $response['message'] = $validator->errors();
                $response['status'] = false;
                return response()->json(apiResponse(200, false, $response['message'], null), 200);
            }
        } catch (\Exception $e) {
            Log::info([
                'Route' => $this->currentPath,
                'Request' => $request->all(),
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => config('message.common_message.exception_error'),
                'data' => null
            ], 500);
        }
    }

    /* this funcation is used for recurring payment cron
     * send type as request
     */
    public function recurringPaymentCron()
    {
        $currentDate = Carbon::now();
        $oneMonthLater = $currentDate->addMonth(-1);
        $formattedDate = $oneMonthLater->format('Y-m-d');
        $projectDonation = ProjectDonation::where('donation_type', '2')
            ->where('is_recurring_stop', '0')
            ->where('last_recurring_donation', '<=', $formattedDate)
            ->get();
        if (!empty($projectDonation)) {
            foreach ($projectDonation as $donation) {
                $this->transactionService->chargeWithCustomerId($donation);
            }
        }

        p($projectDonation);
    }

    /* this funcation is used for get weebhook response from strip and update donation data
     * send type as request
     */
    public function handleWebhook(Request $request)
    {
        
        $payload = @file_get_contents('php://input');
        $sigHeader = $request->header('Stripe-Signature');
        $event = null;
        //env('STRIPE_WEBHOOK_SECRET')
        //$local_wb_se='whsec_da2ec5339c9de97440b5e1d675f5d10d90a7eeb7ded2de5165a3847d40a14478';
        $localWbSe=env('STRIPE_WEBHOOK_SECRET');
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $localWbSe
            );
        } catch (\UnexpectedValueException $e) {
            Log::channel('transaction')->info('error',[
                'Request' => $request,
                'Error' => $e->getMessage(),
                'Line' => $e->getLine(),
            ]);

            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::channel('transaction')->info('error',[
                'Request' => 'invalid signature',
                'Error' => $e->getMessage(),
                'Line' => $e->getLine(),
            ]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }
        
        // // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $invoiceIntent = $event->data->object;
                $subscriptionId = $invoiceIntent->subscription;
                if ($invoiceIntent->billing_reason == 'subscription_cycle' && $invoiceIntent->status == 'paid') {

                    $oldDonation = ProjectDonation::where('transaction_id', $subscriptionId)
                        ->first();
                    if ($oldDonation) {
                        try {
                            DB::beginTransaction();
                            $donation = new ProjectDonation;
                            
                            $donation->project_id = $oldDonation->project_id;
                            $donation->user_id = $oldDonation->user_id;
                            $donation->recurring_donation_id = $oldDonation->id;
                            $donation->comment = $oldDonation->comment;
                            $donation->donation_amount = $oldDonation->donation_amount;
                            $donation->donation_type = $oldDonation->donation_type;
                            $donation->tips_amount = $oldDonation->tips_amount;
                            $donation->email = $oldDonation->email;
                            $donation->transaction_id = $oldDonation->transaction_id;
                            $donation->customer_id = $oldDonation->customer_id;
                            $donation->status = config('constants.paid_status');
                            $donation->is_recurring_stop = '1';
                            $donation->transaction_type = '2';
                            $donation->save();
                            dispatch(new ThankYouRecurringJob($oldDonation->project_id));
                            DB::commit();
                            Log::channel('transaction')->info('success',[
                                'Route' => 'strip',
                                'Request' => $invoiceIntent,
                                'message' => 'Operation successfull.'
                            ]);
                        } catch (\Exception $e) {
                            DB::rollback();
                            Log::channel('transaction')->info('error',[
                                'invoiceIntent' => $invoiceIntent,
                                'Error' => $e->getMessage(),
                                'Line' => $e->getLine(),
                            ]);
                        }
                    }
                }
                break;
        }
        
        return response()->json(['message' => 'Operation successfull.'], 200);
        
    }
}
