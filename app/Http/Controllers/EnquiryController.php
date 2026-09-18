<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\ClassType;
use App\Models\Setting;
use App\Models\FeesMaster;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Teacher;
use App\Models\State;
use App\Models\Remark;
use App\Models\City;
use App\Models\Sessions;
use App\Models\IdCardTemplate;
use App\Models\Master\MessageTemplate;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use App\Models\Master\SchoolDesk;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use App\Models\MessageQueue;
use App\Jobs\SendMessageJob;
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
use Detection\MobileDetect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class EnquiryController extends Controller
{
    private function clearEnquiryCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: (Session::get('admin_branch_id') ?: (Session::get('branch_id') ?: 0));
        $sessionId = $sessionId ?: (Session::get('session_id') ?: 0);

        if ($branchId !== null && $sessionId !== null) {
            $versionKey = "enquiry_version_{$branchId}_{$sessionId}";
            if (Cache::has($versionKey)) {
                Cache::increment($versionKey);
            } else {
                Cache::forever($versionKey, 2);
            }
        }
    }

    private function getEnquiryCacheVersion($branchId, $sessionId)
    {
        $branchId = $branchId ?: 0;
        $sessionId = $sessionId ?: 0;
        $versionKey = "enquiry_version_{$branchId}_{$sessionId}";
        return Cache::rememberForever($versionKey, function () {
            return 1;
        });
    }

    public function studentsDashboard(){
                
                $detect = new MobileDetect;

                if ($detect->isMobile() && !$detect->isTablet()) {
                    return view('students/studentsDashboard'); // Mobile view file
                }

                return view('students/studentsDashboard');
            }
 
            public function qrCode(Request $request){
         
                $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('type', 'StudentRegistration')->first();
                
                $BillCounterNo = null; // Initialize BillCounterNo variable
            
                if(Session::get('role_id') > 1 && !empty($BillCounter)){
                    $BillCounter = $BillCounter->where('branch_id',Session::get('branch_id'));
                }
                
                if (!empty($BillCounter)) {
                    $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                    $BillCounterNo = $counter + 1;
                }
                
                if ($request->isMethod('post')) {
                    $request->validate([
                        'first_name' => 'required',
                        'mobile' => 'required|digits:10',
                        'father_name' => 'required',
                    ]);
            
                    // Increment counter
                    if (!empty($BillCounter)) {
                        $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                        $BillCounter->counter = $counter + 1;
                        $BillCounter->save();
                    }
            
                    $addqr = new Enquiry;
                    $addqr->user_id = Session::get('id');
                    $addqr->session_id = 3;
                    $addqr->branch_id = 1;
                    $addqr->registration_no = $BillCounterNo; // Use BillCounterNo here
                    $addqr->first_name = $request->first_name;
                    $addqr->mobile = $request->mobile;
                    $addqr->father_name = $request->father_name;
                    $addqr->remark_1 = $request->remark_1;
                    $addqr->status = 2;
                    
                    $addqr->save();
                    $this->clearEnquiryCache($addqr->branch_id, $addqr->session_id);
                    
                    return redirect::to('qr_code')->with('message', 'Enquiry Add Successful.');
                }
            
                return view('students.enquiry.enquiry_qrCode',['BillCounter' => $BillCounterNo]);
            }

            public function enquiryAdd(Request $request)
{
    // load BillCounter (may be null)
    $BillCounter = BillCounter::where('session_id', Session::get('session_id'))
                    ->where('type', 'StudentRegistration')
                    ->where('branch_id', Session::get('branch_id'))
                    ->first();

    // If GET -> show form
    if (!$request->isMethod('post')) {
        // users for "Assigned By" (only active users in branch)
        $users = User::where('status', 1)
                     ->where('branch_id', Session::get('branch_id'))
                     ->get();

        // enquiry_status values for reference & response
        $references = DB::table('enquiry_status')
                        ->where('type', 'reference')
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->get();

        $responses = DB::table('enquiry_status')
                        ->where('type', 'response')
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->get();

        return Helper::view('students.enquiry.add', [
            'BillCounter' => $BillCounter,
            'users' => $users,
            'references' => $references,
            'responses' => $responses
        ]);
    }

    // POST -> validate + save
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'father_name' => 'required|string|max:255',
        'mother_name' => 'nullable|string|max:255',
        'gender_id' => 'required|integer',
        'mobile' => 'required|digits:10',
        'email' => 'nullable|email|max:255',
        'class_type_id' => 'nullable|integer',
        'dob' => 'nullable|date',
        'assigned_by' => 'nullable|integer',
        'reference_id' => 'nullable|integer',
        'response_id' => 'nullable|integer',
        'no_of_child' => 'nullable|integer',
    ]);

    DB::beginTransaction();
    try {
        // increment BillCounter
        if (!$BillCounter) {
            $BillCounter = new BillCounter();
            $BillCounter->session_id = Session::get('session_id');
            $BillCounter->branch_id = Session::get('branch_id');
            $BillCounter->type = 'StudentRegistration';
            $BillCounter->counter = 1;
            $BillCounter->save();
        } else {
            $BillCounter->counter = (!empty($BillCounter->counter) ? $BillCounter->counter : 0) + 1;
            $BillCounter->save();
        }

        $enquiry = new Enquiry;
        $enquiry->user_id = Session::get('id');
        $enquiry->session_id = Session::get('session_id');
        $enquiry->branch_id = Session::get('branch_id');
        $enquiry->first_name = $request->first_name;
        $enquiry->email = $request->email ?? null;
        $enquiry->mobile = $request->mobile ?? null;
        $enquiry->class_type_id = $request->class_type_id ?? null;
        $enquiry->father_name = $request->father_name;
        $enquiry->mother_name = $request->mother_name ?? null;
        $enquiry->dob = $request->dob ?? null;
        $enquiry->gender_id = $request->gender_id;
        $enquiry->registration_date = Carbon::now()->toDateString();
        $enquiry->previous_school = $request->previous_school ?? null;
        $enquiry->address = $request->address ?? null;
        $enquiry->response = $request->response ?? null; // text
        $enquiry->note = $request->note ?? null;
        $enquiry->no_of_child = $request->no_of_child ?? null;
        $enquiry->assigned_by = $request->assigned_by ?? null;
        $enquiry->reference_id = $request->reference_id ?? null;
        $enquiry->response_id = $request->response_id ?? null;
        $enquiry->save();
        $this->clearEnquiryCache($enquiry->branch_id, $enquiry->session_id);

        // enqueue SMS / message (if mobile present)
        $toMobile = $request->mobile ?? $request->father_mobile ?? null;
        if ($toMobile) {
            $message = MessageQueue::create([
                'receiver_number' => $toMobile,
                'content' => 'Thank you for enquiry',
                'message_status' => 0,
                'submitted_at' => now(),
            ]);
            SendMessageJob::dispatch($message);
        }

        // optional: email / template sending (safe checks)
        $template = MessageTemplate::select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                    ->leftJoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.slug','student-registration')
                    ->first();

        if ($template && $template->message_type_status != 1) {
            $branch = Branch::find(Session::get('branch_id'));
            $setting = Setting::where('branch_id', Session::get('branch_id'))->first();

            $arrey1 = ['{#name#}','{#school_name#}','{#email#}','{#mobile#}'];
            $arrey2 = [$request->first_name." ".$request->last_name, $setting->name ?? '', $request->email ?? '', $toMobile ?? ''];

            $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                 
                                 
                                        if ($setting->firebase_notification == 1) {
                                            Helper::sendNotification(
                                                $template->title ?? 'Attendence',
                                                $whatsapp,
                                                'student',
                                                $request->id
                                            ); 
                                        }
                                    if ($template->message_type_status == 1) {
                                         if ($branch->whatsapp_srvc == 1) {
                                            if (!empty($toMobile)) {
                                                Helper::MessageQueue($toMobile, $whatsapp);
                                            }
                                        }
                                     }
        }

        DB::commit();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Enquiry Added Successfully.']);
        }
        return redirect()->back()->with('success', 'Enquiry Added Successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Enquiry add error: '.$e->getMessage());
        if ($request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Server error.']);   
        }
        return redirect()->back()->with('error', 'Something went wrong. Please check logs.');
    }
}

public function enquiryView(Request $request)
{
    $search = [
        'enquiry_status' => $request->enquiry_status,
        'state_id'       => $request->state_id,
        'city_id'        => $request->city_id,
        'class_type_id'  => $request->class_type_id,
        'name'           => $request->name,
        'reminder_date'  => $request->reminder_date,
    ];

    $branchId = Session::get('admin_branch_id') ?: (Session::get('branch_id') ?: 0);
    $sessionId = Session::get('session_id') ?: 0;
    $roleId = Session::get('role_id') ?: 0;

    $version = $this->getEnquiryCacheVersion($branchId, $sessionId);

    // Multi-user filter cache key based on branch, session, version and search query
    $filterHash = md5(json_encode([
        'is_post' => $request->isMethod('post'),
        'search'  => $search,
        'role_id' => $roleId,
    ]));

    $cacheKey = "enquiry_list_{$branchId}_{$sessionId}_v{$version}_{$filterHash}";

    $allstudents = Cache::remember($cacheKey, 1800, function () use ($request, $branchId, $roleId) {
        $data = Enquiry::select(
            'enquirys.*',
            'class_types.name as class_name',
            'reference.name as reference_name'
        )
        ->leftJoin('class_types', 'class_types.id', '=', 'enquirys.class_type_id')
        ->leftJoin('enquiry_status as reference', 'reference.id', '=', 'enquirys.reference_id');

        if ($roleId > 1) {
            $data->where('enquirys.branch_id', Session::get('branch_id'));
        }
        if (!empty($branchId)) {
            $data->where('enquirys.branch_id', $branchId);
        }

        if ($request->isMethod('post')) {
            if (!empty($request->name)) {
                $value = $request->name;
                $data->where(function ($query) use ($value) {
                    $query->where('enquirys.first_name', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.last_name', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.mobile', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.email', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.id_number', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.father_name', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.mother_name', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.address', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.previous_school', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.response', 'like', '%' . $value . '%')
                        ->orWhere('enquirys.note', 'like', '%' . $value . '%');
                });
            }
            if (!empty($request->state_id)) {
                $data->where("enquirys.state_id", $request->state_id);
            }
            if (!empty($request->city_id)) {
                $data->where("enquirys.city_id", $request->city_id);
            }
            if (!empty($request->enquiry_status)) {
                $data->where("enquirys.enquiry_status", $request->enquiry_status);
            }
            if (!empty($request->reminder_date)) {
                $data->whereDate("enquirys.reminder_date", $request->reminder_date);
            }
            if (!empty($request->class_type_id)) {
                $data->where("enquirys.class_type_id", $request->class_type_id);
            }
        }

        $students = $data->orderBy('enquirys.id', 'DESC')->get();

        // High-speed bulk query for latest remark (Eliminating N+1 queries)
        if ($students->isNotEmpty()) {
            $studentIds = $students->pluck('id')->all();
            $remarks = Remark::whereIn('Student_id', $studentIds)
                ->orderBy('id', 'DESC')
                ->get()
                ->groupBy('Student_id');

            foreach ($students as $student) {
                $latestStatus = $student->status;
                $latestFollowupDate = $student->next_follow_up_date;
                $latestNote = $student->note;
                if (isset($remarks[$student->id])) {
                    $latestRemark = $remarks[$student->id]->first();
                    if ($latestRemark && !empty($latestRemark->remark)) {
                        $lines = explode("\n", $latestRemark->remark);
                        foreach ($lines as $line) {
                            if (str_starts_with($line, 'Status: ')) {
                                $latestStatus = str_replace('Status: ', '', $line);
                            }
                            if (str_starts_with($line, 'Next Follow Up: ')) {
                                $latestFollowupDate = str_replace('Next Follow Up: ', '', $line);
                            }
                            if (str_starts_with($line, 'Note: ')) {
                                $latestNote = str_replace('Note: ', '', $line);
                            }
                        }
                    }
                }
                $student->latest_status = $latestStatus ?: 'New';
                $student->latest_followup_date = $latestFollowupDate;
                $student->latest_note = $latestNote;
            }
        }

        return $students;
    });

    if ($request->pdf == "pdf") {
        $pdf = PDF::loadView('print_file.pdf.registration_list', ['data' => $allstudents]);
        return $pdf->download('student_registration.pdf');
    }

    return Helper::view('students.enquiry.view', [
        'data' => $allstudents,
        'search' => $search
    ]);
}


public function enquiryEdit(Request $request, $id)
{
    $data = Enquiry::findOrFail($id);

    if ($request->isMethod('post')) {
        $request->validate([
            'first_name'   => 'required',
            'gender_id'    => 'required',
            'mobile'       => 'required|digits:10',
            'father_name'  => 'required',
            'mother_name'  => 'required',
        ]);

        $data->fill($request->only([
            'first_name','last_name','mobile','email','gender_id','dob',
            'father_name','mother_name','father_mobile','class_type_id',
            'id_proof','id_number','remark_1','previous_school','assigned_by',
            'reference_id','response_id','response','note','no_of_child','address'
        ]));

        $data->user_id = Session::get('id');
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');
        $data->registration_date = now();
        $data->save();
        $this->clearEnquiryCache($data->branch_id, $data->session_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Enquiry Updated Successfully.',
            'redirect'=> url('enquiryView')
        ]);
    }

    $users      = User::where('status',1)->get();
    $references = DB::table('enquiry_status')->where('type','reference')->whereNull('deleted_at')->get();
    $responses  = DB::table('enquiry_status')->where('type','response')->whereNull('deleted_at')->get();

    return view('students.enquiry.edit', compact('data','users','references','responses'));
}

            public function enquiryDelete(Request $request){
                $enquiry = Enquiry::find($request->delete_id);
                if ($enquiry) {
                    $branchId = $enquiry->branch_id;
                    $sessionId = $enquiry->session_id;
                    $enquiry->delete();
                    $this->clearEnquiryCache($branchId, $sessionId);
                }
                return redirect::to('enquiryView')->with('message', 'Enquiry Deleted Successfully !');
            }

            public function studentIdPrintMultiple(Request $request){
                $request->validate([
                    'checkbox'  => 'required',
                ]);
                $templates = IdCardTemplate::where('branch_id', Session::get('branch_id'))->get();
                $ids =  Admission::Select('admissions.*','class_types.name as class_name')
                ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                ->find($request->checkbox);
                 return view('print_file.student_print.multipal_id_print', ['data' => $ids,'templates' => $templates]);
            }

            // public function studentIdPrintMultiple(Request $request){
            //     $request->validate([
            //         'checkbox'  => 'required',
            //     ]);
            //     $ids =  Admission::Select('admissions.*','class_types.name as class_name','sessions.from_year','sessions.to_year')
            //     ->leftjoin('class_types','class_types.id','admissions.class_type_id')
            //     ->leftjoin('sessions','sessions.id','admissions.session_id')
            //     ->find($request->checkbox);
            //     // $ids = Admission::select("*")->find($request->checkbox);
            //     //$student_id =  Admission::find($id);
            //     //   $printPreviewId = Helper::printPreview('Student Id Print');
            //     //      return view($printPreviewId, ['data' => $ids]);
            //      return view('print_file.student_print.multipal_id_print', ['data' => $ids]);
            // }

            public function registrationPrint(Request $request, $id){
                $registration =  Enquiry::with('ClassTypes')->with('Gender')->find($id);
                $printPreview =    Helper::printPreview('Enquiry Print');
                //dd($printPreview);
                return view($printPreview, ['data' => $registration]);
                //return view('print_file.student_print.registration_print', ['data' => $registration]);
            }
 


            public function class_type_search(Request $request){
                if (!empty($request->class_type_id)) {
                    $data = array();
                    $data = Classs::where('class_id', $request->class_type_id)->get();
                    $stateData = '';
                    foreach ($data as $class) {
                        $stateData .= '
                        <option value="' . $class->id . '">' . $class->name . '</option>';
                    }
                    echo $stateData;
                }
            }
            
public function studentRegistrationDetail(Request $request, $id)
{
    $data = Enquiry::find($id);

    $remark = Remark::where('Student_id', $data['id'])
                    ->orderBy('id', 'DESC')
                    ->get();

    $latestStatus = $data->status;

    if ($remark->isNotEmpty()) {
        $latestRemark = $remark->first();
        $lines = explode("\n", $latestRemark->remark);

        foreach ($lines as $line) {
            if (str_starts_with($line, 'Status: ')) {
                $latestStatus = str_replace('Status: ', '', $line);
                break;
            }
        }
    }

    return view('students.enquiry.studentRegistrationDetail', [
        'data' => $data,
        'remark' => $remark,
        'latestStatus' => $latestStatus
    ]);
}


public function enquiryFollowUpAdd(Request $request, $id)
    {
        $request->validate([
            'next_follow_up_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $enquiry = Enquiry::findOrFail($id);

        // Update enquiry
        $enquiry->follow_up_date = $request->follow_up_date ?? now();
        $enquiry->next_follow_up_date = $request->next_follow_up_date;
        $enquiry->status = $request->status;
        $enquiry->response = $request->response;
        $enquiry->note = $request->note;
        $enquiry->save();
        $this->clearEnquiryCache($enquiry->branch_id, $enquiry->session_id);

        // Save in remark table
        Remark::create([
            'student_id' => $enquiry->id,
            'date'       => now(),
            'remark'     => "Follow Up: ".$request->response."\n".
                            "Status: ".$request->status."\n".
                            "Next Follow Up: ".$request->next_follow_up_date."\n".
                            "Note: ".$request->note
        ]);

        return redirect()->back()->with('message','Follow Up Added Successfully');
    }

    // Delete Follow Up
    public function followupDelete(Request $request)
    {
        $remark = Remark::findOrFail($request->delete_id);
        $enquiry = Enquiry::find($remark->student_id);

        if ($enquiry) {
            $enquiry->next_follow_up_date = null;
            $enquiry->status = null;
            $enquiry->save();
            $this->clearEnquiryCache($enquiry->branch_id, $enquiry->session_id);
        } else {
            $this->clearEnquiryCache();
        }

        $remark->delete();

        return back()->with('message','Follow Up deleted successfully');
    }

          
            public function enquiry_qr_generate(){
                $file= QrCode::size(300)->generate('https://techvblogs.com/blog/generate-qr-code-laravel-8') ;
                return Response::download($file);
            }
}
