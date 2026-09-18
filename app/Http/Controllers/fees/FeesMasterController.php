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
use Illuminate\Support\Facades\DB;

class FeesMasterController extends Controller

{



            public function feesMaster(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'class_type_id' => 'required',
                    ]);
                    if (!empty($request->fees_group_id)) {
                        foreach ($request->fees_group_id as $key => $fees_group_id) {
                            $oldData = FeesMaster::where('session_id', Session::get('session_id'))
                                ->where('branch_id', Session::get('branch_id'))
                                ->where('class_type_id', $request->class_type_id)
                                ->where('fees_group_id', $fees_group_id)
                                ->first();
                            if (empty($oldData)) {
                                $fees_master = new FeesMaster;
                                $fees_master->user_id = Session::get('id');
                                $fees_master->session_id = Session::get('session_id');
                                $fees_master->branch_id = Session::get('branch_id');
                                $fees_master->fees_group_id = $fees_group_id;
                                $fees_master->amount = $request->amount[$fees_group_id] ?? 0;
                                $fees_master->installment_due_date = $request->installment_due_date[$fees_group_id] ?? null;
                                $fees_master->editable = $request->editable_value[$fees_group_id] ?? 0;
                                $fees_master->class_type_id = $request->class_type_id;
                                $fees_master->save();
                            } else {
                                continue;
                            }
                        }
                    }
                    return redirect::to('feesMasterAdd')->with('message', 'Fees Record Added Successfully !');
                }

                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');

                // 1. Single query eager-loading all FeesMaster records with feesGroup and ClassTypes
                $allFeesMasters = FeesMaster::with(['feesGroup', 'ClassTypes'])
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->orderBy('class_type_id', 'ASC')
                    ->get();

                // Group records by class_type_id
                $groupedByClass = $allFeesMasters->groupBy('class_type_id');

                // 2. Pre-fetch in-use fees_group_ids from fees_detail for O(1) in-memory lookup
                $usedDetailGroups = DB::table('fees_detail')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('fees_group_id')
                    ->flip()
                    ->toArray();

                // 3. Pre-fetch in-use (class_type_id + fees_group_id) pairs from fees_assign_details for O(1) in-memory lookup
                $usedAssignPairs = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->select('class_type_id', 'fees_group_id')
                    ->get()
                    ->mapWithKeys(function($row) {
                        return [$row->class_type_id . '_' . $row->fees_group_id => true];
                    })
                    ->toArray();

                // 4. Calculate KPI statistics
                $totalClassesConfigured = $groupedByClass->count();
                $totalHeadsAssigned = $allFeesMasters->count();
                $totalProjectedAmount = $allFeesMasters->sum('amount');
                $inUseHeadsCount = 0;

                foreach ($allFeesMasters as $fm) {
                    $cId = $fm->class_type_id;
                    $gId = $fm->fees_group_id;
                    if (isset($usedDetailGroups[$gId]) || isset($usedAssignPairs[$cId . '_' . $gId])) {
                        $inUseHeadsCount++;
                    }
                }

                $stats = [
                    'total_classes' => $totalClassesConfigured,
                    'total_heads' => $totalHeadsAssigned,
                    'total_amount' => $totalProjectedAmount,
                    'in_use_heads' => $inUseHeadsCount,
                ];

                $masterFeesArray = [];
                foreach ($groupedByClass as $cId => $fms) {
                    $masterFeesArray[$cId] = $fms->map(function($fm) {
                        return $fm->feesGroup->name ?? '';
                    })->filter()->values()->toArray();
                }

                // For compatibility with existing modals and components
                $fees_master_list = $allFeesMasters->unique('class_type_id')->values();
                $feesGroupInstallmentsList = FeesGroup::where('fees_type', 'installment')->get();

                return Helper::view('fees.fees_master.feesMaster', [
                    'allFeesMasters' => $allFeesMasters,
                    'groupedByClass' => $groupedByClass,
                    'usedDetailGroups' => $usedDetailGroups,
                    'usedAssignPairs' => $usedAssignPairs,
                    'stats' => $stats,
                    'masterFeesArray' => $masterFeesArray,
                    'dataview' => $fees_master_list,
                    'allData' => $allFeesMasters,
                    'feesGroupInstallmentsList' => $feesGroupInstallmentsList,
                ]);
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
