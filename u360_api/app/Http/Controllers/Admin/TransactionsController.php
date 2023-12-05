<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    protected $currentPath;
    protected $transactionServiceObj;
    public function __construct(TransactionService $transaction)
    {
        $this->currentPath = Route::getFacadeRoot()->current()->uri();
        $this->transactionServiceObj = $transaction;
    }

    public function index(Request $request)
    {
        try {
            $paramsArr = [
                'page' => !empty($request->page) ? $request->page : 1,
                'user' => $request->user ?? '',
                'title' => $request->title ?? '',
                'srchByUser' => '1'
            ];
            $transactions = $this->transactionServiceObj->index($paramsArr);
            if ($transactions['success'] === false) {
                return response()->json([
                    'status_code' => 200, 'success' => false,
                    'message' => config('message.common_message.error_message')
                ], 200);
            }
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => config('message.transactions.fetch_success'), 'data' => $transactions
            ], 200);
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

    public function getUsers(Request $request)
    {
        try {
            $response = [];
            $transactionUsers = Transaction::query();
            $transactionUsers = $transactionUsers->select('donation_id');
            $transactionUsers = $transactionUsers->with(['hasDonation.hasUser' => function ($query) {
                $query->select('id', 'name');
            }]);
            $transactionUsers = $transactionUsers->groupBy('donation_id');
            $transactionUsers = $transactionUsers->get();
            if (isset($transactionUsers) && $transactionUsers->count()) {
                foreach ($transactionUsers as $transactionUser) {
                    $transactionUsersArr = [];
                    $transactionUsersArr['id'] = $transactionUser->hasDonation->hasUser->id ?? 0;
                    $transactionUsersArr['user_name'] = $transactionUser->hasDonation->hasUser->name ?? '';
                    $response[] = $transactionUsersArr;
                }
                return response()->json([
                    'status_code' => 200, 'success' => true,
                    'message' => config('message.transactions.users_fetch_success'), 'data' => $response
                ], 200);
            }
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
}
