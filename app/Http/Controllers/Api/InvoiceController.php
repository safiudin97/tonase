<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;


class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'description' => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            //create invoice
            $create = Invoice::create([
                'user_id' => Auth::user()->id,
                'invoice_no' => date('ymdhis'),
                'amount' => $request->amount,
                'description' => $request->description,
                'date_of_invoice' => date('Y-m-d'),
                'time_of_invoice' => date('H:i:s'),
            ]);

            if ($create) {
                $status = 'success';
                $message = 'Invoice created successfully';
                $code = 200;  
            }else{
                $message = "Sorry, Failed to create invoice";
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }


    public function list()
    {
        

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;


        //get mutasi
        $invoice = Invoice::where('user_id', Auth::user()->id)->get();

        if (count($invoice) > 0) {

            $status = 'success';
            $message = 'Get invoice successfully';
            $data = $invoice->toArray();
            $code = 200;  
        }else{
            $message = "Sorry, Failed to get invoice";
        }
    

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
