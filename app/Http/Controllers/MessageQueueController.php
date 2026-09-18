<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\MessageQueue;

use Session;
use Hash;
use Helper;
use File;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageQueueController extends Controller

{
    
    //  public function message_queue(Request $request){
          
    //   $item = MessageQueue ::get();

    //     return view('message_queue.view',['data'=>$item]);
    // }
    
    
    public function message_queue(Request $request)
    {
        $query = MessageQueue::query();

        if (!empty($request->message_status) || $request->message_status === '0') {
            $query->where('message_status', $request->message_status);
        }

        $item = $query->orderBy('id', 'desc')->get();

        $search = [
            'message_status' => $request->message_status
        ];

        return view('message_queue.view', ['data' => $item, 'search' => $search]);
    }
  
}