<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\ClassType;
use App\Models\Setting;
use App\Models\FeesMaster;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\Sessions;
use App\Models\Master\MessageTemplate;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use App\Models\FeesGroup;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use App\Models\FeesCollect;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\FeesDetail;
use Session;
use Hash;
use Helper;
use QrCode;
use Response;
use Str;
use PDF;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromoteController extends Controller

{
    
 
            public function promoteAdd(Request $request){
                $search['name'] = $request->name;
                $search['class_type_id'] = $request->class_type_id;
                $search['admissionNo'] = $request->admissionNo;
                if ($request->isMethod('post')) {
                    $data =  Admission::with('ClassTypes')->where('session_id',Session::get('session_id'))->where('status','1')->where('branch_id',Session::get('branch_id'));
                    
                   
                    if(!empty($request->name)){
                        $value = $request->name;
                        $data->where(function($query) use ($value){
                            $query->where("first_name", 'like', '%' .$value. '%');
                            $query->orWhere("father_name", 'like', '%' .$value. '%');
                            $query->orWhere("mother_name", 'like', '%' .$value. '%');
                            $query->orWhere("mobile", 'like', '%' .$value. '%');
                            $query->orWhere("email", 'like', '%' .$value. '%');
                            $query->orWhere("aadhaar", 'like', '%' .$value. '%');
                        });
                    }
                    if(!empty($request->admissionNo)){
                       $data = $data ->where("admissionNo", $request->admissionNo);
                    }           
                    if(!empty($request->class_type_id)){
                       $data = $data ->where("class_type_id", $request->class_type_id);
                    }  
                    $allstudents = $data->orderBy('first_name','ASC')->get();
                    // dd($allstudents);
                    $session = Sessions::all();
                    return  view('students/promote/promote',['search'=>$search,'data'=>$allstudents,'session'=>$session]);
                }
                return view('students/promote/promote',['search'=>$search,]);
            }  
            

           public function studentsPromoteAdd(Request $request){
    if ($request->isMethod('post')) {

        $request->merge(['roll_no' => (array) $request->roll_no]);

        $request->validate([
            'admission_ids' => 'required|array',
            'promote_class_type_id' => 'required',
            'session_id' => 'required',
            'date' => 'required|date',
        ]);

        if (!empty($request->admission_ids)) {

            $fees_master = $request->fees_master ? explode(',', $request->fees_master) : [];

            $data = FeesMaster::whereIn('id', $fees_master)
                ->where('class_type_id', $request->promote_class_type_id)
                ->where('session_id', $request->session_id)
                ->get();

            foreach ($request->admission_ids as $admission_id) {

                $newSession_Admission = null; // ✅ FIX

                $candidate = Admission::find($admission_id);
                if (!$candidate) {
                    continue;
                }

                $isAlreadyPromoted = Admission::where('admissionNo', $candidate->admissionNo)
                    ->where('first_name', $candidate->first_name)
                    ->where('father_name', $candidate->father_name)
                    ->where('mobile', $candidate->mobile)
                    ->where('class_type_id', $request->promote_class_type_id)
                    ->where('session_id', $request->session_id)
                    ->first();

                if (empty($isAlreadyPromoted)) {

                    $BillCounter = BillCounter::where('type', 'Admission')
                        ->where('session_id', $request->session_id)
                        ->first();

                    if ($BillCounter) {
                        $BillCounter->counter = ($BillCounter->counter ?? 0) + 1;
                        $BillCounter->save();
                    }

                    $oldRow = Admission::find($admission_id);
                    $newRow = $oldRow->replicate();
                    $newRow->session_id = $request->session_id;

                    $promoteStatus = (int) ($request->promote_status[$admission_id] ?? 1);
                    if ($promoteStatus === 2) {
                        // Running in same class
                        $newRow->class_type_id = $oldRow->class_type_id;
                    } else {
                        // Promoted to next class
                        $newRow->class_type_id = $request->promote_class_type_id;
                    }

                    $newRow->roll_no = $request->roll_no[$admission_id] ?? null;
                    $newRow->admission_date = $request->date ?: date('Y-m-d');
                    $newRow->promote_date = null;
                    $newRow->save();

                    $oldRow->promote_date = $request->date ?: date('Y-m-d');
                    $oldRow->save();
                    $newSession_Admission = $newRow->id;

                    if ($oldRow->admission_type_id != 2) {
                        if ($data->isNotEmpty()) {

                            $feesGroup = new FeesAssign();
                            $feesGroup->user_id = Session::get('id');
                            $feesGroup->session_id = $request->session_id;
                            $feesGroup->branch_id = Session::get('branch_id');
                            $feesGroup->admission_id = $newRow->id;
                            $feesGroup->save();

                            $feesGroupAmount = 0;
                            foreach ($data as $value) {
                                $feesGroupDetail = new FeesAssignDetail();
                                $feesGroupDetail->user_id = Session::get('id');
                                $feesGroupDetail->session_id = $request->session_id;
                                $feesGroupDetail->branch_id = Session::get('branch_id');
                                $feesGroupDetail->fees_group_id = $value->fees_group_id;
                                $feesGroupDetail->fees_master_id = $value->id;
                                $feesGroupDetail->fees_group_amount = $value->amount ?? 0;
                                $feesGroupDetail->fees_assign_id = $feesGroup->id;
                                $feesGroupDetail->admission_id = $newRow->id;
                                $feesGroupDetail->save();

                                $feesGroupAmount += $value->amount ?? 0;
                            }

                            $feesGroup->total_amount = $feesGroupAmount;
                            $feesGroup->net_amount = $feesGroupAmount;
                            $feesGroup->save();
                        }
                    }
               

                /* ================= CARRY FORWARD ================= */

                $carryForwardIds = $request->input('carry_forward_ids', []);

                if (!$newSession_Admission) {
                    continue;
                }

                if (in_array($admission_id, $carryForwardIds)) {

                    $fees_details_id = [];
                    $totalPending = 0;

                    $BillCounter = BillCounter::where('session_id', Session::get('session_id'))
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('type', 'FeesSlip')
                        ->first();

                    if (!$BillCounter) {
                        $BillCounter = new BillCounter();
                        $BillCounter->session_id = Session::get('session_id');
                        $BillCounter->branch_id = Session::get('branch_id');
                        $BillCounter->type = 'FeesSlip';
                        $BillCounter->counter = 0;
                        $BillCounter->save();
                    }

                    $groups = FeesAssignDetail::where('admission_id', $admission_id)->get();

                    foreach ($groups as $head) {

                        $paid = FeesDetail::where('admission_id', $admission_id)
                            ->where('fees_group_id', $head->fees_group_id)
                            ->sum('total_amount');

                        $pending = $head->fees_group_amount - $paid;
                        if ($pending <= 0) continue;

                        $totalPending += $pending;

                        $pay = FeesCollect::where('admission_id', $admission_id)->first();
                        if ($pay) {
                            $pay->amount += $pending;
                            $pay->save();
                        } else {
                            $pay = new FeesCollect();
                            $pay->user_id = Session::get('id');
                            $pay->session_id = Session::get('session_id');
                            $pay->branch_id = Session::get('branch_id');
                            $pay->admission_id = $admission_id;
                            $pay->amount = $pending;
                            $pay->save();
                        }

                        $BillCounter->counter += 1;
                        $BillCounter->save();

                        $payDetail = new FeesDetail();
                        $payDetail->user_id = Session::get('id');
                        $payDetail->session_id = Session::get('session_id');
                        $payDetail->branch_id = Session::get('branch_id');
                        $payDetail->fees_collect_id = $pay->id;
                        $payDetail->fees_group_id = $head->fees_group_id;
                        $payDetail->receipt_no = $BillCounter->counter;
                        $payDetail->admission_id = $admission_id;
                        $payDetail->paid_amount = $pending;
                        $payDetail->total_amount = $pending;
                        $payDetail->payment_mode_id = 1;
                        $payDetail->status = 2;
                        $payDetail->date = date('Y-m-d');
                        $payDetail->save();

                        $fees_details_id[] = $payDetail->id;
                    }

                    if (!empty($fees_details_id)) {

                        $invoice = new FeesDetailsInvoices();
                        $invoice->user_id = Session::get('id');
                        $invoice->session_id = Session::get('session_id');
                        $invoice->branch_id = Session::get('branch_id');
                        $invoice->admission_id = $admission_id;
                        $invoice->fees_details_id = implode(',', $fees_details_id);
                        $invoice->payment_date = date('Y-m-d');
                        $invoice->payment_mode = 1;
                        $invoice->invoice_no = $BillCounter->counter;
                        $invoice->status = 2;
                        $invoice->amount = $totalPending;
                        $invoice->remark = 'Carry Forward Fees';
                        $invoice->save();
                    }
              
                
                /* ================= Carry Forward Group ================= */
                            $fees_group_list = FeesGroup::where('session_id', $request->session_id)
                                ->where('branch_id', Session::get('branch_id'))
                                ->where('name', 'Carry Forward Amount')
                                ->first();
                    
                            if (!$fees_group_list) {
                                $fees_group_list = new FeesGroup();
                                $fees_group_list->user_id     = Session::get('id');
                                $fees_group_list->session_id  = $request->session_id;
                                $fees_group_list->branch_id   = Session::get('branch_id');
                                $fees_group_list->name        = 'Carry Forward Amount';
                                $fees_group_list->fees_refund = 'no';
                                $fees_group_list->save();
                            }
                    
                            $fees_master = FeesMaster::where('session_id', $request->session_id)
                                ->where('branch_id', Session::get('branch_id'))
                                ->where('class_type_id', $request->promote_class_type_id)
                                ->where('fees_group_id', $fees_group_list->id)
                                ->first();
                    
                            if (!$fees_master) {
                                $fees_master = new FeesMaster();
                                $fees_master->user_id       = Session::get('id');
                                $fees_master->session_id    = $request->session_id;
                                $fees_master->branch_id     = Session::get('branch_id');
                                $fees_master->fees_group_id = $fees_group_list->id;
                                $fees_master->amount        = 0;
                                $fees_master->editable      = 0;
                                $fees_master->class_type_id = $request->promote_class_type_id;
                                $fees_master->save();
                            }
                    
                            $feesGroup = new FeesAssign();
                            $feesGroup->user_id       = Session::get('id');
                            $feesGroup->session_id    = $request->session_id;
                            $feesGroup->branch_id     = Session::get('branch_id');
                            $feesGroup->admission_id  = $newSession_Admission;
                            $feesGroup->total_amount  = $totalPending;
                            $feesGroup->net_amount    = $totalPending;
                            $feesGroup->save();
                    
                            $feesGroupDetail = new FeesAssignDetail();
                            $feesGroupDetail->user_id           = Session::get('id');
                            $feesGroupDetail->session_id        = $request->session_id;
                            $feesGroupDetail->branch_id         = Session::get('branch_id');
                            $feesGroupDetail->fees_group_id     = $fees_group_list->id;
                            $feesGroupDetail->class_type_id    = $request->promote_class_type_id;
                            $feesGroupDetail->fees_master_id    = $fees_master->id;
                            $feesGroupDetail->fees_group_amount = $totalPending;
                            $feesGroupDetail->fees_assign_id    = $feesGroup->id;
                            $feesGroupDetail->admission_id      = $newSession_Admission;
                            $feesGroupDetail->save();
                }             
                                            
                }           
            }

            return response()->json(['success' => true, 'message' => 'Students promoted successfully.']);
        }
    }

    return response()->json(['success' => false, 'message' => 'Invalid request.'], 400);
}


public function getClassBySession(Request $request)
{
    $classes = ClassType::where('session_id', $request->session_id)
                          ->where('branch_id',Session::get('branch_id'))
        ->select('id', 'name')
        ->get();

    return response()->json([
        'data' => $classes
    ]);
}

            
}
