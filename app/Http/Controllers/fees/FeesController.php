<?php
 
namespace App\Http\Controllers\fees;

use Illuminate\Validation\Validator;
use App\Models\Student;
use App\Models\ClassType;
use App\Models\Admission;
use App\Models\Notification;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\Account;
use App\Models\FeesStructure;
use App\Models\FeesGroup;
use App\Models\FeesMaster;
use App\Models\FeesDiscount;
use App\Models\FeesCollect;
use App\Models\Sessions;
use App\Models\PermissionMessages;
use PDF;
use App\Models\fees\FeesAdvance;
use App\Models\fees\FeesAdvanceHistory;
use App\Models\FeesDetail;
use App\Models\Invoice;
use App\Models\StoreItem;
use App\Models\StoreItemRequest;
use App\Models\StoreBillingDetail;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Master\PaymentMode;
use App\Models\Setting;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\fees\FeesAssignDetail;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Response;
use Auth;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\Controllers\WhatsappController;

class FeesController extends Controller

{
            private function createFeeNotification(
                int $admissionId,
                int $branchId,
                int $sessionId,
                string $title,
                string $body,
                string $type
            ): Notification {
                return Notification::create([
                    'title' => $title,
                    'content' => $body,
                    'type' => $type,
                    'admission_id' => $admissionId,
                    'user_id' => null,
                    'device_token' => null,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'message_seen' => 0,
                    'show_status' => 1,
                ]);
            }

            private function sendFeePush(
                int $admissionId,
                string $title,
                string $body,
                string $type,
                array $extraData = []
            ): array {
                try {
                    return Helper::sendNotification(
                        $title,
                        $body,
                        'student',
                        $admissionId,
                        null,
                        null,
                        array_merge([
                            'type' => $type,
                            'notification_type' => 'default',
                            'channel_id' => 'default',
                            'channelId' => 'default',
                        ], $extraData)
                    );
                } catch (\Throwable $e) {
                    Log::error('Fee push notification failed.', [
                        'admission_id' => $admissionId,
                        'notification_type' => $type,
                        'error' => $e->getMessage(),
                    ]);

                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            private function feeAmount(float $amount): string
            {
                return '₹' . number_format($amount, 2);
            }


    
    
            public function FeesGroupRemoveDuplicateEntries(){
                    $allEntries = FeesAssignDetail::where('session_id', Session::get('session_id'))
                        ->where('branch_id', Session::get('branch_id'))
                        ->orderBy('admission_id')
                        ->orderBy('fees_group_id')
                        ->orderBy('id') // oldest entry first
                        ->get()
                        ->groupBy('admission_id');
                
                    $deletedCount = 0;
                
                    foreach ($allEntries as $admission_id => $entries) {
                        $groupTracker = [];
                        $duplicateFeesAssignIds = [];
                
                        foreach ($entries as $entry) {
                            if (in_array($entry->fees_group_id, $groupTracker)) {
                                $duplicateFeesAssignIds[] = $entry->id; // duplicate
                            } else {
                                $groupTracker[] = $entry->fees_group_id;
                            }
                        }
                
                        // Delete duplicate entries
                        if (!empty($duplicateFeesAssignIds)) {
                            FeesAssignDetail::whereIn('id', $duplicateFeesAssignIds)->delete();
                            $deletedCount += count($duplicateFeesAssignIds);
                        }
                    }
                
                    //echo "✅ $deletedCount duplicate fee group entries deleted from all students.";
                }

            public function feeDashboard(){
                return view('fees/fee_dashboard');
            }
  
            public function addFees(Request $request){
                $searchTypes = [
                    'first_name',
                    'admissionNo',
                    'father_name',
                    'mother_name',
                    'mobile',
                    'aadhaar',
                    'jan_aadhaar',
                    'address',
                ];

                $search = [
                    'admission_no' => trim((string) $request->admission_no),
                    'name' => trim((string) $request->name),
                    'search_type' => $request->search_type ?? '',
                    'admission_type_id' => $request->admission_type_id ?? '',
                    'class_type_id' => $request->class_type_id ?? '',
                ];

                if ($request->ajax() || $request->has('ajax_search')) {
                    $query = Admission::with('ClassTypes')
                        ->where('status', 1)
                        ->where('session_id', Session::get('session_id'))
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('school', 1);

                    if (!empty($search['admission_no'])) {
                        $query->where('admissionNo', 'LIKE', '%'.$search['admission_no'].'%');
                    }

                    if (!empty($search['class_type_id'])) {
                        $query->where('class_type_id', $search['class_type_id']);
                    }

                    if (!empty($search['admission_type_id'])) {
                        $query->where('admission_type_id', $search['admission_type_id']);
                    }

                    if (!empty($search['name'])) {
                        $keyword = $search['name'];
                        if (!empty($search['search_type']) && in_array($search['search_type'], $searchTypes)) {
                            $query->where($search['search_type'], 'LIKE', '%'.$keyword.'%');
                        } else {
                            $query->where(function ($q) use ($keyword) {
                                $q->where('admissionNo', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('ledger_no', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('first_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ['%'.$keyword.'%'])
                                    ->orWhere('father_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('mother_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('mobile', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('father_mobile', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('aadhaar', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('jan_aadhaar', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('address', 'LIKE', '%'.$keyword.'%');
                            });
                        }
                    }

                    $students = $query
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->limit(250)
                        ->get();

                    return response()->json([
                        'status' => 'success',
                        'count' => $students->count(),
                        'students' => $students->map(function ($s) {
                            return [
                                'id' => $s->id,
                                'unique_system_id' => $s->unique_system_id,
                                'admissionNo' => $s->admissionNo,
                                'first_name' => $s->first_name,
                                'last_name' => $s->last_name,
                                'full_name' => trim(($s->first_name ?? '').' '.($s->last_name ?? '')),
                                'father_name' => $s->father_name ?? '',
                                'mother_name' => $s->mother_name ?? '',
                                'mobile' => $s->mobile ?: ($s->father_mobile ?: '-'),
                                'class_name' => $s->ClassTypes->name ?? 'N/A',
                                'class_type_id' => $s->class_type_id,
                                'admission_type_id' => $s->admission_type_id,
                                'image' => $s->image ?? '',
                            ];
                        }),
                    ]);
                }

                if ($request->isMethod('post')) {
                    $request->validate([
                        'class_type_id' => 'nullable|integer|exists:class_types,id',
                        'admission_type_id' => 'nullable|in:1,2',
                        'search_type' => 'nullable|in:'.implode(',', $searchTypes),
                        'name' => [
                            'nullable',
                            'string',
                            'max:100',
                            function ($attribute, $value, $fail) use ($request) {
                                if (!empty($request->search_type) && trim((string) $value) === '') {
                                    $fail('Search keyword is required when a search type is selected.');
                                }
                            },
                        ],
                    ]);

                    if ($search['class_type_id'] === ''
                        && $search['admission_type_id'] === ''
                        && $search['search_type'] === ''
                        && $search['name'] === '') {
                        return back()
                            ->withInput()
                            ->withErrors(['name' => 'Please select a filter or enter a search keyword.']);
                    }

                    $data = Admission::with('ClassTypes')
                        ->where('status', 1)
                        ->where('session_id', Session::get('session_id'))
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('school', 1);

                    if ($search['class_type_id'] !== '') {
                        $data->where('class_type_id', $search['class_type_id']);
                    }

                    if ($search['admission_type_id'] !== '') {
                        $data->where('admission_type_id', $search['admission_type_id']);
                    }

                    if ($search['name'] !== '') {
                        $keyword = $search['name'];

                        if ($search['search_type'] !== '') {
                            $data->where($search['search_type'], 'LIKE', '%'.$keyword.'%');
                        } else {
                            $data->where(function ($query) use ($keyword) {
                                $query->where('admissionNo', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('ledger_no', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('first_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ['%'.$keyword.'%'])
                                    ->orWhere('father_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('mother_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('mobile', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('father_mobile', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('aadhaar', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('jan_aadhaar', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('address', 'LIKE', '%'.$keyword.'%');
                            });
                        }
                    }

                    $allstudents = $data
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get();

                    return view('fees.fees_collect.add', ['data' => $allstudents, 'search' => $search]);
                }

                // Initial GET: load initial active students so the cashier has immediate access
                $initialStudents = Admission::with('ClassTypes')
                    ->where('status', 1)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('school', 1)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->limit(50)
                    ->get();

                return view('fees.fees_collect.add', ['data' => $initialStudents, 'search' => $search]);
            }
                
            public function feesLedgerCollect(Request $request){
                $serach['name'] = $request->name;
                $serach['class_type_id'] = !empty($request->class_type_id) ? $request->class_type_id : 0;
                $serach['sr_no'] = !empty($request->sr_no) ? $request->sr_no : "";
                $srno = Admission::where('status', 1)->where('admission_type_id', 1)->where('branch_id', Session::get('branch_id'))->where('session_id', Session::get('session_id'))->where('school','=',1)->whereNotNull('ledger_no')->orderBy('ledger_no')->get();
                if ($request->isMethod('post')) {
                    $request->validate([
                       // 'sr_no' => 'required',
                    ]);
                    $allstudents = Admission::where('branch_id', Session::get('branch_id'))->where('session_id', Session::get('session_id'))->where('status', 1)->where('admission_type_id', 1);
                    // if ($request->class_type_id > 0) {
                    // $allstudents = $allstudents->where('class_type_id',$request->class_type_id);
                    // }
                    // if ($request->class_type_id > 0 && $request->sr_no != '') {
                    if ($request->sr_no != '') {
                        $allstudents = $allstudents-> where('ledger_no',$request->sr_no);
                    }
                    if (!empty($request->name)) {
                        $value = $request->name;
                        $allstudents = $allstudents->where(function ($query) use ($value) {
                            $query->orWhere('ledger_no', 'like', '%' . $value . '%');
                            $query->orWhere('father_name', 'like', '%' . $value . '%');
                            $query->orWhere('admissionNo', 'like', '%' . $value . '%');
                            $query->orWhere('mobile', 'like', '%' . $value . '%');
                            $query->orWhere('first_name', 'like', '%' . $value . '%');
                            $query->orWhere('last_name', 'like', '%' . $value . '%');
                        });
                    }
                    $allstudents = $allstudents->whereNotNull('ledger_no')->get();
                    return  view('fees.fees_collect.byLedger', ['data' => $allstudents, 'serach' => $serach,'srno_post'=>$request->sr_no,'srno'=>$srno]);
                }
                return  view('fees.fees_collect.byLedger', ['serach' => $serach,'srno'=>$srno]);
            }

            public function feesGroup(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                    ]);
                    $fees_group = new FeesGroup; //model name
                    $fees_group->user_id = Session::get('id');
                    $fees_group->session_id = Session::get('session_id');
                    $fees_group->branch_id = Session::get('branch_id');
                    $fees_group->name = $request->name;
                    $fees_group->fees_refund = $request->fees_refund ?? 'no';
                    $fees_group->fees_type = 'full';
                    $fees_group->save();
                    return redirect::to('feesGroup')->with('message', 'Fees Group Added Successfully !');
                }

                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');

                $fees_group_list = FeesGroup::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->orderBy('id', 'ASC')
                    ->get();

                $usedGroupIdsMaster = FeesMaster::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->pluck('fees_group_id')
                    ->toArray();

                $usedGroupIdsAssign = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('fees_group_id')
                    ->toArray();

                $usedGroupIdsDetail = DB::table('fees_detail')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('fees_group_id')
                    ->toArray();

                $inUseGroupIds = array_values(array_unique(array_filter(array_merge($usedGroupIdsMaster, $usedGroupIdsAssign, $usedGroupIdsDetail))));

                $stats = [
                    'total' => $fees_group_list->count(),
                    'refundable' => $fees_group_list->where('fees_refund', 'yes')->count(),
                    'non_refundable' => $fees_group_list->where('fees_refund', '!=', 'yes')->count(),
                    'in_use' => $fees_group_list->whereIn('id', $inUseGroupIds)->count(),
                ];

                return Helper::view('fees.fees.feesGroup', [
                    'dataview' => $fees_group_list,
                    'stats' => $stats,
                    'inUseGroupIds' => $inUseGroupIds
                ]);
            }

            public function feesGroupEdit(Request $request, $id){
                $data = FeesGroup::find($id);
                if (!$data) {
                    return redirect::to('feesGroup')->with('error', 'Fees Group not found!');
                }
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                    ]);
                    $data->user_id = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->name = $request->name;
                    $data->fees_refund = $request->fees_refund ?? 'no';
                    $data->save();
                    return redirect::to('feesGroup')->with('message', 'Fees Group Updated Successfully !');
                }

                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');

                $fees_group_list = FeesGroup::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->orderBy('id', 'ASC')
                    ->get();

                $usedGroupIdsMaster = FeesMaster::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->pluck('fees_group_id')
                    ->toArray();

                $usedGroupIdsAssign = DB::table('fees_assign_details')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('fees_group_id')
                    ->toArray();

                $usedGroupIdsDetail = DB::table('fees_detail')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->pluck('fees_group_id')
                    ->toArray();

                $inUseGroupIds = array_values(array_unique(array_filter(array_merge($usedGroupIdsMaster, $usedGroupIdsAssign, $usedGroupIdsDetail))));

                $stats = [
                    'total' => $fees_group_list->count(),
                    'refundable' => $fees_group_list->where('fees_refund', 'yes')->count(),
                    'non_refundable' => $fees_group_list->where('fees_refund', '!=', 'yes')->count(),
                    'in_use' => $fees_group_list->whereIn('id', $inUseGroupIds)->count(),
                ];

                return Helper::view('fees.fees.feesGroupEdit', [
                    'data' => $data,
                    'dataview' => $fees_group_list,
                    'stats' => $stats,
                    'inUseGroupIds' => $inUseGroupIds
                ]);
            }
            
            public function feesGroupDelete(Request $request)
            {
                $id = $request->input('delete_id');
                $feesGroup = FeesGroup::find($id);
                if (!$feesGroup) {
                    return Redirect::to('feesGroup')->with('error', 'Fees Group not found!');
                }
                $isUsedInDetail = DB::table('fees_detail')->where('fees_group_id', $id)->whereNull('deleted_at')->count();
                $isUsedInAssign = DB::table('fees_assign_details')->where('fees_group_id', $id)->whereNull('deleted_at')->count();
                if (($isUsedInDetail + $isUsedInAssign) > 0) {
                    return Redirect::to('feesGroup')->with('error', 'Cannot delete this Fees Group because it is currently assigned or has transaction records!');
                }
                $feesGroup->delete();
            
                return Redirect::to('feesGroup')->with('message', 'Fees Group Deleted Successfully!');
            }
                
                
            public function studentFeesOnclick(Request $request)
                    {
                        // Remove duplicate fee entries
                        $this->FeesGroupRemoveDuplicateEntries();
                    
                        $sessionId = $request->session_id ?? Session::get('session_id');
                        $branchId = Session::get('branch_id');
                    
                        // Get the Bill Counter
                        $billCounter = BillCounter::where('type', 'FeesSlip')
                            ->where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->first();
                    
                        $billCounterNo = $billCounter ? ($billCounter->counter + 1) : 1;
                    
                        // Get Student Admission Record
                        $studentQuery = Admission::where('session_id', $sessionId);
                        if (!empty($request->admission_id)) {
                            $studentQuery->where('id', $request->admission_id);
                        } elseif (!empty($request->unique_system_id) && $request->unique_system_id !== 'null') {
                            $studentQuery->where('unique_system_id', $request->unique_system_id);
                        }
                        $checkStudent = $studentQuery->first();
                    
                        $admissionId = $checkStudent->id ?? null;
                    
                        if ($checkStudent) {
                            $stuData = $checkStudent;
                        } else {
                            $stuData = ['first_name' => 'not_found_not_found', 'class_type_id' => 'not'];
                        }
                    
                        // Get previous sessions (<= current session)
                        if (!empty($checkStudent->unique_system_id)) {
                            $previousSessionIds = Admission::where('unique_system_id', $checkStudent->unique_system_id)->pluck('session_id');
                        } elseif (!empty($admissionId)) {
                            $previousSessionIds = Admission::where('id', $admissionId)->pluck('session_id');
                        } else {
                            $previousSessionIds = collect();
                        }
                        $sessions = Sessions::whereIn('id', $previousSessionIds)
                            ->where('id', '<=', $sessionId)
                            ->orderByDesc('id')
                            ->get();
                    
                        // Prepare fees-related data
                        $feesAssign = FeesAssign::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('admission_id', $admissionId)
                            ->first();
                    
                        $feesCollect = FeesCollect::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('admission_id', $admissionId)
                            ->first();
                    
                        $feesDetailsInvoices = FeesDetailsInvoices::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('admission_id', $admissionId)
                            ->orderByDesc('id')
                            ->get();
                    
                        $feesMaster = FeesMaster::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('class_type_id', $stuData['class_type_id'])
                            ->get();
                    
                        $stuFeeDet = FeesDetail::with(['PaymentMode', 'Admission', 'FeesCollect'])
                            ->where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('admission_id', $admissionId)
                            ->where('fees_type', 0)
                            ->orderByDesc('id')
                            ->get();
                    
                        $billCounterFinal = BillCounter::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('type', 'FeesSlip')
                            ->first();
                    
                        $inventory = StoreItemRequest::where('session_id', $sessionId)
                            ->where('branch_id', $branchId)
                            ->where('admission_id', $admissionId)
                            ->groupBy('receipt_no')
                            ->get();
                    
                        // Prepare data array for view
                        $data = [
                            'session_id' => $sessionId,
                            'BillCounter' => $billCounterFinal,
                            'stuData' => $stuData,
                            'sessions' => $sessions,
                            'FeesAssign' => $feesAssign,
                            'FeesCollect' => $feesCollect,
                            'FeesDetailsInvoices' => $feesDetailsInvoices,
                            'FeesMaster' => $feesMaster,
                            'stuFeeDet' => $stuFeeDet,
                            'inventory' => $inventory,
                        ];
                    
                        // Check if student has fees assigned
                        if (!empty($feesAssign->total_amount)) {
                            return view('fees.fees_collect.student_bill', ['data' => $data]);
                        }
                    
                        // No data found case
                        return response()->json(0);
                    }


 
            
            public function inventoryPaySubmit(Request $request){
                $enteredAmount = (Int)$request->get('enteredAmount');
                $collectedData = $request->get('collectedData');
                // Sort the array by 'pending' in ascending order
                usort($collectedData, function ($a, $b) {
                    return $a['pending'] - $b['pending'];
                });
                foreach ($collectedData as $item) {
                    $pending = (Int)($item['pending'] ?? 0);
                    $receipt = $item['receipt'];
                    $admissionId = $item['admission_id'];
                    if ($enteredAmount >= $pending) {
                        // Deduct the full pending amount and save to the database
                        $this->saveStoreReceipt($admissionId, $receipt, $pending);
                        $enteredAmount -= $pending;
                    }
                    else {
                        // Partial payment case, if enteredAmount is less than the pending amount
                        $this->saveStoreReceipt($admissionId, $receipt, $enteredAmount);
                        $enteredAmount = 0;
                        break; // Exit the loop as no more amount left to allocate
                    }
                }
            }
            function saveStoreReceipt($admissionId, $receipt, $amount) {
                if($amount > 0 ){
                    $pay = new StoreBillingDetail;
                    $pay->user_id = Session::get('id');
                    $pay->session_id =Session::get('session_id');
                    $pay->branch_id = Session::get('branch_id');
                    $pay->fees_counter_id = Session::get('counter_id');
                    $pay->admission_id = $admissionId;
                    $pay->receipt_no = $receipt;
                    $pay->amount = $amount;
                    $pay->date = date('Y-m-d');
                    $pay->save();
                }
            }
        
            public function studentPaySubmit(Request $request){ 
               
                if (!empty($request->offline_receipt_no)) {

                    $FeesDetail = FeesDetail::where('offline_receipt_no', $request->offline_receipt_no)
                                            ->where('status', '!=', 2)
                                            ->first();

                    if ($FeesDetail) {
                        // Jis student ne yeh receipt pehle use ki hai, uska data nikal rahe hain
                        $studentData = Admission::select('admissions.*', 'class_types.name as class_name')
                            ->leftJoin('class_types', 'class_types.id', 'admissions.class_type_id')
                            ->where('admissions.id', $FeesDetail->admission_id)
                            ->first();

                        return Response::json([
                            'status' => 'error',
                            'message' => 'This Receipt Number has already been used by another student. Details are below:',
                            'data' => [
                                'admission_no'       => $studentData->admissionNo ?? 'N/A',
                                'student_name'       => trim(($studentData->first_name ?? '') . ' ' . ($studentData->last_name ?? '')),
                                'father_name'        => $studentData->father_name ?? 'N/A',
                                'mobile'             => $studentData->mobile ?? 'N/A',
                                'class_name'         => $studentData->class_name ?? 'N/A',
                                'offline_receipt_no' => $request->offline_receipt_no,
                            ]
                        ], 422);
                    }
                }
                
            
                $cheque_image = '';
                $session_id = $request->session_id ?? Session::get('session_id');
                $BillCounter = BillCounter::where('session_id',$session_id)->where('branch_id',Session::get('branch_id'))->where('type', 'FeesSlip')->get()->first();
                $request->validate([
                    'admission_id' => 'required|integer',
                    'selected_head' => 'required|array|min:1',
                    'selected_head.*' => 'required|integer',
                    'amount' => 'required|array',
                    'amount.*' => 'required|numeric|min:0',
                    'discount_amount' => 'nullable|array',
                    'discount_amount.*' => 'nullable|numeric|min:0',
                    'fine' => 'required|array',
                    'fine.*' => 'required|numeric|min:0',
                    'date' => 'required|date',
                    'payment_mode_id' => 'required|integer',
                ]);

                if (count($request->selected_head) !== count($request->amount)
                    || count($request->selected_head) !== count($request->fine)) {
                    return Response::json(['status' => 'error', 'message' => 'Invalid fee-head values. Please reselect the fee heads.'], 422);
                }

                // Totals used for the invoice must come from the submitted detail rows;
                // never trust stale/disabled summary inputs from the browser.
                $submittedAmount = collect($request->amount)->sum(fn ($value) => (float) $value);
                $submittedFine = collect($request->fine)->sum(fn ($value) => (float) $value);
                $submittedDiscount = collect($request->discount_amount ?? [])->sum(fn ($value) => (float) ($value ?? 0));

                $FeesAssign = FeesAssign::where('admission_id',$request->admission_id)
                    ->where('session_id', $session_id)
                    ->where('branch_id', Session::get('branch_id'))
                    ->first();
                if (!$BillCounter || !$FeesAssign) {
                    return Response::json(['status' => 'error', 'message' => 'Fee assignment or receipt counter was not found.'], 422);
                }
                $fees_details_id =[];
                $slip = "";
                if ($request->isMethod('post')) {
                    //dd($request);
                    $admission_id = $request->admission_id;
                    $data = Admission::where('id',$admission_id)
                        ->where('session_id', $session_id)
                        ->where('branch_id', Session::get('branch_id'))
                        ->first();
                    if (!$data) {
                        return Response::json(['status' => 'error', 'message' => 'Student was not found for the selected session.'], 422);
                    }
                    DB::beginTransaction();
                    try {
                    $BillCounter = BillCounter::whereKey($BillCounter->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                    if (!empty($admission_id)) {
                        if (!empty($request->selected_head)) {
                            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                            $BillCounter->counter = $counter + 1;
                            $BillCounter->save();
                            foreach($request->selected_head as $key=> $head){
                                if (((float) $request->amount[$key]) > 0
                                    || ((float) ($request->discount_amount[$key] ?? 0)) > 0
                                    || ((float) ($request->fine[$key] ?? 0)) > 0) {
                                    $payOld = FeesCollect::where('admission_id',$admission_id)
                                        ->where('session_id', $session_id)
                                        ->where('branch_id', Session::get('branch_id'))
                                        ->first();
                                    if(!empty($payOld)){
                                        $pay = $payOld;
                                        $payOld->increment('discount', $request->discount_amount[$key] ?? 0);
                                        $payOld->increment('amount', $request->amount[$key] ?? 0);
                                        $payDetail = new FeesDetail; //model name
                                        $payDetail->user_id = Session::get('id');
                                        $payDetail->session_id = $session_id;
                                        $payDetail->branch_id = Session::get('branch_id');
                                        $payDetail->fees_collect_id = $payOld->id;
                                        $payDetail->fees_group_id = $head;
                                        $payDetail->receipt_no  = $request->slip_no;
                                        $payDetail->admission_id = $admission_id; 
                                        $payDetail->paid_amount = $request->amount[$key];
                                        $payDetail->installment_fine = $request->fine[$key];
                                        $payDetail->payment_mode_id = $request->payment_mode_id;
                                        $payDetail->discount = $request->discount_amount[$key];
                                        $payDetail->total_amount = $request->amount[$key]+$request->discount_amount[$key];
                                        $payDetail->status = $request->payment_status;
                                         $payDetail->date = $request->date;
                                         $payDetail->offline_receipt_no = $request->offline_receipt_no;
                                        $payDetail->save();  
                                        $fees_details_id[]= $payDetail->id;
                                    }
                                    else{
                                        $pay = new FeesCollect;
                                        $pay->user_id = Session::get('id');
                                        $pay->session_id = $session_id;
                                        $pay->branch_id = Session::get('branch_id');
                                        $pay->admission_id = $request->admission_id;
                                        $pay->fees_assign_id = $FeesAssign->id;
                                        $pay->amount = $request->amount[$key];
                                        $pay->discount = $request->discount_amount[$key] ?? 0;
                                        $pay->save();
                                        $collect_id = $pay->id;
                                        $payDetail = new FeesDetail; //model name
                                        $payDetail->user_id = Session::get('id');
                                        $payDetail->session_id = $session_id;
                                        $payDetail->branch_id = Session::get('branch_id');
                                        $payDetail->fees_collect_id = $collect_id;
                                        $payDetail->fees_group_id = $head;
                                        $payDetail->receipt_no  = $request->slip_no;
                                        $payDetail->admission_id = $admission_id;
                                        $payDetail->paid_amount = $request->amount[$key];
                                        $payDetail->installment_fine = $request->fine[$key];
                                        $payDetail->discount = $request->discount_amount[$key];
                                        $payDetail->total_amount = $request->amount[$key]+$request->discount_amount[$key];
                                        $payDetail->status = $request->payment_status;
                                        $payDetail->date = $request->date;
                                        $payDetail->payment_mode_id = $request->payment_mode_id;
                                        $payDetail->offline_receipt_no = $request->offline_receipt_no;
                                        $payDetail->save();
                                        $fees_details_id[]= $payDetail->id;
                                    }
                                    
                                }
                            }
                        }
                        if(!empty($fees_details_id)){
                            $transaction_slip = '';
                            if ($request->file('payment_receipt')) {
                                $image = $request->file('payment_receipt');
                                $path = $image->getRealPath();
                                $transaction_slip =$image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'payment_receipt';
                                $image->move($destinationPath, $transaction_slip);
                            }
                            $invoice = new FeesDetailsInvoices();
                            $invoice->user_id = Session::get('id');
                            $invoice->session_id = $session_id;
                            $invoice->branch_id = Session::get('branch_id');
                            $invoice->fees_counter_id = Session::get('fees_counter_id');
                            $invoice->admission_id = $admission_id;
                            $invoice->fees_details_id = implode(',',$fees_details_id );
                            $invoice->payment_date = $request->date; 
                            $invoice->payment_mode = $request->payment_mode_id;
                            $invoice->transaction_id = $request->transition_id;
                            $invoice->bank_name = $request->bank_name;
                            $invoice->invoice_no = $request->slip_no;
                            $invoice->status = $request->payment_status;
                            $invoice->cheque_number = $request->cheque_number;
                            $invoice->cheque_date = $request->cheque_date;
                            $invoice->payment_receipt = $transaction_slip;  
                            $invoice->amount = $submittedAmount;
                            $invoice->total_fine = $submittedFine;
                            $invoice->discount = $submittedDiscount;
                            $invoice->remark = $request->other_fee_remark;
                            $invoice->offline_receipt_no = $request->offline_receipt_no;
                            $invoice->save();
                            $fees_details_invoice_id = $invoice->id;
                            $slip = $invoice->invoice_no;
                            
 
    if ($request->advance_payment == 'yes') {
        $existingData = FeesAdvance::where('unique_system_id', $data->unique_system_id)->first();
        $balance = 0;
        if (!empty($existingData)) {
            $balance = $existingData->balance - $request->total_amount;
            $existingData->balance = $balance ?? ''; 
            $existingData->save();
            $advancahistory = new FeesAdvanceHistory;
            $advancahistory->debit = $request->total_amount; 
            $advancahistory->user_id = Session::get('id'); 
            $advancahistory->session_id = Session::get('session_id'); 
            $advancahistory->unique_system_id = $data->unique_system_id; 
            $advancahistory->branch_id = Session::get('branch_id'); 
            $advancahistory->date = $request->date; 
            $advancahistory->details = "Amount debited for this Receipt No . =" . $request->slip_no; 
            $advancahistory->fees_advance_id = $existingData->id; 
            $advancahistory->save();
        }
    }

                        }
                    }
                    
                
                    $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                          ->where('message_types.status',1)->where('message_types.slug','fees-collect')->first();
                          
                    $branch = Branch::find(Session::get('branch_id'));
                    $setting = Setting::where('branch_id',Session::get('branch_id'))->first();
                    $payment_mode = PaymentMode::where('id',$request->payment_mode_id)->first();
                    
                    $finalCollectedAmt = $submittedAmount + $submittedFine;


                    $arrey1 = array(
                                    '{#name#}',
                                    '{#collect_amount#}',
                                    '{#method#}',
                                    '{#school_name#}');
                    $arrey2 = array(
                                    $request->name,
                                    $finalCollectedAmt,
                                    $payment_mode->name,
                                    $setting->name);

             $invoice_data =  FeesDetailsInvoices::select('fees_details_invoices.*','admissions.first_name',
                    'admissions.last_name','class_types.name as class_name','class_types.id as class_type_id','admissions.father_name',
                    'admissions.admissionNo','payment_modes.name as payment_mode','payment_modes.id as payment_mode_id')
                    ->leftjoin('admissions as admissions', 'admissions.id', 'fees_details_invoices.admission_id')
                    ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                    ->leftjoin('payment_modes','payment_modes.id','fees_details_invoices.payment_mode')
                    ->where('fees_details_invoices.session_id', $session_id)
                    ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                    ->where('fees_details_invoices.id',$fees_details_invoice_id)->first();
                    $explode = explode(',',$invoice_data->fees_details_id);
                    $fess_print = FeesDetail::select('fees_detail.*','payment_modes.name as payment_mode','fees_group.name as fees_group_name')
                        ->leftJoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                        ->leftJoin('fees_collect','fees_collect.id','fees_detail.fees_collect_id')
                        ->leftJoin('fees_group','fees_group.id','fees_detail.fees_group_id')
                        ->whereIn('fees_detail.id',$explode);
                        $fess_print=$fess_print->get();

                    $studentName = trim(($invoice_data->first_name ?? '') . ' ' . ($invoice_data->last_name ?? ''));
                    $feeHeadDetails = $fess_print->map(function ($feeDetail) {
                        $line = (string) ($feeDetail->fees_group_name ?? 'Fee');
                        $line .= ': Paid ' . $this->feeAmount((float) ($feeDetail->paid_amount ?? 0));
                        if ((float) ($feeDetail->discount ?? 0) > 0) {
                            $line .= ', Discount ' . $this->feeAmount((float) $feeDetail->discount);
                        }
                        if ((float) ($feeDetail->installment_fine ?? 0) > 0) {
                            $line .= ', Fine ' . $this->feeAmount((float) $feeDetail->installment_fine);
                        }
                        return $line;
                    })->implode("\n");

                    $feeNotificationTitle = 'Fee Payment Received';
                    $feeNotificationBodyLines = [
                        'Student: ' . ($studentName !== '' ? $studentName : ($invoice_data->admissionNo ?? 'Student')),
                        'Receipt No: ' . ($invoice_data->invoice_no ?? $slip),
                        'Payment Date: ' . Carbon::parse($invoice_data->payment_date)->format('d M Y'),
                        'Payment Mode: ' . ($invoice_data->payment_mode ?? $payment_mode->name ?? '-'),
                    ];
                    if (!empty($invoice_data->offline_receipt_no)) {
                        $feeNotificationBodyLines[] = 'Offline Receipt No: ' . $invoice_data->offline_receipt_no;
                    }
                    if ($feeHeadDetails !== '') {
                        $feeNotificationBodyLines[] = "Fee Details:\n" . $feeHeadDetails;
                    }
                    $feeNotificationBodyLines[] = 'Amount: ' . $this->feeAmount((float) $submittedAmount);
                    $feeNotificationBodyLines[] = 'Discount: ' . $this->feeAmount((float) $submittedDiscount);
                    $feeNotificationBodyLines[] = 'Fine: ' . $this->feeAmount((float) $submittedFine);
                    $feeNotificationBodyLines[] = 'Total Paid: ' . $this->feeAmount((float) $finalCollectedAmt);
                    $feeNotificationBodyLines[] = 'Status: Payment Received';
                    $feeNotificationBody = implode("\n", $feeNotificationBodyLines);

                    $this->createFeeNotification(
                        (int) $admission_id,
                        (int) Session::get('branch_id'),
                        (int) $session_id,
                        $feeNotificationTitle,
                        $feeNotificationBody,
                        'fee_payment'
                    );

                   if ($request->has('checkbox_whatsapp')) {     
                    $printPreview = Helper::printPreview('Fees Collect');
                            
                            $pdf = PDF::loadView($printPreview,['data'=>$fess_print,'invoice_data'=>$invoice_data]);
        

                    $file_name = 'Fees Recipt '. $slip . '-' . time() . '.pdf';
                    $destinationPath = env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf/';
                    $file_path = $destinationPath . $file_name;
                    file_put_contents($file_path, $pdf->output());
                    $file_show_path = env('IMAGE_SHOW_PATH').'fees_receipt_pdf/'.$file_name;
        
                    if($branch->whatsapp_srvc != 0){
                        if ($request->mobile != ""){
                            if($template->whatsapp_status != 0){
                                $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                Helper::sendWhatsappMessage($request->mobile,$whatsapp,$file_show_path);
                                // if(File::exists(env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf/'.$file_name)){
                                //     File::delete(env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf/'.$file_name);
                                // } 
                            }
                        }
                    }
                }
                $response = $this->callAction('printFeesInvoice', [ 
                    'request' => new Request([
                        'fees_details_invoice_id' => $fees_details_invoice_id,
                    ])
                ]);
                DB::commit();

                $pushResult = $this->sendFeePush(
                    (int) $admission_id,
                    $feeNotificationTitle,
                    $feeNotificationBody,
                    'fee_payment',
                    [
                        'fees_details_invoice_id' => (string) $fees_details_invoice_id,
                        'receipt_no' => (string) $slip,
                        'amount' => (string) $finalCollectedAmt,
                    ]
                );

                return Response::json(array(
                    'status' => 'success',
                    'unique_system_id'=>$data->unique_system_id,
                    'session_id' => $data->session_id,
                    'slip'=>$slip,
                    'fees_details_invoice_id'=>$fees_details_invoice_id,
                    'notification_sent' => !empty($pushResult['success']),
                )); 
                    } catch (\Throwable $e) {
                        if (DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }

                        Log::error('Fee collection rolled back.', [
                            'admission_id' => $admission_id,
                            'session_id' => $session_id,
                            'branch_id' => Session::get('branch_id'),
                            'error' => $e->getMessage(),
                        ]);

                        return Response::json([
                            'status' => 'error',
                            'message' => 'Fee collection failed. No fee data was saved. Please try again.',
                        ], 500);
                    }
                }
            }
            
public function sendReceiptOnWhatsapp(Request $request)
{ 
    $fees_details_invoice_id = $request->fees_details_invoice_id;

    if ($request->isMethod('post')) {

        // Template
        $template = MessageTemplate::select('message_templates.*','message_types.slug','message_types.status as message_type_status')
            ->leftJoin('message_types','message_types.id','message_templates.message_type_id')
            ->where('message_types.slug','fees-collect')
            ->first();

        $branch   = Branch::find(Session::get('branch_id'));
        $setting  = Setting::where('branch_id',Session::get('branch_id'))->first();

        // Invoice + Student Info
        $invoice_data = FeesDetailsInvoices::select(
                'fees_details_invoices.*',
                'admissions.first_name','admissions.last_name','admissions.mobile',
                'class_types.name as class_name',
                'admissions.father_name','admissions.admissionNo',
                'payment_modes.name as payment_mode',
                'admissions.id as admission_id'
            )
            ->leftJoin('admissions','admissions.id','fees_details_invoices.admission_id')
            ->leftJoin('class_types','class_types.id','admissions.class_type_id')
            ->leftJoin('payment_modes','payment_modes.id','fees_details_invoices.payment_mode')
            ->where('fees_details_invoices.session_id',Session::get('session_id'))
            ->where('fees_details_invoices.branch_id',Session::get('branch_id'))
            ->where('fees_details_invoices.id',$fees_details_invoice_id)
            ->first();

        // Student info
        $student_name = $invoice_data->first_name.' '.$invoice_data->last_name;
        $mobile       = $invoice_data->mobile ?? null;
        $payment_mode = $invoice_data->payment_mode;

        // Collected Amount (total_amount + fine - discount)
        $finalCollectedAmt = (int) ($invoice_data->total_amount ?? 0) 
                           + (int) ($invoice_data->total_fine ?? 0) 
                           - (int) ($invoice_data->discount_given ?? 0);

        // Replace arrays for template
        $arrey1 = ['{#name#}','{#collect_amount#}','{#method#}','{#school_name#}'];
        $arrey2 = [$student_name,$finalCollectedAmt,$payment_mode,$setting->name];

        // Fee details
        $explode = explode(',',$invoice_data->fees_details_id);
        $fess_print = FeesDetail::select('fees_detail.*','payment_modes.name as payment_mode','fees_group.name as fees_group_name')
            ->leftJoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
            ->leftJoin('fees_collect','fees_collect.id','fees_detail.fees_collect_id')
            ->leftJoin('fees_group','fees_group.id','fees_detail.fees_group_id')
            ->whereIn('fees_detail.id',$explode)
            ->get();

        // PDF Generate
        $printPreview = Helper::printPreview('Fees Collect');
        $pdf = PDF::loadView($printPreview,[
            'data'=>$fess_print,
            'invoice_data'=>$invoice_data
        ]);

        $slip = $invoice_data->id; // use invoice id as slip no.
        $file_name = 'Fees Receipt '.$slip.'-'.time().'.pdf';
        $destinationPath = env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf/';
        $file_path = $destinationPath.$file_name;
        file_put_contents($file_path, $pdf->output());
        $file_show_path = env('IMAGE_SHOW_PATH').'fees_receipt_pdf/'.$file_name;
//dd($file_show_path);
        // Send WhatsApp
        
        $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                
                                if ($setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Fee Payment Received',
                                        'We have received your payment of ₹'.$finalCollectedAmt.' on '.$invoice_data->payment_date.' via '.$payment_mode.' Thank you!',
                                        'student',
                                        $invoice_data->admission_id
                                    ); 
                                }
                                 
                                if ($template->message_type_status == 1) {
                                    if ($branch->whatsapp_srvc == 1) {
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile,$whatsapp,$file_show_path);
                                        }
                                    }
                                }
        
      

        // // Push Notification
        // Helper::sendNotification(
        //     'Fee Payment Received',
        //     'We have received your payment of ₹'.$finalCollectedAmt.' on '.$invoice_data->payment_date.' via '.$payment_mode.' Thank you!',
        //     'student',
        //     $invoice_data->admission_id
        // );
    }



    return response()->json([
    'status' => 'success',
    'message' => 'WhatsApp message sent successfully!'
]);

}

 
            public function viewFees(Request $request){
                $serach['name'] = $request->name;
                $serach['class_type_id'] = $request->class_type_id;
                $serach['starting'] = $request->starting;
                $serach['ending'] = $request->ending;
                $serach['user_id'] = $request->user_id;
                $serach['admission_no'] = $request->admission_no;
                $data =  FeesDetailsInvoices::select('fees_details_invoices.*','class.name as class_name','admissions.admissionNo','admissions.first_name'
                ,'admissions.last_name','users.first_name as users_first_name'
                ,'users.last_name as users_last_name','admissions.father_name','admissions.school','payment_modes.name as payment_mode','payment_modes.id as payment_mode_id')
                ->leftjoin('admissions as admissions', 'admissions.id', 'fees_details_invoices.admission_id')
                ->leftjoin('class_types as class','class.id','admissions.class_type_id')
                ->leftjoin('payment_modes','payment_modes.id','fees_details_invoices.payment_mode')
                ->leftjoin('users','users.id','fees_details_invoices.user_id')
                ->where('fees_details_invoices.session_id', Session::get('session_id'))
                ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                ->where('fees_details_invoices.status', '!=', 2);
                if ($request->isMethod('post')) {
                    if (!empty($request->name)) {
                        $data = $data->where('admissions.first_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.last_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.father_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.mother_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.admissionNo', $request->name)
                        ->orwhere('admissions.mobile', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.aadhaar', $request->name)
                        ->orwhere('admissions.email', 'LIKE', '%' . $request->name . '%');
                    }
                    if (!empty($request->starting)) {
                        $data = $data->whereBetween('fees_details_invoices.payment_date', [$request->starting, $request->ending]);
                    }
                    if (!empty($request->class_type_id)) {
                        $data = $data->where("admissions.class_type_id", $request->class_type_id);
                    }
                    if (!empty($request->user_id)) {
                        $data = $data->where("fees_details_invoices.user_id", $request->user_id);
                    }
                }
                else{
                    $data = $data->whereBetween('fees_details_invoices.payment_date', [date('Y-m-d'), date('Y-m-d')]);
                    $serach['starting'] = date('Y-m-d');
                    $serach['ending'] = date('Y-m-d');
                }
                if (Session::get('role_id') > 1) {
                    $data = $data->where('fees_details_invoices.user_id', Session::get('id'));
                }
                $data = $data->where('admissions.school','=',1)->orderBy('fees_details_invoices.id', 'DESC')->get();
                
                return view('fees.fees_collect.index', ['data' => $data, 'serach' => $serach]);
            }
    
            public function AssignFeesEdit(Request $request,$id){
                $data = FeesAssignDetail::select('fees_assign_details.*','fees_group.id as feesGroupId')
                ->leftJoin('fees_group','fees_group.id','fees_assign_details.fees_group_id')
                ->where('fees_assign_details.admission_id',$id)
                ->get();
                $feesAssign = FeesAssign::where('admission_id',$id)->first(); 
                if ($request->isMethod('post')) {
                    $feesAssign->emi_check = $request->emi_check;
                    $feesAssign->save();
                    for($i=0; $i < count($request->fees_group_id); $i++ ){
                        $values = FeesAssignDetail::where('fees_assign_id',$request->fees_assign_id[$i])
                        ->where('fees_master_id',$request->fees_master_id[$i])
                        ->where('fees_group_id',$request->fees_group_id[$i])
                        ->where('admission_id',$id)
                        ->first();
                        if(!empty($values)){
                            $values->fees_group_amount = $request->amount[$i];
                            $values->save();
                        }
                        else{
                            $values = new FeesAssignDetail;
                            $values->user_id = Session::get('id');
                            $values->branch_id = Session::get('branch_id');
                            $values->session_id = Session::get('session_id');
                            $values->fees_group_amount = $request->amount[$i];
                            $values->admission_id = $request->admission_id[$i];
                            $values->fees_assign_id = $request->fees_assign_id[$i];
                            $values->fees_master_id = $request->fees_master_id[$i];
                            $values->fees_group_id = $request->fees_group_id[$i];
                            $values->save();
                        }
                    }
                    return redirect::to('student_assign_fees')->with('message', 'Assign Fees Update Successfully.');
                }
                return view('fees.assign_fees_student.edit',['data'=>$data,'feesAssign'=>$feesAssign]);
            }


            public function getFeesDetail(Request $request){
                $admission_id = $request->admission_id;
                $fees = FeesCollect::with('Student')->with('ClassTypes')->with('PaymentMode')->orderBy('id', 'DESC')->groupBy('admission_id')->get();
                $feesDetail = FeesDetail::where('admission_id', $admission_id)->with('FeesType')->orderBy('id', 'DESC')->get();
                $html = "";
                $name = "n";
                $count = 1;
                foreach ($feesDetail   as $key => $item) {
                    $html .= '<tr><td>' . $count++ . '</td><td>' . $item['FeesType']['name'] . '<input type="hidden" name="fees_type_id[]" value="' . $item['fees_type_id'] . '"></td><td title="Click on the amount for edit"><span id="' . $name . $count . '" class="editable">' . $item['amount'] . '</span></td>
                    <td><a href="" class="btn btn-primary  btn-xs ml-3"><i class="fa fa-edit"></i></a></td></tr>';
                    // return view('fees.fees_collect.index',['data'=>$fees,'dataview'=>$feesDetail]);
                }
                echo $html;
            }
  
            public function printPayement($id){
                $explode = explode(',',$id);
                $fess_print = FeesDetail::select('fees_detail.*','admissions.first_name','fees_group.name as fees_group_name','admissions.last_name','class_types.name as class_name','admissions.father_name','admissions.admissionNo','payment_modes.name as payment_mode')
                ->leftJoin('admissions','admissions.id','fees_detail.admission_id')
                ->leftJoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                ->leftJoin('fees_collect','fees_collect.id','fees_detail.fees_collect_id')
                ->leftJoin('fees_group','fees_group.id','fees_detail.fees_group_id')
                ->leftJoin('class_types','class_types.id','admissions.class_type_id')
                ->whereIn('fees_detail.id',$explode)->get();
                
                //dd($fess_print);
                $printPreview = Helper::printPreview('Fees Collect');
                // dd($printPreview);
                return view($printPreview, ['data' => $fess_print]);
                // return view('print_file.student_print.print_fees', ['data' => $fess_print]);
            }
    
            public function printFeesInvoice(Request $request){
                // dd($request);
                $explode = [];
                if(!empty($request->fees_details_invoice_id)){
                    $invoice_data =  FeesDetailsInvoices::select('fees_details_invoices.*','admissions.first_name','users.email as user_email',
                    'admissions.last_name','admissions.category','class_types.name as class_name','gender.name as gender_name','class_types.id as class_type_id','admissions.father_name',
                    'admissions.admissionNo','payment_modes.name as payment_mode','payment_modes.id as payment_mode_id')
                    ->leftjoin('admissions as admissions', 'admissions.id', 'fees_details_invoices.admission_id')
                    ->leftjoin('users as users', 'users.id', 'fees_details_invoices.user_id')
                    ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                    ->leftjoin('gender','gender.id','admissions.gender_id')
                    ->leftjoin('payment_modes','payment_modes.id','fees_details_invoices.payment_mode')
                    ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                    ->where('fees_details_invoices.id',$request->fees_details_invoice_id)->first();
                    $explode = explode(',',$invoice_data->fees_details_id);
                    $fess_print = FeesDetail::select('fees_detail.*','payment_modes.name as payment_mode','fees_group.name as fees_group_name')
                        ->leftJoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                        ->leftJoin('fees_collect','fees_collect.id','fees_detail.fees_collect_id')
                        ->leftJoin('fees_group','fees_group.id','fees_detail.fees_group_id')
                        ->whereIn('fees_detail.id',$explode);
                        $fess_print=$fess_print->get();
                        
                    $printPreview = Helper::printPreview('Fees Collect');
                    // dd($printPreview);
                    return view($printPreview, ['data'=>$fess_print,'invoice_data'=>$invoice_data]);
                } 
                else{            
                    return redirect::to('fee_dashboard');
                }
                //dd($fess_print);
            }
    
            public function printPayementGenerate($id){
                $fess_print = FeesDetail::with('Admission')->with('PaymentMode')->with('FeesCollect')->with('ClassTypes')->find($id);
                //dd($fess_print);
                $printPreview =    Helper::printPreview('Fees Collect');
                //dd($printPreview);
                $randomString = Str::random(10);
                $pdf = PDF::loadView($printPreview, ['data' => $fess_print]);
                file_put_contents(env('IMAGE_UPLOAD_PATH'). 'feesPaymentPdf' . '/' .$randomString.$fess_print->receipt_no . '.pdf', $pdf->output());
                $file_url = env('IMAGE_SHOW_PATH') . 'feesPaymentPdf' . '/' .$randomString.$fess_print->receipt_no . '.pdf';  
                FeesDetail::where('id',$id)->update(['fees_pdf_name' => $file_url]);
                return redirect::to('fees/index')->with('message', 'PDF Generated Successfully !');
                // return view($printPreview, ['data' => $fess_print]);
                // return view('print_file.student_print.print_fees', ['data' => $fess_print]);
            }

            public function collectFeesDelete(Request $request){
                $request->validate([
                    'admission_id' => 'required|integer',
                    'fees_invoice_id' => 'required|integer',
                    'session_id' => 'required|integer',
                ]);

                $admissionId = (int) $request->admission_id;
                $feeInvoiceId = (int) $request->fees_invoice_id;
                $sessionId = (int) $request->session_id;
                $branchId = (int) Session::get('branch_id');

                $student = Admission::where('id', $admissionId)
                    ->where('branch_id', $branchId)
                    ->first();
                if (!$student) {
                    return Response::json([
                        'status' => 'error',
                        'message' => 'Student not found.',
                    ], 404);
                }

                DB::beginTransaction();
                try {
                    $invoice = FeesDetailsInvoices::where('id', $feeInvoiceId)
                        ->where('admission_id', $admissionId)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->where('status', '!=', 2)
                        ->lockForUpdate()
                        ->first();

                    if (!$invoice) {
                        DB::rollBack();
                        return Response::json([
                            'status' => 'error',
                            'message' => 'Fee receipt was not found or has already been reverted.',
                        ], 422);
                    }

                    $feeDetailIds = array_values(array_filter(array_map(
                        'intval',
                        explode(',', (string) $invoice->fees_details_id)
                    )));
                    $feeDetails = FeesDetail::select(
                            'fees_detail.*',
                            'fees_group.name as fees_group_name',
                            'payment_modes.name as payment_mode'
                        )
                        ->leftJoin('fees_group', 'fees_group.id', '=', 'fees_detail.fees_group_id')
                        ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_detail.payment_mode_id')
                        ->whereIn('fees_detail.id', $feeDetailIds)
                        ->where('fees_detail.admission_id', $admissionId)
                        ->get();

                    FeesDetail::whereIn('id', $feeDetailIds)
                        ->where('admission_id', $admissionId)
                        ->update(['status' => 2]);
                    $invoice->status = 2;
                    $invoice->save();

                    $totalCollected = FeesDetail::where('admission_id', $admissionId)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->where('status', '!=', 2)
                        ->sum('total_amount');
                    $feesCollect = FeesCollect::where('admission_id', $admissionId)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->first();
                    if ($feesCollect) {
                        $feesCollect->amount = $totalCollected;
                        $feesCollect->save();
                    }

                    $revertedAmount = (float) ($invoice->amount ?? $feeDetails->sum('paid_amount'));
                    $revertedDiscount = (float) ($invoice->discount ?? $feeDetails->sum('discount'));
                    $revertedFine = (float) ($invoice->total_fine ?? $feeDetails->sum('installment_fine'));
                    $revertedTotal = $revertedAmount + $revertedFine;
                    $paymentMode = (string) (optional($feeDetails->first())->payment_mode ?? '-');
                    $headDetails = $feeDetails->map(function ($feeDetail) {
                        return (string) ($feeDetail->fees_group_name ?? 'Fee')
                            . ': ' . $this->feeAmount((float) ($feeDetail->paid_amount ?? 0));
                    })->implode("\n");

                    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                    $feeNotificationTitle = 'Fee Payment Reverted';
                    $bodyLines = [
                        'Student: ' . ($studentName !== '' ? $studentName : ($student->admissionNo ?? 'Student')),
                        'Receipt No: ' . ($invoice->invoice_no ?? $feeInvoiceId),
                        'Original Payment Date: ' . Carbon::parse($invoice->payment_date)->format('d M Y'),
                        'Payment Mode: ' . $paymentMode,
                    ];
                    if (!empty($invoice->offline_receipt_no)) {
                        $bodyLines[] = 'Offline Receipt No: ' . $invoice->offline_receipt_no;
                    }
                    if ($headDetails !== '') {
                        $bodyLines[] = "Reverted Fee Details:\n" . $headDetails;
                    }
                    $bodyLines[] = 'Amount Reverted: ' . $this->feeAmount($revertedAmount);
                    $bodyLines[] = 'Discount Reversed: ' . $this->feeAmount($revertedDiscount);
                    $bodyLines[] = 'Fine Reverted: ' . $this->feeAmount($revertedFine);
                    $bodyLines[] = 'Total Reverted: ' . $this->feeAmount($revertedTotal);
                    $bodyLines[] = 'Status: Payment Reverted';
                    $feeNotificationBody = implode("\n", $bodyLines);

                    $this->createFeeNotification(
                        $admissionId,
                        $branchId,
                        $sessionId,
                        $feeNotificationTitle,
                        $feeNotificationBody,
                        'fee_revert'
                    );

                    DB::commit();

                    $pushResult = $this->sendFeePush(
                        $admissionId,
                        $feeNotificationTitle,
                        $feeNotificationBody,
                        'fee_revert',
                        [
                            'fees_details_invoice_id' => (string) $feeInvoiceId,
                            'receipt_no' => (string) ($invoice->invoice_no ?? ''),
                            'amount' => (string) $revertedTotal,
                        ]
                    );

                    return Response::json([
                        'status' => 'success',
                        'unique_system_id' => $student->unique_system_id,
                        'session_id' => $student->session_id,
                        'notification_sent' => !empty($pushResult['success']),
                    ]);
                } catch (\Throwable $e) {
                    if (DB::transactionLevel() > 0) {
                        DB::rollBack();
                    }
                    Log::error('Fee revert failed.', [
                        'admission_id' => $admissionId,
                        'fees_invoice_id' => $feeInvoiceId,
                        'error' => $e->getMessage(),
                    ]);

                    return Response::json([
                        'status' => 'error',
                        'message' => 'Fee could not be reverted. Please try again.',
                    ], 500);
                }
            }
  
            public function feesSearchData(Request $request){
                $name = $request->post('name');
                $class_type_id = $request->get('class_type_id');
                $fees_type_id = $request->get('fees_type_id');
                $data =  FeesCollect::with('Student')->with('PaymentMode');
                if (!empty($name)) {
                    $data = $data->where("student_name", $name);
                }
                if (!empty($class_type_id)) {
                    $data = $data->where("class_type_id", $class_type_id);
                }
                $allfees = $data->orderBy('id', 'DESC')->get();
                return  view('fees.fees_collect.fees_search_data', ['data' => $allfees]);
            }

       
            public function feesMasterData(Request $request){
                $data =  FeesMaster::find($request->fees_master_id);
                $paidAmount =  FeesDetail::where('class_type_id', $request->class_type_id)->where('fees_type_id', $data['fees_type_id'])->sum('total_amount');
                //dd($data);
                if ($paidAmount > 0) {
                    $net_amount =  $data['amount'] - $paidAmount;
                } 
                else {
                    $net_amount = $data['amount'];
                }
                echo json_encode($net_amount);
            }

            public function ledgerSave(Request $request){
                if(!empty($request->admission_id)){
                    foreach($request->admission_id as $key => $ids)
                {
                $find = Admission::find($ids);
                    $find->ledger_no = $request->ledger_number[0] ?? null; 
                        $find->save();
                    }
                    return redirect::to('ledger_update')->with('message', 'Ledger Number Updated Successfully');
                }
            }
            
            public function ledgerUpdate(Request $request){
                $serach['name'] = $request->name;
                $serach['class_type_id'] = !empty($request->class_type_id) ? $request->class_type_id : 0;
                if ($request->isMethod('post')) {
                    $value = $request->name;
                    $data = Admission::with('ClassTypes')->where('status', 1)->where('admission_type_id', 1)->where('session_id', Session::get('session_id'))->where('school','=',1);
                    if(Session::get('role_id') > 1){
                        $data = $data->where('branch_id', Session::get('branch_id'));
                    }
                    if (!empty(Session::get('admin_branch_id'))) {
                       $data = $data->where('branch_id', Session::get('admin_branch_id'));
                    }
                    if (!empty($request->name)) {
                        $data = $data->where(function ($query) use ($value) {
                            $query->where('first_name', 'like', '%' . $value . '%');
                            $query->orWhere('userName', 'like', '%' . $value . '%');
                            $query->orWhere('mobile', 'like', '%' . $value . '%');
                            $query->orWhere('aadhaar', 'like', '%' . $value . '%');
                            $query->orWhere('email', 'like', '%' . $value . '%');
                            $query->orWhere('father_name', 'like', '%' . $value . '%');
                            $query->orWhere('mother_name', 'like', '%' . $value . '%');
                            $query->orWhere('address', 'like', '%' . $value . '%');
                            $query->orWhere('admissionNo', 'like', '%' . $value . '%');
                        });
                    }
                    if (!empty($request->class_type_id)) {
                        $data = $data->where("class_type_id", $request->class_type_id);
                    }
                    $allstudents = $data->orderBy('id', 'DESC')->get();
                    return  view('fees.fees_collect.studentSearchList', ['data' => $allstudents]);
                }
                return view('fees.fees_collect.ledgerUpdate',['serach' => $serach]);
            }

            public function feesLedger(Request $request){
                $sessionId = Session::get('session_id');
                $branchId = Session::get('branch_id');
                $roleId = Session::get('role_id');

                $page = max(1, (int) $request->input('page', 1));
                $perPageRaw = $request->input('per_page', 25);
                $perPage = ($perPageRaw === 'all') ? 'all' : (int) $perPageRaw;
                if ($perPage !== 'all') {
                    $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
                }

                $search = [
                    'admission_no'  => (string) $request->input('admission_no', ''),
                    'class_type_id' => (string) $request->input('class_type_id', ''),
                    'name'          => (string) $request->input('name', ''),
                    'father_name'   => (string) $request->input('father_name', ''),
                    'mobile'        => (string) $request->input('mobile', ''),
                    'starting'      => (string) $request->input('starting', ''),
                    'ending'        => (string) $request->input('ending', ''),
                    'status'        => (string) $request->input('status', ''),
                ];

                $baseQuery = Admission::select(
                        'admissions.id',
                        'admissions.admissionNo',
                        'admissions.first_name',
                        'admissions.last_name',
                        'admissions.father_name',
                        'admissions.mobile',
                        'admissions.class_type_id',
                        'class_types.name as className',
                        'fees_assigns.total_amount',
                        'fees_assigns.total_discount as assign_discount'
                    )
                    ->leftJoin('fees_assigns', function($join) use ($sessionId) {
                        $join->on('fees_assigns.admission_id', '=', 'admissions.id')
                             ->where('fees_assigns.session_id', '=', $sessionId);
                    })
                    ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                    ->where('admissions.admission_type_id', 1)
                    ->where('admissions.status', 1)
                    ->where('admissions.school', 1)
                    ->where('admissions.branch_id', $branchId)
                    ->where('admissions.session_id', $sessionId);

                if ($roleId == 2) {
                    $baseQuery->where('admissions.class_type_id', Session::get('class_type_id'));
                }

                // General keyword search
                $keyword = trim($search['name']);
                if ($keyword !== '') {
                    $baseQuery->where(function ($q) use ($keyword) {
                        $q->where('admissions.first_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.last_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.mobile', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.email', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.aadhaar', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.father_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.mother_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.admissionNo', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.address', 'LIKE', '%' . $keyword . '%');
                    });
                }

                // In-column specific filters
                if (!empty($search['admission_no'])) {
                    $baseQuery->where('admissions.admissionNo', 'LIKE', '%' . trim($search['admission_no']) . '%');
                }

                if (!empty($search['class_type_id'])) {
                    $baseQuery->where('admissions.class_type_id', (int) $search['class_type_id']);
                }

                if (!empty($search['father_name'])) {
                    $val = trim($search['father_name']);
                    $baseQuery->where(function($q) use ($val) {
                        $q->where('admissions.father_name', 'LIKE', "%{$val}%")
                          ->orWhere('admissions.mobile', 'LIKE', "%{$val}%");
                    });
                }

                // Payment Activity Date range filter using efficient whereExists
                if (!empty($search['starting']) && !empty($search['ending'])) {
                    $baseQuery->whereExists(function($q) use ($search, $sessionId) {
                        $q->select(DB::raw(1))
                          ->from('fees_detail')
                          ->whereColumn('fees_detail.admission_id', 'admissions.id')
                          ->where('fees_detail.session_id', $sessionId)
                          ->whereBetween('fees_detail.date', [$search['starting'], $search['ending']])
                          ->whereNull('fees_detail.deleted_at');
                    });
                } elseif (!empty($search['starting'])) {
                    $baseQuery->whereExists(function($q) use ($search, $sessionId) {
                        $q->select(DB::raw(1))
                          ->from('fees_detail')
                          ->whereColumn('fees_detail.admission_id', 'admissions.id')
                          ->where('fees_detail.session_id', $sessionId)
                          ->whereDate('fees_detail.date', '>=', $search['starting'])
                          ->whereNull('fees_detail.deleted_at');
                    });
                } elseif (!empty($search['ending'])) {
                    $baseQuery->whereExists(function($q) use ($search, $sessionId) {
                        $q->select(DB::raw(1))
                          ->from('fees_detail')
                          ->whereColumn('fees_detail.admission_id', 'admissions.id')
                          ->where('fees_detail.session_id', $sessionId)
                          ->whereDate('fees_detail.date', '<=', $search['ending'])
                          ->whereNull('fees_detail.deleted_at');
                    });
                }

                $totalCount = (clone $baseQuery)->count();

                // Compute high-speed overall statistics for filtered students
                $totalAssigned = (float) (clone $baseQuery)->sum(DB::raw('COALESCE(fees_assigns.total_amount, 0) - COALESCE(fees_assigns.total_discount, 0)'));
                
                $matchingStudentIds = (clone $baseQuery)->pluck('admissions.id')->filter()->all();
                $statsPaid = !empty($matchingStudentIds) ? DB::table('fees_detail')
                    ->whereIn('admission_id', $matchingStudentIds)
                    ->where('session_id', $sessionId)
                    ->whereIn('status', [0, 1])
                    ->whereNull('deleted_at')
                    ->selectRaw('
                        COALESCE(SUM(total_amount), 0) as total_collected,
                        COALESCE(SUM(discount), 0) as total_discount,
                        COALESCE(SUM(installment_fine), 0) as total_fine
                    ')->first() : null;

                $totalCollected = (float) ($statsPaid->total_collected ?? 0);
                $totalDiscount = (float) ($statsPaid->total_discount ?? 0);
                $totalFine = (float) ($statsPaid->total_fine ?? 0);
                $totalPending = max(0, $totalAssigned - $totalCollected - $totalDiscount);

                $stats = [
                    'total_students'  => $totalCount,
                    'total_assigned'  => $totalAssigned,
                    'total_collected' => $totalCollected,
                    'total_discount'  => $totalDiscount,
                    'total_fine'      => $totalFine,
                    'total_pending'   => $totalPending,
                ];

                // Ordering and Pagination
                $dataQuery = (clone $baseQuery)->orderBy('admissions.id', 'DESC');

                if ($perPage === 'all') {
                    $data = $dataQuery->get();
                    $startIndex = 0;
                    $lastPage = 1;
                } else {
                    $data = $dataQuery->forPage($page, $perPage)->get();
                    $startIndex = ($page - 1) * $perPage;
                    $lastPage = max(1, (int) ceil($totalCount / $perPage));
                }

                // Single-batch lookup for paginated students (0 N+1 queries!)
                $pageAdmissionIds = $data->pluck('id')->filter()->all();
                $paidLookup = [];
                if (!empty($pageAdmissionIds)) {
                    $paidLookup = DB::table('fees_detail')
                        ->whereIn('admission_id', $pageAdmissionIds)
                        ->where('session_id', $sessionId)
                        ->whereIn('status', [0, 1])
                        ->whereNull('deleted_at')
                        ->groupBy('admission_id')
                        ->select(
                            'admission_id',
                            DB::raw('COALESCE(SUM(installment_fine), 0) as total_fine'),
                            DB::raw('COALESCE(SUM(discount), 0) as total_discount'),
                            DB::raw('COALESCE(SUM(total_amount), 0) as total_paid')
                        )
                        ->get()
                        ->keyBy('admission_id');
                }

                // AJAX Real-Time Data Response
                if ($request->ajax() || $request->wantsJson() || $request->input('ajax') == '1') {
                    $html = view('fees.ledger.ledger_rows', [
                        'data'       => $data,
                        'startIndex' => $startIndex,
                        'paidLookup' => $paidLookup,
                        'permission' => Helper::permissioncheck(11),
                    ])->render();

                    return response()->json([
                        'status'       => true,
                        'html'         => $html,
                        'total'        => $totalCount,
                        'from'         => $totalCount > 0 ? $startIndex + 1 : 0,
                        'to'           => $perPage === 'all' ? $totalCount : min($startIndex + count($data), $totalCount),
                        'current_page' => $page,
                        'last_page'    => $lastPage,
                        'per_page'     => $perPage,
                        'stats'        => $stats,
                    ]);
                }

                $classType = Helper::classType();
                $permission = Helper::permissioncheck(11);
                $getSetting = Helper::getSetting();

                return view('fees.ledger.view', [
                    'data'         => $data,
                    'search'       => $search,
                    'totalCount'   => $totalCount,
                    'startIndex'   => $startIndex,
                    'lastPage'     => $lastPage,
                    'currentPage'  => $page,
                    'perPage'      => $perPage,
                    'stats'        => $stats,
                    'paidLookup'   => $paidLookup,
                    'classType'    => $classType,
                    'permission'   => $permission,
                    'getSetting'   => $getSetting,
                ]);
            }

            public function fees_ledger_view(Request $request) {
                $getFees = FeesAssignDetail::select('fees_assign_details.*', 'fees_group.name as group_name')
                    ->join('fees_group', 'fees_group.id', '=', 'fees_assign_details.fees_group_id')
                    ->where('admission_id',$request->admission_id)
                    ->get();
            
                $html = '<table class="table">
                    <thead>
                        <tr class="sky_tr">
                            <th>#</th> 
                            <th>Fees Type</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Fine</th>
                            <th>Paid</th>
                            <th style="text-align: right;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
            
                if (!$getFees->isEmpty()) {
                    $i = 1;
                    $grand_total = 0;
                    $Paids = 0;
                    $Discount = 0;
                    $Fine = 0;
                    $balances = 0;
                    $fine_amt = 0;
            
                    foreach ($getFees as $item) {
                        $feesDetails = FeesDetail::where('fees_type', 0)
                                    ->whereIn('status', [0, 1])
                                    ->where('admission_id',$request->admission_id)
                                    ->where('fees_group_id', $item->fees_group_id)
                                    ->selectRaw('SUM(total_amount) as total_amount, SUM(discount) as total_discount, SUM(installment_fine) as installment_fine')
                                    ->first();

                                $pad = $feesDetails->total_amount;
                                $discounts = $feesDetails->total_discount;
                                $fine_amt = $feesDetails->installment_fine;

            
                        $balance = $item->fees_group_amount-$item->discount - $pad;
                    
            
                        $html .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . ($item->group_name ?? '') . '</td>
                            <td>' . (!empty($item->installment_due_date) ? date('d-m-Y', strtotime($item->installment_due_date)) : '') . '</td>
                            <td>' . ($item->fees_group_amount > $pad ? '<span class="label1 label-danger-custom">Unpaid</span>' : '<span class="label1 label-success-custom">Total Paid</span>') . '</td>
                            <td>' . ($item->fees_group_amount-$item->discount ?? '0') . '</td>
                            <td>' . ($discounts ?? '0') . '</td>
                            <td>' . ($fine_amt ?? '0'). '</td>
                            <td>' . ($pad ?? '0') . '</td>
                            <td style="text-align: right;">' . ($balance ?? '') . '</td>
                        </tr>';
            
                        $grand_total += $item->fees_group_amount-$item->discount;
                        $Paids += $pad;
                        $Discount += $discounts;
                        $Fine += $fine_amt;
                        $balances += $balance;
                    }
            
                    $html .= '<tr>
                        <td colspan="12">
                            <div class="row">
                            <div class="col-6"></div>
                                <div class="col-6">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th colspan="3" style="text-align: right; font-weight: normal;"><strong>Grand Total:</strong> ' . $grand_total . '</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" style="text-align: right; font-weight: normal;"><strong>Paid:</strong> ' . $Paids . '</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" style="text-align: right; font-weight: normal;"><strong>Discount:</strong> ' . $Discount . '</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" style="text-align: right; font-weight: normal;"><strong>Fine:</strong> ' . $Fine . '</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" style="text-align: right; font-weight: normal;"><strong>Balance:</strong> ' . $balances . '</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
                } else {
                    $html .= '<tr class="text-center">
                        <td colspan="9"><b>!! NO DATA FOUND !!</b></td>
                    </tr>';
                }
            
                $html .= '</tbody></table>';
            
                return response()->json(['html' => $html]);
            }
    
           
    
            public function getFeesGroup(Request $request){
                $data =  FeesMaster::Select('fees_master.*','groups.name as fees_group_name','class.name as class_name','session.from_year as from_year','session.to_year as to_year')
                ->leftjoin('fees_group as groups','groups.id','fees_master.fees_group_id')
                ->leftjoin('class_types as class','class.id','fees_master.class_type_id')
                ->leftjoin('sessions as session','session.id','fees_master.session_id')
                ->where('fees_master.class_type_id',$request->class_type_id)->where('fees_master.session_id', $request->session_id)->where('fees_master.branch_id', Session::get('branch_id'))->get();
                return Response::json(array('data' => $data)); 
            }
            public function feesRemainderCron(Request $request){
                $search['name'] = $request->name;
                $search['class_type_id'] = $request->class_type_id ?? '';
                $serach['ending'] = $request->ending;
                $search['status'] = $request->status;
                //$search['batch'] = $request->batch;
                $search['admissionNo'] = $request->admissionNo;
                $search['session_id'] = $request->session_id;
                
                $session = Session::get('session_id');
                $branch_id = Session::get('branch_id');
                
                $studentArray='';
                if ($request->isMethod('post')) {     
                // Start Query
                $admission_ids = Admission::select('admissions.*', 'class_types.name as class_name')
                    ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                    ->where('admissions.school', 1)
                    ->where('admissions.session_id', $request->session_id ?? $session)
                    ->where('admissions.branch_id', $branch_id);
                
                // Apply Search Filter
                if (!empty($request->name)) {
                    $value = $request->name;
                    $admission_ids->where(function ($query) use ($value) {
                        $query->where("admissions.first_name", 'like', '%' . $value . '%')
                              ->orWhere("admissions.last_name", 'like', '%' . $value . '%')
                              ->orWhere("admissions.mobile", 'like', '%' . $value . '%')
                              ->orWhere("admissions.email", 'like', '%' . $value . '%')
                              ->orWhere("admissions.aadhaar", 'like', '%' . $value . '%')
                              ->orWhere("admissions.father_name", 'like', '%' . $value . '%')
                              ->orWhere("admissions.mother_name", 'like', '%' . $value . '%')
                              ->orWhere("admissions.address", 'like', '%' . $value . '%');
                    });
                }
                
                // Filter by Admission Number
                if (!empty($request->admissionNo)) {
                    $admission_ids->where("admissions.admissionNo", $request->admissionNo);
                }
                
                // Filter by Class Type
                if (!empty($request->class_type_id)) {
                    $admission_ids->where('admissions.class_type_id', $request->class_type_id);
                }
                
                // Filter by Batch
                /*if (!empty($request->batch)) {
                    $admission_ids->where("admissions.batch", $request->batch);
                }*/
                
                // Filter by Status
                if (isset($request->status) && $request->status !== '') {
                    $admission_ids->where("admissions.status", $request->status);
                } else {
                    $admission_ids->where("admissions.status", 1);
                }
                
                $admission_ids = $admission_ids->orderBy('admissions.class_type_id', 'ASC')->get();

                $template = MessageTemplate::Select('message_templates.*','message_types.slug')
                ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                ->where('message_types.status',1)->where('message_types.slug','feesreminder')->first();
                $setting = Setting::where('session_id',$session)->where('branch_id', Session::get('branch_id'))->first();     
                $studentArray =[];
                foreach($admission_ids as $student){
                    $fees_assigned = FeesAssign::where('admission_id',$student->id)->where('session_id',$session)->first();
                   $fees_collected = FeesDetail::where('admission_id',$student->id)->whereIn('status',[0,1])->sum('total_amount');

                    $isRemaining = (($fees_assigned->total_amount ?? 0)-($fees_assigned->total_discount ?? 0))-($fees_collected ?? 0);
                    // dd($isRemaining);
                    if($isRemaining >0 ){
                        $getHead =  FeesAssignDetail::Select('fees_assign_details.*','fees_group.name as group_name')
                        ->leftjoin('fees_group','fees_group.id','fees_assign_details.fees_group_id')
                        ->where('admission_id',$student->id)->where('fees_assign_details.session_id',$session)
                        //   ->whereNotNull('installment_due_date')
                        //   ->whereDate('installment_due_date','<=', date('Y-m-d'))
                        ->get();
                        $AllRemainingFees = '';
                        $total_pending = 0;
                        $remainingAmount = 0;
                        $installment_due_date = '';
                        foreach($getHead as $head){
                            //if($head->installment_due_date >= date('Y-m-d') || $head->installment_due_date === null)
                            //{
                                $feesDetails = FeesDetail::where('admission_id',$student->id)->where('fees_group_id',$head->fees_group_id)->whereIn('status',[0,1])->sum('paid_amount');
                                $remainingAmount = (($head->fees_group_amount ?? 0) - ($head->discount ?? 0)) - ($feesDetails ?? 0);
                                if($remainingAmount > 0){
                                    $line = $head->group_name . ' = Rs.' . number_format($remainingAmount);
                                    $AllRemainingFees .= $line . "\n";
                                    $total_pending += $remainingAmount;
                                    $installment_due_date = $head->installment_due_date;
                                }
                            // }
                        }         
                        $AllRemainingFees .= '<span class="bg-danger p-1">*TOTAL PENDING:' . ' = Rs.' . $total_pending.'*</span>';
                        $arrey1 = array(
                            '{#name#}',
                            '{#class_name#}',
                            '{#fees_remain#}',
                            '{#school_name#}',
                            '{#dur_date#}',
                        );
                        $arrey2 = array(
                            ($student->first_name ?? 0).' '.($student->last_name ?? ''),
                            $student->class_name ?? '',
                            preg_replace('/<br\s*\/?>/', '', nl2br($AllRemainingFees)) ,
                            $setting->name ?? '',
                            // date("d-m-Y", strtotime($installment_due_date)),
                            '',
                        );
                        $message = str_replace($arrey1,$arrey2,$template->whatsapp_content);       
                        //dd($message);    
                        if($remainingAmount > 0)
                            {
                                $studentArray[] =  array( 'id'=>$student->id,
                                'name'=>($student->first_name ?? 0).' '.($student->last_name ?? ''),
                                'className'=>$student->class_name,
                                'class_type_ids'=>$student->class_type_id,
                                'mobile'=>$student->mobile,
                                'admission_id'=>$student->id,
                                'admissionNo'=>$student->admissionNo,
                                'father_name'=>$student->father_name,
                                'category'=>$student->category,
                                'student_type'=>$student->student_type,
                                'course'=>$student->course,
                                'batch'=>$student->batch,
                                'status'=>$student->status,
                                'gender_id'=>$student->gender_id,
                                'session_id'=>$student->session_id,
                                'fees_assigned'=>$fees_assigned->total_amount,
                                'pendings'=>$AllRemainingFees,
                                'message'=>$message,
                            );
                            
                            
                        }
                    }
                }
            }
            if (isset($studentArray[0]['class_type_ids']) && empty($request->class_type_id)) {
                $search['class_type_id'] = $studentArray[0]['class_type_ids'];
            }
           
              return view('fees.dues.duesList',['data' => $studentArray,'search'=>$search]);
            }
            
    
    
            public function feesModification(Request $request){
                $admissionNo = $request->admissionNo ?? '';
                $class_type_id= $request->class_type_id ?? '';
                $admission_type_id= $request->admission_type_id_modify ?? '';
                $data =  FeesAssign::Select('fees_assigns.*','admissions.first_name','admissions.last_name','admissions.admissionNo','admissions.mobile')
                ->leftjoin('admissions','admissions.id','fees_assigns.admission_id')->where('fees_assigns.session_id',Session::get('session_id'))
                ->where('fees_assigns.branch_id',Session::get('branch_id'));
                if($class_type_id != ''){
                    $data= $data->where('admissions.class_type_id',$class_type_id);
                }
                if($admission_type_id != ''){
                    $data= $data->where('admissions.admission_type_id',$admission_type_id);
                }
                if($admissionNo != ''){
                    $data= $data->where('admissions.admissionNo',$admissionNo);
                }
                $data = $data ->get();
              
                return view('fees.modification.fees_modification', ['data' => $data]);
            }
    
       
    
            public function updateAssignedFees(Request $request){
                $request->validate([
                    'fees_assign_detail_id' => 'required|integer',
                    'session_id' => 'nullable|integer',
                    'field' => 'required|in:installment_due_date,fees_group_amount,discount,fees_refund',
                    'value' => $request->field === 'installment_due_date'
                        ? 'nullable|date_format:Y-m-d'
                        : 'nullable',
                ]);

                $feesAssignedId = $request->fees_assign_detail_id;
                $sessionId = $request->session_id ?? Session::get('session_id');
                $value = $request->value;
                $field = $request->field;
                $feesAssignDetail = FeesAssignDetail::where('id', $feesAssignedId)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', $sessionId)
                    ->first();

                if (!$feesAssignDetail) {
                    return Response::json(['message' => 'Assigned fee record was not found.'], 404);
                }

                $admission_id = $feesAssignDetail->admission_id;
                $feesAssignDetail->$field = $field === 'installment_due_date' && empty($value) ? null : $value;
                $feesAssignDetail->save();
                $feesAssignDetail = FeesAssignDetail::where('branch_id',Session::get('branch_id'))
                    ->where('session_id', $sessionId)
                    ->where('admission_id',$admission_id)->get();   
                $total_amount = 0;
                $total_discount = 0;
                if(!empty($feesAssignDetail)){
                    foreach($feesAssignDetail as $item){ 
                        $total_amount += $item->fees_group_amount ?? 0;
                        $total_discount += $item->discount ?? 0;
                    }
                }
                $feesAssign = FeesAssign::where('id', $feesAssignDetail[0]->fees_assign_id)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', $sessionId)
                    ->first();
                if (!$feesAssign) {
                    return Response::json(['message' => 'Fee assignment was not found.'], 404);
                }
                $feesAssign->total_amount = $total_amount ?? 0;
                $feesAssign->total_discount = $total_discount ?? 0;
                $feesAssign->net_amount = $total_amount-$total_discount;
                $feesAssign->save();
                return Response::json(array('message' =>'Fees Updated Successfully' )); 
            }
    
            public function deleteAssignedFees(Request $request){
                $assign_id = $request->fees_assign_detail_id ?? '' ;
                $deleteData = FeesAssignDetail::find($assign_id);
                $admission_id = $deleteData->admission_id;
                $deleteData->delete();  
                $feesAssignDetail=FeesAssignDetail::where('branch_id',Session::get('branch_id'))->where('admission_id',$admission_id)->get();    
                $total_amount = 0;
                $total_discount = 0;
                if(!empty($feesAssignDetail)){
                    foreach($feesAssignDetail as $item){
                        $total_amount += $item->fees_group_amount ?? 0;
                        $total_discount += $item->discount ?? 0;
                    }
                }
                $feesAssign = FeesAssign::find($feesAssignDetail[0]->fees_assign_id);
                $feesAssign->total_amount = $total_amount ?? 0;
                $feesAssign->total_discount = $total_discount ?? 0;
                $feesAssign->net_amount = $total_amount-$total_discount;
                $feesAssign->save();
                return Response::json(array('id' =>$assign_id )); 
            }
            public function getStudentsList(Request $request){
                $fees_assign_details = FeesAssignDetail::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->groupBy('admission_id')->pluck('admission_id')->implode(',');
                $admissionIds = [];
                if(!empty($fees_assign_details)){
                    $admissionIds = explode(',', $fees_assign_details);
                }
                $class_type_id = $request->class_type_id ?? '';
                $admissionNo= $request->admissionNo ?? '';
                $data = Admission::where('session_id',Session::get('session_id'))
               ->where('status',1)
               ->where('branch_id',Session::get('branch_id'));
                if($class_type_id != ''){
                    $data= $data->where('class_type_id', $class_type_id);
                }
                if($request->admission_type_id != ''){
                    $data = $data->where('admission_type_id',$request->admission_type_id);
                }
                if($admissionNo != ''){
                    $data= $data->where('admissionNo',$admissionNo);
                }
                $data = $data->get();
                return view('fees.modification.admissionList', ['data' => $data]);
            }
            public function createFeesInstallment(Request $request){
                if(!empty($request->installment_name)){
                    foreach($request->installment_name as $key=> $name)
                {
                $fees_group = FeesGroup::where('name' , $name)->first();            
                if(!empty($fees_group)){
                    $fees_group = $fees_group;
                }
                else
                {
                   $fees_group = new FeesGroup; //model name
                }
                $fees_group->user_id = Session::get('id');
                    $fees_group->session_id = Session::get('session_id');
                    $fees_group->branch_id = Session::get('branch_id');
                    $fees_group->name = $name;
                        $fees_group->fees_type = 'installment';
                        $fees_group->description = $request->description;
                        $fees_group->save();
                    }
                    return redirect::to('feesGroup')->with('message','Fees Group Created successfully');
                }
            }
            public function createFeesInstallmentClassWise(Request $request){
                if(!empty($request->installmentRow)){
                    $returnStatus['fees_master'] = [];
                    foreach($request->installmentRow as $key=> $row){
                        $returnStatus['entry'] = false;
                        $fees_group = FeesGroup::find($request->installment_id[$key]);          
                        if(!empty($fees_group)){
                            $fees_group = $fees_group;
                        }
                        $fees_group->user_id = Session::get('id');
                        $fees_group->session_id = Session::get('session_id');
                        $fees_group->branch_id = Session::get('branch_id');
                        $fees_group->name = $request->installment_name[$key];
                        $fees_group->fees_type = 'installment';
                        $fees_group->save();
                        if(!empty($request->installment_class_type_id)){
                            $fees_master = FeesMaster::where('fees_group_id' , $fees_group->id)->where('class_type_id' , $request->installment_class_type_id)->first();
                            if(!empty($fees_master)){
                                $fees_master = $fees_master;
                                $isUsed1 = FeesDetail::where('fees_group_id',$fees_group->id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->count();
                                $isUsed2 = FeesAssignDetail::where('fees_group_id',$fees_group->id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->count();
                                if(($isUsed1 + $isUsed2) == 0){
                                   $returnStatus['entry'] = true;
                                   $returnStatus['fees_master'][] = $fees_master->id;
                                }else{
                                   $returnStatus['entry'] = false;
                                }
                            }
                            else{
                                $fees_master = new FeesMaster; //model name
                                $returnStatus['entry'] = true;
                                $returnStatus['fees_master'][] = $fees_master->id;
                            }
                            $fees_master->user_id = Session::get('id');
                            $fees_master->session_id = Session::get('session_id');
                            $fees_master->branch_id = Session::get('branch_id');
                            $fees_master->fees_group_id = $fees_group->id;
                            $fees_master->amount = $request->installment_value[$key];
                            $fees_master->installment_month = $request->installment_month[$key];
                            $fees_master->installment_fine = $request->installment_fine[$key];
                            $fees_master->installment_due_date = $request->installment_due_date[$key];
                            $fees_master->class_type_id = $request->installment_class_type_id;
                            $fees_master->save();
                        }
                    }
                    $returnStatus['class_type_id'] = $request->installment_class_type_id;
                    return $returnStatus;
                }
            }
        
            public function assignFeesMultipleStudents(Request $request){
                if(!empty($request->admissionIds)){
                    foreach($request->admissionIds as $admission){
                        if(!empty($request->fees_master_ids)){
                            foreach($request->fees_master_ids as $master_id){
                                $fees_master = FeesMaster::find($master_id);
                                $fees_groups = FeesGroup::find($fees_master->fees_group_id);
                                $fees_assign_details = FeesAssignDetail::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                                ->where('fees_master_id',$master_id)->where('admission_id',$admission)->first();
                                if(empty($fees_assign_details)){       
                                    $feesAssign = FeesAssign::where('admission_id', $admission)->first();
                                    if(!empty($feesAssign)){
                                        $feesAssign = $feesAssign;
                                    }else{
                                        $feesAssign = new FeesAssign();
                                    }
                                    $feesAssign->user_id = Session::get('id');
                                    $feesAssign->session_id = Session::get('session_id');
                                    $feesAssign->branch_id = Session::get('branch_id');
                                    $feesAssign->admission_id = $admission;
                                    $feesAssign->save();
                                    $values = FeesAssignDetail::where('fees_assign_id',$feesAssign->id)
                                    ->where('fees_master_id',$fees_master->id)
                                    ->where('fees_group_id',$fees_master->fees_group_id)
                                    ->where('admission_id',$admission)
                                    ->first();
                                    if(!empty($values)){
                                        $values = $values;
                                    }else{
                                        $values = new FeesAssignDetail;
                                    }
                                    $values->user_id = Session::get('id');
                                    $values->branch_id = Session::get('branch_id');
                                    $values->session_id = Session::get('session_id');
                                    $values->fees_group_amount = $fees_master->amount;
                                    $values->admission_id = $admission;
                                    $values->fees_assign_id = $feesAssign->id;
                                    $values->class_type_id = $fees_master->class_type_id;
                                    $values->fees_master_id = $fees_master->id;
                                    $values->fees_group_id = $fees_master->fees_group_id;
                                    if (isset($fees_groups->fees_refund)) {
                                    $values->fees_refund = $fees_groups->fees_refund;
                                    } else {
                                    $values->fees_refund = 'no';
                                    }                                    $values->installment_month = $fees_master->installment_month;
                                    $values->installment_fine = $fees_master->installment_fine;
                                    $values->installment_due_date= $fees_master->installment_due_date;
                                    $values->save();
                                    $total_assign_detail = FeesAssignDetail::where('admission_id',$admission)->sum('fees_group_amount');
                                    $discount_assign_detail = FeesAssignDetail::where('admission_id',$admission)->sum('discount');
                                    $amountIncrement = FeesAssign::where('id',$feesAssign->id)->update(['total_amount'=>$total_assign_detail ]);
                                    $amountIncrement = FeesAssign::where('id',$feesAssign->id)->update(['net_amount'=>($total_assign_detail-$discount_assign_detail) ]);
                                    //   $amountIncrement = FeesAssign::where('id',$feesAssign->id)->increment('total_amount', $request->installment_value[$key] );
                                    //   $amountIncrement = FeesAssign::where('id',$feesAssign->id)->increment('net_amount', $request->installment_value[$key] );
                                }             
                            }
                        }
                    }
                    return redirect::to("feesMasterAdd")->with('message','Students Assigned Successfully');
                }
            }
        
            public function getMasterData(Request $request){
                $masterData = FeesMaster::select('fees_master.*','fees_group.name as fees_group_name')
                ->leftJoin('fees_group','fees_group.id','fees_master.fees_group_id')
                ->where('fees_master.class_type_id',$request->class_type_id)
                ->where('fees_master.session_id',Session::get('session_id'))
                ->where('fees_master.branch_id',Session::get('branch_id'))
                ->get();
                return $masterData; 
            }
        
            public function caReport(Request $request){
                $page = max(1, (int) $request->input('page', 1));
                $perPageRaw = $request->input('per_page', 25);
                $perPage = ($perPageRaw === 'all') ? 'all' : (int) $perPageRaw;
                if ($perPage !== 'all') {
                    $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
                }

                $search = [
                    'user_id'            => (string) $request->input('user_id', ''),
                    'class_type_id'      => (string) $request->input('class_type_id', ''),
                    'starting'           => (string) $request->input('starting', ''),
                    'ending'             => (string) $request->input('ending', ''),
                    'admission_no'       => (string) $request->input('admission_no', ''),
                    'invoice_no'         => (string) $request->input('invoice_no', ''),
                    'offline_receipt_no' => (string) $request->input('offline_receipt_no', ''),
                    'name'               => (string) $request->input('name', ''),
                    'father_name'        => (string) $request->input('father_name', ''),
                    'status'             => (string) $request->input('status', ''),
                    'payment_mode_id'    => (string) $request->input('payment_mode_id', ''),
                ];

                $baseQuery = FeesDetailsInvoices::leftJoin('admissions', 'admissions.id', '=', 'fees_details_invoices.admission_id')
                    ->leftJoin('class_types as class', 'class.id', '=', 'admissions.class_type_id')
                    ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
                    ->leftJoin('users', 'users.id', '=', 'fees_details_invoices.user_id')
                    ->where('fees_details_invoices.session_id', Session::get('session_id'))
                    ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                    ->where('admissions.school', 1);

                if (Session::get('role_id') > 1) {
                    $baseQuery->where('fees_details_invoices.user_id', Session::get('id'));
                }

                // Keyword search across student name, father name, mother name, mobile, aadhaar, admissionNo, and invoice numbers
                $keyword = trim($search['name']);
                if ($keyword !== '') {
                    $baseQuery->where(function($q) use ($keyword) {
                        $q->where('admissions.first_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.last_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.father_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.mother_name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.admissionNo', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.mobile', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('admissions.aadhaar', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('fees_details_invoices.invoice_no', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('fees_details_invoices.offline_receipt_no', 'LIKE', '%' . $keyword . '%');
                    });
                }

                // In-column specific filters
                if (!empty($search['admission_no'])) {
                    $baseQuery->where('admissions.admissionNo', 'LIKE', '%' . trim($search['admission_no']) . '%');
                }

                if (!empty($search['father_name'])) {
                    $baseQuery->where('admissions.father_name', 'LIKE', '%' . trim($search['father_name']) . '%');
                }

                if (!empty($search['invoice_no'])) {
                    $baseQuery->where('fees_details_invoices.invoice_no', 'LIKE', '%' . trim($search['invoice_no']) . '%');
                }

                if (!empty($search['offline_receipt_no'])) {
                    $baseQuery->where('fees_details_invoices.offline_receipt_no', 'LIKE', '%' . trim($search['offline_receipt_no']) . '%');
                }

                if (!empty($search['class_type_id'])) {
                    $baseQuery->where('admissions.class_type_id', (int) $search['class_type_id']);
                }

                if (!empty($search['user_id'])) {
                    $baseQuery->where('fees_details_invoices.user_id', (int) $search['user_id']);
                }

                if (!empty($search['payment_mode_id'])) {
                    $baseQuery->where('fees_details_invoices.payment_mode', (int) $search['payment_mode_id']);
                }

                if ($search['status'] !== '' && $search['status'] !== 'all') {
                    $baseQuery->where('fees_details_invoices.status', (int) $search['status']);
                }

                // Date range filters (payment_date)
                if (!empty($search['starting']) && !empty($search['ending'])) {
                    $baseQuery->whereBetween('fees_details_invoices.payment_date', [$search['starting'], $search['ending']]);
                } elseif (!empty($search['starting'])) {
                    $baseQuery->whereDate('fees_details_invoices.payment_date', '>=', $search['starting']);
                } elseif (!empty($search['ending'])) {
                    $baseQuery->whereDate('fees_details_invoices.payment_date', '<=', $search['ending']);
                }

                // Compute aggregate statistics for the filtered dataset in a single fast query
                $statsRaw = (clone $baseQuery)->selectRaw("
                    COUNT(fees_details_invoices.id) as total_count,
                    COALESCE(SUM(fees_details_invoices.amount), 0) as total_amount,
                    COALESCE(SUM(fees_details_invoices.discount), 0) as total_discount,
                    COALESCE(SUM(fees_details_invoices.total_fine), 0) as total_fine
                ")->first();

                $totalCount = (int) ($statsRaw->total_count ?? 0);
                $totalAmount = (float) ($statsRaw->total_amount ?? 0);
                $totalDiscount = (float) ($statsRaw->total_discount ?? 0);
                $totalFine = (float) ($statsRaw->total_fine ?? 0);
                $netCollected = $totalAmount + $totalFine;

                $stats = [
                    'total_count'   => $totalCount,
                    'total_amount'  => $totalAmount,
                    'total_discount'=> $totalDiscount,
                    'total_fine'    => $totalFine,
                    'net_collected' => $netCollected,
                ];

                // Ordering and Pagination
                $dataQuery = (clone $baseQuery)->select(
                    'fees_details_invoices.*',
                    'class.name as class_name',
                    'admissions.image',
                    'admissions.mobile',
                    'admissions.admissionNo',
                    'admissions.first_name',
                    'admissions.last_name',
                    'users.first_name as users_first_name',
                    'users.last_name as users_last_name',
                    'admissions.father_name',
                    'admissions.school',
                    'payment_modes.name as payment_mode',
                    'payment_modes.id as payment_mode_id'
                )->orderBy('fees_details_invoices.id', 'DESC');

                if ($perPage === 'all') {
                    $data = $dataQuery->get();
                    $startIndex = 0;
                    $lastPage = 1;
                } else {
                    $data = $dataQuery->forPage($page, $perPage)->get();
                    $startIndex = ($page - 1) * $perPage;
                    $lastPage = max(1, (int) ceil($totalCount / $perPage));
                }

                // AJAX Real-Time Data Response
                if ($request->ajax() || $request->wantsJson() || $request->input('ajax') == '1') {
                    $html = view('fees.reports.ca_report_rows', [
                        'data'       => $data,
                        'startIndex' => $startIndex,
                    ])->render();

                    return response()->json([
                        'status'       => true,
                        'html'         => $html,
                        'total'        => $totalCount,
                        'from'         => $totalCount > 0 ? $startIndex + 1 : 0,
                        'to'           => $perPage === 'all' ? $totalCount : min($startIndex + count($data), $totalCount),
                        'current_page' => $page,
                        'last_page'    => $lastPage,
                        'per_page'     => $perPage,
                        'stats'        => $stats,
                    ]);
                }

                $paymentModes = Helper::getPaymentMode();
                $allUsers = Helper::getAllUsers();
                $classType = Helper::classType();

                return view('fees.reports.CA', [
                    'data'         => $data,
                    'search'       => $search,
                    'totalCount'   => $totalCount,
                    'startIndex'   => $startIndex,
                    'lastPage'     => $lastPage,
                    'currentPage'  => $page,
                    'perPage'      => $perPage,
                    'stats'        => $stats,
                    'paymentModes' => $paymentModes,
                    'allUsers'     => $allUsers,
                    'classType'    => $classType,
                ]);
            }
        
            public function fees_cheque(Request $request){
                $search['name'] = $request->name;
                $search['class_type_id'] = $request->class_type_id ?? '';
                $serach['starting'] = $request->starting;
                $serach['ending'] = $request->ending;
                if ($request->isMethod('post')) {
                    $update = FeesDetailsInvoices::find($request->id);
                    
                    if(!empty($update))
                    {
                        $update->status = $request->status_id ?? '';
                        $update->remark = $request->remark ?? '';
                        $update->save();
                        $feesDetailsId = explode(',', $update->fees_details_id); // Convert string to array

                if (!empty($feesDetailsId)) {
        $fees_ = FeesDetail::whereIn('id', $feesDetailsId)->update(['status' => 0]);
        
    } 

                    }
                }
                $data =  FeesDetailsInvoices::select('fees_details_invoices.*','class.name as class_name','admissions.admissionNo','admissions.mobile','admissions.first_name'
                ,'admissions.last_name','admissions.father_name','admissions.school','payment_modes.name as payment_mode','payment_modes.id as payment_mode_id')
                ->leftjoin('admissions as admissions', 'admissions.id', 'fees_details_invoices.admission_id')
                ->leftjoin('class_types as class','class.id','admissions.class_type_id')
                ->leftjoin('payment_modes','payment_modes.id','fees_details_invoices.payment_mode')
                ->where('fees_details_invoices.session_id', Session::get('session_id'))
                ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                ->where('fees_details_invoices.status', 1);
                if (Session::get('role_id') == 2) {
                    $data = $data->where('admissions.class_type_id', Session::get('class_type_id'));
                } 
                $data = $data->where('school', '>', 0)->orderBy('fees_details_invoices.payment_date','DESC')->get();
               return view('fees.fees_cheque', ['data' => $data, 'search' => $search]);
            }
     

    
}
