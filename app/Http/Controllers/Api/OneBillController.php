<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Invoice;
use App\Models\EwalletTransaction;
use App\Models\OneBill;
use App\Models\Ewallet;
use PDF;

class OneBillController extends Controller
{
    public function create(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'invoice' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{

            $billing_number = \App::generateRandomString(15);

            //create billing id
            $create_bill = OneBill::create([
                'user_id' => Auth::user()->id,
                'billing_no' => $billing_number,
            ]);

            $total_amount = 0;
            foreach ($request->invoice as $invoice_id) {
                //update billing id in invoice
                $update_invoice = Invoice::where('id', $invoice_id)->where('is_payment','n')->first();
                if($update_invoice){
                    $total_amount += $update_invoice->amount;
                    $update_invoice->billing_id = $create_bill->id;
                    $update_invoice->save();
                }
            }

            if ($create_bill) {

                //update total_amount
                $update_total_amount = OneBill::where('id', $create_bill->id)->first();
                $update_total_amount->total_amount = $total_amount;
                $update_total_amount->save();

                $result = [
                    'billing_number' => $billing_number
                ];
                $status = 'success';
                $message = 'Billing ID created successfully';
                $data = $result;
                $code = 200;  
            }else{
                $message = "Sorry, Failed to create Billing ID";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function payment(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'billing_number' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{

            $billing_number = $request->billing_number;

            //set one billing to is payment
            $one_billing = OneBill::where('billing_no', $billing_number)->first();
            $one_billing->is_payment = 'y';
            $one_billing->payment_date = date('Y-m-d');
            $one_billing->payment_time = date('H:i:s');
            
            if($one_billing->save()){
                //update invoice is payment to yes
                $update = [
                    'is_payment' => 'y'
                ];
                Invoice::where('billing_id', $one_billing->id)->update($update);

                //update balance user
                $ewallet_user = Ewallet::where('user_id', Auth::user()->id)->first();
                $previous_balance_user = $ewallet_user->balance;
                // cek saldo user
                if($ewallet_user->balance < $one_billing->total_amount){
                    $message = "Sorry, your balance not enough, please topup first";
                }else{
                    $ewallet_user->balance = $ewallet_user->balance - $one_billing->total_amount;
                    $ewallet_user->save();
    
                    //create ewallet transaction user
                    EwalletTransaction::create([
                        'user_id'             => Auth::user()->id,
                        'amount'               => $one_billing->total_amount,
                        'type_of_transaction' => 'one_billing',
                        'date_of_transaction' => date('Y-m-d'),
                        'time_of_transaction' => date('H:i:s'),
                        'last_balance' => $previous_balance_user - $one_billing->total_amount,
                        'billing_id'     => $one_billing->id
                    ]);


                    $render['billing'] = $one_billing;
                    $billing_detail = Invoice::where('billing_id', $one_billing->id)->get();
                    $render['billing_detail'] = $billing_detail;
                      
                    $path = public_path('billing/');
                    $filename =  Auth::user()->name .'-'. $one_billing->billing_no . '.' . 'pdf' ;

                    
                    PDF::loadView('billing', $render)->save($path . $filename);

                    $document = [
                        'document' => $path . $filename
                    ];
    
                    $status = 'success';
                    $message = 'Payment successfully';
                    $data = $document;
                    $code = 200; 
                }
                
            }else{
                $message = "Sorry, Failed to payment";
            }
            
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
