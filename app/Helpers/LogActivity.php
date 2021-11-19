<?php

namespace App\Helpers;

use Request;
use App\Models\LogUser;
use Auth;

class LogActivity
{


    public static function addToLog($subject, $user_id = null)
    {
    	$log = [];
    	$log['subject'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['agent'] = Request::header('user-agent');
    	$log['user_id'] = ($user_id != null) ? $user_id : Auth::user()->id;
    	LogUser::create($log);
    }


}