<?php

namespace App\Http\Controllers\fees;

use Illuminate\Validation\Validator;
use App\Models\Student;
use App\Models\StudentFees;
use App\Models\ClassType;
use App\Models\Master\Section;
use App\Models\Admission;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Account;
use App\Models\FeesStructure;
use App\Models\FeesType;
use App\Models\FeesGroup;
use App\Models\FeesMaster;
//use App\Models\FeesAssign;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use App\Models\FeesDiscount;
use App\Models\FeesCollect;
use App\Models\FeesReminder;
use App\Models\FeesDetail;
use App\Models\Setting;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesMasterController extends Controller

{



            public function feesMaster(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        //'fees_group_id' => 'required',
                        //'amount' => 'required',
                        'class_type_id' => 'required',
                    ]);
                   // dd($request->amount[$key]);
                    foreach ($request->fees_group_id as $key => $fees_group_id) {
                        $oldData = FeesMaster::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))
                        ->where('class_type_id', $request->class_type_id)
                        ->where('fees_group_id',$fees_group_id)->first();
                        if(empty($oldData)){
                           
                            $fees_master = new FeesMaster; //model name
                            $fees_master->user_id = Session::get('id');
                            $fees_master->session_id = Session::get('session_id');
                            $fees_master->branch_id = Session::get('branch_id');
                            $fees_master->fees_group_id = $fees_group_id;
                            $fees_master->amount = $request->amount[$fees_group_id];
                            $fees_master->installment_due_date = $request->installment_due_date[$fees_group_id];
                            $fees_master->editable = $request->editable_value[$fees_group_id];
                            $fees_master->class_type_id = $request->class_type_id;
                            $fees_master->save();
                        }
                        else{
                            //return redirect::to('feesMasterAdd')->with('error', 'Already Assigned !');
                             continue;
                        }
                    }
                    return redirect::to('feesMasterAdd')->with('message', 'Fees Record Added Successfully !');
                }
                $fees_master_list = FeesMaster::with('feesGroup')->with('ClassTypes')->where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->groupBy('class_type_id')->get();
        $all_data = FeesMaster::with('feesGroup')->with('ClassTypes')->where('session_id', Session::get('session_id'));
      
     
                        $feesGroupInstallmentsList = FeesGroup::where('fees_type','installment')->get();

                return view('fees.fees_master.feesMaster', ['feesGroupInstallmentsList'=>$feesGroupInstallmentsList,'dataview' => $fees_master_list,'allData'=>$all_data]);
            }

            public function feesMasterEdit(Request $request, $id){
                $datas = FeesMaster::where('class_type_id', $id)
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))->get();
                if ($request->isMethod('post')) {
                    for ($count = 0; $count < count($request->fees_group_id); $count++) {
                        $old_data = FeesMaster::where('session_id', Session::get('session_id'))
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('fees_group_id',$request->fees_group_id[$count])
                        ->where('class_type_id',$request->class_type_id)
                        ->first();
                        if($old_data != null){
                            $data = $old_data;
                        }else{
                            $data = new FeesMaster;
                        }
                        $data->fees_group_id = $request->fees_group_id[$count];
                        $data->amount = $request->amount[$count];
                        $data->editable = $request->editable_value[$count];
                        $data->class_type_id = $request->class_type_id;
                        $data->save();
                    }
                    return redirect::to('feesMasterAdd')->with('message', 'Fees Record Updated Successfully !');
                }
                return view('fees.fees_master.feesMasterEdit', ['data' => $datas]);
            }

            public function feesMasterDelete(Request $request){
                $id = $request->delete_id;
                $feesMaster = FeesMaster::find($id)->delete();
                return redirect::to('feesMasterAdd')->with('message', 'Fees Record Deleted Successfully !');
            }

            public function feesMasterData(Request $request){
                $data =  FeesMaster::find($request->fees_master_id);
                $paidAmount =  FeesDetail::where('class_type_id', $request->class_type_id)->where('fees_type_id', $data['fees_type_id'])->sum('total_amount');
                // dd($request);
                if ($paidAmount > 0) {
                    $net_amount =  $data['amount'] - $paidAmount;
                } else {
                    $net_amount = $data['amount'];
                }
                echo json_encode($net_amount);
            }

            public function mesterClassAmt(Request $request){
                // dd($request);
                $data =  FeesMaster::where('class_type_id',$request->class_type_id)->where('session_id', Session::get('session_id'))->get();
                $feesAssign = '';
                $admission_id = '';
                if(!empty($request->admission_id)){
                    $feesAssign = FeesAssign::where('admission_id',$request->admission_id)->first();
                    $admission_id = $request->admission_id;
                }
                if (count($data) > 0) {
                    return view('fees.fees_master.mesterClassAmt', ['data' => $data, 'feesAssign'=>$feesAssign, 'admission_id'=>$admission_id]);
                } else {
                    return null;
                }
            }
}
