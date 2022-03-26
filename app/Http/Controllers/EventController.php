<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function handle(Request $request){        
        DB::beginTransaction();
        
        try {
            switch($request->type){
                case 'deposit':
                    $response = $this->deposit($request);
                    break;
                case 'withdraw':
                    $response = $this->withdraw($request);
                    break;
                case 'transfer':
                    $response = $this->transfer($request);
                    break;
                default:
                    throw new Exception("0", 400);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(intval($e->getMessage()), $e->getCode());
        }

        DB::commit();
        return $response;
    }

    private function deposit(Request $request){
        $validator = Validator::make($request->all(), [
            'account_to' => 'required|integer',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) throw new Exception("0", 400);

        $transaction = Transaction::createTransaction([
            'type' => 'deposit',
            'value' => $request->value,
            'account_id' => $request->account_to
        ]);

        $account_to = $transaction->account_to;
        
        return response()->json([
            'account_to' => $account_to->getInfo()
        ], 201);
    }

    private function withdraw(Request $request){
        $validator = Validator::make($request->all(), [
            'account_from' => 'required|integer',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) throw new Exception("0", 400);

        $transaction = Transaction::createTransaction([
            'type' => 'withdraw',
            'value' => $request->value,
            'account_id' => $request->origin
        ]);

        $account_from = $transaction->account_from;
        
        return response()->json([
            'account_from' => $account_from->getInfo()
        ], 201);
    }

    private function transfer(Request $request){
        $validator = Validator::make($request->all(), [
            'account_to' => 'required|integer',
            'account_from' => 'required|integer',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) throw new Exception("0", 400);

        $transfer = Transfer::createTransfer([
            'value' => $request->value,
            'account_from' => $request->account_from,
            'account_to' => $request->account_to,
        ]);

        $account_from = $transfer->from;
        $account_to = $transfer->to;
        return response()->json([
            'account_from' => $account_from->getInfo(),
            'account_to' => $account_to->getInfo()
        ], 201);
    }
}
