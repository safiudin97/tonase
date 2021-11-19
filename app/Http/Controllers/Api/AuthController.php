<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use App\Models\LogPasswordUser;
use App\Models\Ewallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'                      => 'required',
            'password'                      => 'required',
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            $user = User::where('email', '=', $request->email)->firstOrFail();
            if($user){
                if (Hash::check($request->password, $user->password)) {
                    $user->generateToken();
                    $status = 'success';
                    $message = 'Login sukses';
                    $data = $user->toArray();
                    $code = 200;  
                    \LogActivity::addToLog('Login.', $data['id']);
                }
                else{
                    $message = "Login gagal, password salah";
                }          
            }
            else{
                $message = "Login gagal, username salah";
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!$#%]).+$/',
        ],
        [
            'password.regex' => 'Password minimum 7 characters contains alphabet, numeric, symbol'
        ]);
        
        $status = "error";
        $message = "";
        $data = null;
        $code = 400;
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            if($user){

                //insert log previous password user
                LogPasswordUser::create([
                    'user_id' => $user->id,
                    'previous_password' => Hash::make($request->password),
                ]);

                //inset into balance user
                Ewallet::create([
                    'user_id'   => $user->id,
                ]);
                
                $status = "success";
                $message = "register successfully";
                $code = 200;
            }
            else{
                $message = 'register failed';
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'logout berhasil',
            'data' => []
        ], 200); 
    }
    
    public function resetPassword(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()]).+$/',
        ],
        [
            'new_password.regex' => 'Password minimum 7 characters contains alphabet, numeric, symbol'
        ]);

        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }else{
            //cek if new password same previous password
            $check = LogPasswordUser::where('user_id', Auth::user()->id)->get();
            foreach ($check as $key => $value) {
                if (Hash::check($request->new_password, $value->previous_password)) {
                    $message = "Sorry, Password cannot be the same as before";
                }else{
                    $user = User::where('id', Auth::user()->id)->first();
                    $user->password = Hash::make($request->new_password);
                    $user->save();
    
                    $status = 'success';
                    $message = 'Password changed successfully';
                    $code = 200;  
                }
            }
    
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
