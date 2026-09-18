<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\MessFeesStrucher;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelMessFeesStrucherController extends Controller

{


public function messFeesStrucher(Request $request)
{
    if ($request->isMethod('post')) {
        $classNames = $request->class_name;
        
        foreach ($classNames as $count => $className) {
            if (isset($request->vag_fees[$count]) && isset($request->nonvag_fees[$count])) {
                $oldData = MessFeesStrucher::where('class_name', $className)->first();

                if (!$oldData) {
                    $hostel = new MessFeesStrucher();
                } else {
                    $hostel = $oldData;
                }

                $hostel->vag_fees = $request->vag_fees[$count];
                $hostel->class_name = $className;
                $hostel->nonvag_fees = $request->nonvag_fees[$count];
                $hostel->save();
            }
        }

        return redirect::to('messFeesStrucher')->with('message', 'Mess Fees Strucher Updated Successfully!');
    }

    return view('hostel.mess_fees_Strucher.add');
}

    
   
}
























