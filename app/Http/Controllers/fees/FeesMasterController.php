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

                // 4. Pre-fetch classes that have ANY student assigned/linked to fees
                $classesWithAssignedStudents = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('class_type_id')
                    ->unique()
                    ->flip()
                    ->toArray();

                // 5. Pre-fetch classes that have fees collected for student(s)
                $collectedClassIdsFromAdmissions = DB::table('fees_detail')
                    ->join('admissions', 'admissions.id', '=', 'fees_detail.admission_id')
                    ->where('fees_detail.session_id', $sessionId)
                    ->where('fees_detail.branch_id', $branchId)
                    ->whereNull('fees_detail.deleted_at')
                    ->where('fees_detail.status', '!=', 2)
                    ->where(function($q) {
                        $q->where('fees_detail.paid_amount', '>', 0)
                          ->orWhere('fees_detail.total_amount', '>', 0);
                    })
                    ->pluck('admissions.class_type_id')
                    ->toArray();

                $collectedClassIdsFromAssign = DB::table('fees_detail')
                    ->join('fees_assign_details', function($join) {
                        $join->on('fees_assign_details.admission_id', '=', 'fees_detail.admission_id')
                             ->on('fees_assign_details.fees_group_id', '=', 'fees_detail.fees_group_id');
                    })
                    ->where('fees_detail.session_id', $sessionId)
                    ->where('fees_detail.branch_id', $branchId)
                    ->whereNull('fees_detail.deleted_at')
                    ->where('fees_detail.status', '!=', 2)
                    ->where(function($q) {
                        $q->where('fees_detail.paid_amount', '>', 0)
                          ->orWhere('fees_detail.total_amount', '>', 0);
                    })
                    ->pluck('fees_assign_details.class_type_id')
                    ->toArray();

                $classesWithCollectedFees = array_flip(array_unique(array_filter(array_merge($collectedClassIdsFromAdmissions, $collectedClassIdsFromAssign))));

                // 6. Calculate KPI statistics
                $totalClassesConfigured = $groupedByClass->count();
                $totalHeadsAssigned = $allFeesMasters->count();
                $totalProjectedAmount = $allFeesMasters->sum('amount');
                $inUseHeadsCount = 0;

                foreach ($allFeesMasters as $fm) {
                    $cId = $fm->class_type_id;
                    $gId = $fm->fees_group_id;
                    if (isset($classesWithAssignedStudents[$cId]) || isset($usedDetailGroups[$gId]) || isset($usedAssignPairs[$cId . '_' . $gId])) {
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
                    'classesWithAssignedStudents' => $classesWithAssignedStudents,
                    'classesWithCollectedFees' => $classesWithCollectedFees,
                    'stats' => $stats,
                    'masterFeesArray' => $masterFeesArray,
                    'dataview' => $fees_master_list,
                    'allData' => $allFeesMasters,
                    'feesGroupInstallmentsList' => $feesGroupInstallmentsList,
                ]);
            }

            public function feesMasterEdit(Request $request, $id){
                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');

                $classTypeId = $id;
                $datas = FeesMaster::where('class_type_id', $classTypeId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->get();

                if ($datas->isEmpty()) {
                    $singleMaster = FeesMaster::where('id', $id)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->first();
                    if ($singleMaster) {
                        $classTypeId = $singleMaster->class_type_id;
                        $datas = FeesMaster::where('class_type_id', $classTypeId)
                            ->where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->get();
                    }
                }

                // Guard: Check if fees have already been collected for any student of this class
                $hasCollectedFees = DB::table('fees_detail')
                    ->join('admissions', 'admissions.id', '=', 'fees_detail.admission_id')
                    ->where('admissions.class_type_id', $classTypeId)
                    ->where('fees_detail.session_id', $sessionId)
                    ->where('fees_detail.branch_id', $branchId)
                    ->whereNull('fees_detail.deleted_at')
                    ->where('fees_detail.status', '!=', 2)
                    ->where(function($q) {
                        $q->where('fees_detail.paid_amount', '>', 0)
                          ->orWhere('fees_detail.total_amount', '>', 0);
                    })
                    ->exists();

                if (!$hasCollectedFees) {
                    $hasCollectedFees = DB::table('fees_detail')
                        ->join('fees_assign_details', function($join) {
                            $join->on('fees_assign_details.admission_id', '=', 'fees_detail.admission_id')
                                 ->on('fees_assign_details.fees_group_id', '=', 'fees_detail.fees_group_id');
                        })
                        ->where('fees_assign_details.class_type_id', $classTypeId)
                        ->where('fees_detail.session_id', $sessionId)
                        ->where('fees_detail.branch_id', $branchId)
                        ->whereNull('fees_detail.deleted_at')
                        ->where('fees_detail.status', '!=', 2)
                        ->where(function($q) {
                            $q->where('fees_detail.paid_amount', '>', 0)
                              ->orWhere('fees_detail.total_amount', '>', 0);
                        })
                        ->exists();
                }

                if ($hasCollectedFees) {
                    return redirect::to('feesMasterAdd')->with('error', 'Cannot edit fee structure for this class because fees have already been collected for student(s).');
                }

                if ($request->isMethod('post')) {
                    for ($count = 0; $count < count($request->fees_group_id); $count++) {
                        $old_data = FeesMaster::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('fees_group_id', $request->fees_group_id[$count])
                            ->where('class_type_id', $request->class_type_id)
                            ->first();
                        if ($old_data != null) {
                            $data = $old_data;
                        } else {
                            $data = new FeesMaster;
                        }
                        $data->fees_group_id = $request->fees_group_id[$count];
                        $data->amount = $request->amount[$count];
                        $data->editable = $request->editable_value[$count] ?? 0;
                        $data->class_type_id = $request->class_type_id;
                        $data->session_id = $sessionId;
                        $data->branch_id = $branchId;
                        $data->save();
                    }
                    return redirect::to('feesMasterAdd')->with('message', 'Fees Record Updated Successfully !');
                }
                return view('fees.fees_master.feesMasterEdit', ['data' => $datas]);
            }

            public function feesMasterDelete(Request $request){
                $id = $request->delete_id;
                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');

                $feesMaster = FeesMaster::where('id', $id)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$feesMaster) {
                    return redirect::to('feesMasterAdd')->with('error', 'Fees record not found.');
                }

                // Guard: Check if any student in this class is assigned/linked to fees
                $classHasAssignedStudents = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where('class_type_id', $feesMaster->class_type_id)
                    ->whereNull('deleted_at')
                    ->exists();

                // Check if this specific fee head has student assignments
                $headHasAssignments = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where('fees_group_id', $feesMaster->fees_group_id)
                    ->whereNull('deleted_at')
                    ->exists();

                // Check if this fee group has any collected fee payments
                $headHasPayments = DB::table('fees_detail')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where('fees_group_id', $feesMaster->fees_group_id)
                    ->whereNull('deleted_at')
                    ->where('status', '!=', 2)
                    ->exists();

                if ($classHasAssignedStudents || $headHasAssignments || $headHasPayments) {
                    return redirect::to('feesMasterAdd')->with('error', 'Cannot delete this fee head because student(s) are already linked to this class fee structure.');
                }

                $feesMaster->delete();
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
