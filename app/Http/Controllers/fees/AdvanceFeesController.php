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

class AdvanceFeesController extends Controller

{
     
            public function AdvanceFees(Request $request)  {
                
                
                $serach['class_type_id'] = !empty($request->class_type_id) ? $request->class_type_id : 0;
              
                $data =  Admission::select('admissions.*','class.name as class_name','users.first_name as users_first_name'
                ,'users.last_name as users_last_name','fees_advances.balance')
                ->leftjoin('fees_advances', 'fees_advances.unique_system_id', 'admissions.unique_system_id')
                ->leftjoin('class_types as class','class.id','admissions.class_type_id')
                ->leftjoin('users','users.id','fees_advances.user_id')
                ->where('admissions.branch_id', Session::get('branch_id'));
                if ($request->isMethod('post')) {
                    if (!empty($request->class_type_id)) {
                        $data = $data->where("admissions.class_type_id", $request->class_type_id);
                    }
                
                }
                $data = $data->groupBy('unique_system_id')->orderBy('fees_advances.id', 'DESC')->get();

                return view('fees.advance_fees.view',['serach' => $serach,'data'=>$data]);
            }
         
            
          

            public function AddAdvanceFees(Request $request)
{
    if ($request->isMethod('post')) {
        // Fetch existing advance fees record
        $existingData = FeesAdvance::where('unique_system_id',$request->unique_system_id)->first();

        // Determine balance and set data object
        $balance = $request->amount;
        if (!empty($existingData)) {
            $advanca = $existingData;
            $balance = ($request->debit_credit == 'credit') 
                ? $existingData->balance + $request->amount 
                : $existingData->balance - $request->amount;
        } else {
            $advanca = new FeesAdvance;
        }

        // Populate and save FeesAdvance record
        $advanca->user_id = Session::get('id');
        $advanca->unique_system_id = $request->unique_system_id;
        $advanca->session_id = Session::get('session_id');
        $advanca->branch_id = Session::get('branch_id');
        $advanca->date = $request->date;
        $advanca->balance = $balance;
        $advanca->save();

        // Create and save FeesAdvanceHistory record
        $advancahistory = new FeesAdvanceHistory;
        $advancahistory->{$request->debit_credit} = $request->amount; // Dynamic assignment
        $advancahistory->user_id = Session::get('id');
        $advancahistory->session_id = Session::get('session_id');
        $advancahistory->unique_system_id = $request->unique_system_id;
        $advancahistory->branch_id = Session::get('branch_id');
        $advancahistory->date = $request->date;
        $advancahistory->details = $request->details;
        $advancahistory->fees_advance_id = $advanca->id;
        $advancahistory->save();

        return redirect('AdvanceFees')->with('message', 'Advance Fees Added Successfully!');
    }

    return redirect('AdvanceFees');
}

            
        public function viewAdvanceFees (Request $request)  {
                        $getAdvance = FeesAdvanceHistory::select('fees_advance_historys.*')
                        ->where('unique_system_id',$request->unique_system_id)
                        ->get();
                
                    $html = '<table class=" table table-bordered table-striped dataTable ">
                        <thead>
                            <tr class="sky_tr">
                                <th>#</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Credit</th>
                                <th>Debit</th>
                            </tr>
                        </thead>
                        <tbody>';
                
                    if (!$getAdvance->isEmpty()) {
                        $i = 1;
                       
                
                        foreach ($getAdvance as $item) {
                          
    
                                   
                        
                
                            $html .= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . ($item->details ?? '') . '</td>
                                <td>' . (!empty($item->date) ? date('d-M-Y', strtotime($item->date)) : '') . '</td>
                                <td>' . ($item->credit ?? '') . '</td>
                                <td>' . ($item->debit ?? '') . '</td>
                            </tr>';
                
                           
                        }
                
                      
                    } else {
                        $html .= '<tr class="text-center">
                            <td colspan="9"><b>!! NO DATA FOUND !!</b></td>
                        </tr>';
                    }
                
                    $html .= '</tbody></table>';
                
                    return response()->json(['html' => $html]);
                    }

            
}
