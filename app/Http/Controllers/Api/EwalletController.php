<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ewallet;
use App\Models\EwalletTransaction;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;

class EwalletController extends Controller
{
    public function topup(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'nominal' => 'required|numeric',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            //update saldo user
            $ewallet = Ewallet::where('user_id', Auth::user()->id)->first();

            $previous_balance = $ewallet->balance;

            $ewallet->balance = $ewallet->balance + $request->nominal;

            if ($ewallet->save()) {

                //create ewallet transaction
                EwalletTransaction::create([
                    'user_id'             => Auth::user()->id,
                    'amount'               => $request->nominal,
                    'type_of_transaction' => 'topup',
                    'date_of_transaction' => date('Y-m-d'),
                    'time_of_transaction' => date('H:i:s'),
                    'last_balance' => $previous_balance + $request->nominal,
                ]);

                $status = 'success';
                $message = 'Topup successfully';
                $code = 200;  
            }else{
                $message = "Sorry, Topup failed";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }


    public function withdraw(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'nominal' => 'required|numeric',
            'bank_account_id' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{

            //cek bank account exist
            $bank_account = BankAccount::where('user_id', Auth::user()->id)->first();
            if($bank_account){
                //update saldo user
                $ewallet = Ewallet::where('user_id', Auth::user()->id)->first();

                $previous_balance = $ewallet->balance;

                $ewallet->balance = $ewallet->balance - $request->nominal;

                if ($ewallet->save()) {

                    //create ewallet transaction
                    EwalletTransaction::create([
                        'user_id'             => Auth::user()->id,
                        'amount'               => $request->nominal,
                        'type_of_transaction' => 'withdraw',
                        'date_of_transaction' => date('Y-m-d'),
                        'time_of_transaction' => date('H:i:s'),
                        'last_balance' => $previous_balance - $request->nominal,
                        'bank_account_id'     => $request->bank_account_id
                    ]);

                    $status = 'success';
                    $message = 'Withdraw successfully';
                    $code = 200;  
                }else{
                    $message = "Sorry, Withdraw failed";
                }
            }else{
                $message = "Sorry, please create bank account before withdraw";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }


    public function transfer(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'nominal' => 'required|numeric',
            'target_user' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{

            //cek target user exist
            $target_user = User::where('id', $request->target_user)->first();
            if($target_user){
                //update saldo user sender
                $ewallet = Ewallet::where('user_id', Auth::user()->id)->first();

                $previous_balance = $ewallet->balance;

                $ewallet->balance = $ewallet->balance - $request->nominal;

                if ($ewallet->save()) {

                    //create ewallet transaction sender
                    EwalletTransaction::create([
                        'user_id'             => Auth::user()->id,
                        'amount'               => $request->nominal,
                        'type_of_transaction' => 'transfer',
                        'date_of_transaction' => date('Y-m-d'),
                        'time_of_transaction' => date('H:i:s'),
                        'last_balance' => $previous_balance - $request->nominal,
                        'target_user'     => $request->target_user
                    ]);

                    //update balance target user
                    $ewallet_target_user = Ewallet::where('user_id', $request->target_user)->first();
                    $previous_balance_target_user = $ewallet_target_user->balance;
                    $ewallet_target_user->balance = $ewallet_target_user->balance + $request->nominal;
                    $ewallet_target_user->save();

                    //create ewallet transaction target user
                    EwalletTransaction::create([
                        'user_id'             => $request->target_user,
                        'amount'               => $request->nominal,
                        'type_of_transaction' => 'transfer',
                        'date_of_transaction' => date('Y-m-d'),
                        'time_of_transaction' => date('H:i:s'),
                        'last_balance' => $previous_balance_target_user + $request->nominal,
                        'from_user'     => Auth::user()->id
                    ]);

                    $status = 'success';
                    $message = 'Transfer successfully';
                    $code = 200;  
                }else{
                    $message = "Sorry, Transfer failed";
                }
            }else{
                $message = "Sorry, Target user not available";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function addbankAccount(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'holder_name' => 'required',
            'number_account' => 'required|numeric',
            'bank_name' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            //create bank account user
            $create = BankAccount::create([
                'user_id' => Auth::user()->id,
                'account_holder_name' => $request->holder_name,
                'account_number' => $request->number_account,
                'account_bank_name' => $request->bank_name,
            ]);

            if ($create) {
                $status = 'success';
                $message = 'Bank account created successfully';
                $code = 200;  
            }else{
                $message = "Sorry, Failed to create bank account";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }


    public function mutasi()
    {
        

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;


        //get mutasi
        $mutasi = EwalletTransaction::where('user_id', Auth::user()->id)->get();

        if (count($mutasi) > 0) {

            $result = [];

            foreach($mutasi as $key => $value){
                $result[] = [
                    "id"    => $value->id,
                    "amount" => $value->amount,
                    "type_of_transaction" => $value->type_of_transaction,
                    "date_of_transaction" => $value->date_of_transaction,
                    "time_of_transaction"    => $value->time_of_transaction,
                    "last_balance"    => $value->last_balance,
                ];
            }

            $status = 'success';
            $message = 'Get mutasi successfully';
            $data = $result;
            $code = 200;  
        }else{
            $message = "Sorry, Failed to get mutasi";
        }
    

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
