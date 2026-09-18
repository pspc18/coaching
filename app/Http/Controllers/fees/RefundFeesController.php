<?php

namespace App\Http\Controllers\fees;

use Illuminate\Validation\Validator;
use App\Models\Setting;
use App\Models\fees\FeesCounter;
use App\Models\fees\FeesAdvance;
use App\Models\fees\FeesAdvanceHistory;
use App\Models\Admission;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RefundFeesController extends Controller

{
     
            public function RefundFees(Request $request)  {
              
               

                return view('fees.RefundFees.view');
            }
            
}
