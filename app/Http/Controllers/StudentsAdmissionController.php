<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\RollNumber;
use App\Models\StudentId;
use App\Models\StudentAction;
use App\Models\exam\FillMarks;
use App\Models\Classs;
use App\Models\ClassType;
use App\Models\Subject;
use App\Models\Sessions;
use App\Models\Master\Branch;
use App\Models\TcCertificate;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\BloodGroup;
use App\Models\DatatableFields;
use App\Models\FeesMaster;
use App\Models\FeesCollect;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\fees\FeesAssignDetail;
use App\Models\WhatsappSetting;
use App\Models\FeesStructure;
use App\Models\FeesDetail;
use App\Models\StudentDocument;
use App\Models\StudentAttendance;
use App\Models\AttendanceMark;
use App\Models\Setting;
use App\Models\State;
use App\Models\Gender;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\City;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\StudentField;
use Session;
use PDF;
use Helper;
use Str;
use Mail;
use File;
use DB;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Imports\YourImportClassName;
use App\Exports\StudentProfileReportExport;
use App\Exports\LoginCredentialReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;


class StudentsAdmissionController extends Controller
{
    /**
     * Clear admission cache for a specific branch & session, or globally
     */
    public static function clearAdmissionCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?? Session::get('branch_id');
        $sessionId = $sessionId ?? Session::get('session_id');

        if ($branchId && $sessionId) {
            $versionKey = "admission_cache_ver_{$branchId}_{$sessionId}";
            Cache::increment($versionKey);
        } else {
            Cache::increment('admission_cache_global_ver');
        }
    }

    /**
     * Get admission cache version string for a branch & session
     */
    public static function getAdmissionCacheVersion($branchId, $sessionId)
    {
        $versionKey = "admission_cache_ver_{$branchId}_{$sessionId}";
        $globalVer = Cache::get('admission_cache_global_ver', 1);
        $branchVer = Cache::remember($versionKey, 86400 * 30, function () {
            return 1;
        });
        return "{$globalVer}_{$branchVer}";
    }

           /*public function unique_system_id(){
                $data = Admission::whereNull('unique_system_id')->get();
                
                if(!empty($data)){ 
                    foreach($data as $item){
                        $find = Admission::find($item->id);
                        $uniqueId = strtoupper(Str::random(10));
                        $find->unique_system_id = $uniqueId;
                        $find->save();
                    }
                }
            }*/
    
            protected function unique_system_id($id){
                $uniqueId = strtoupper(Str::random(10));
                Admission::where('id',$id)->whereNull('unique_system_id')->update(['unique_system_id' => $uniqueId]);
            }
    
            protected function convertExcelDate($date){
                if(is_numeric($date)) {
                        return Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($date);
                    }elseif (is_string($date)) {
                        try{
                            return Carbon::createFromFormat('Y-m-d', $date);
                        } catch (\Exception $e) {
                            try{
                                return Carbon::createFromFormat('d-m-Y', $date);
                            }catch (\Exception $e) {
                                try {
                                    return Carbon::createFromFormat('m-d-Y', $date);
                                }catch (\Exception $e) {
                                        return null;
                                }
                            }
                        }
                    }
                     return null;
            }
            public function saveAdmissionDatatableFields(Request $request){
                $allowedFields = array_keys(Helper::getAdmissionDatatableFields());
                $allowedFields = array_values(array_diff($allowedFields, ['SR.NO']));
                array_unshift($allowedFields, 'Biomax');

                $request->validate([
                    'fields' => 'nullable|array',
                    'fields.*' => ['string', Rule::in($allowedFields)],
                ]);

                $selectedFields = array_values(array_intersect(
                    $allowedFields,
                    $request->input('fields', [])
                ));

                DatatableFields::updateOrCreate(
                    ['user_id' => Session::get('id')],
                    ['fields' => implode(',', $selectedFields)]
                );
                self::clearAdmissionCache();
                return redirect::to('admissionView')->with('message','Datatable Fields Selected Successfully');
            }

            private function admissionVisibleColumns(): array
            {
                $availableFields = array_keys(Helper::getAdmissionDatatableFields());
                $availableFields = array_values(array_diff($availableFields, ['SR.NO']));
                array_unshift($availableFields, 'Biomax');

                $preference = DatatableFields::where('user_id', Session::get('id'))->first();
                if (!$preference) {
                    return $availableFields;
                }

                $savedFields = array_filter(explode(',', (string) $preference->fields));
                return array_values(array_intersect($availableFields, $savedFields));
            }
    
            public function admissionStudentPrint(Request $request, $id){
                $student_admission = Admission::select('admissions.*', 'sessions.from_year', 'class_types.name as class_name','sessions.to_year', 'gender.name as genderName','countries.name as country_name','states.name as state_name','citys.name as city_name')
                ->leftJoin('gender','gender.id','admissions.gender_id')
                ->leftjoin('countries','countries.id','admissions.country_id')
                ->leftjoin('states','states.id','admissions.state_id')
                ->leftjoin('citys','citys.id','admissions.city_id')
                ->leftjoin('sessions','sessions.id','admissions.session_id')
                ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                ->where('admissions.id',$id)->first();
                $printPreview = Helper::printPreview('Admission Print');
                //dd($printPreview);
                return view($printPreview, ['data' => $student_admission]);
               // return view('print_file.student_print.admissionStudentPrint', ['data' => $student_admission]);
            }

            public function studentProfileExcel($id)
            {
                $profileResponse = $this->studentDetail($id);

                if (!($profileResponse instanceof \Illuminate\View\View)) {
                    return $profileResponse;
                }

                $profileData = $profileResponse->getData();
                $student = $profileData['data'];
                $studentName = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim(
                    ($student->first_name ?? 'student').'-'.($student->last_name ?? '')
                ));
                $fileName = trim($studentName, '-').'-student-profile-'.date('Y-m-d').'.xlsx';

                return Excel::download(new StudentProfileReportExport($profileData), $fileName);
            }
    
            public function getStreamSubjects(Request $request){
                $data = Subject::where('class_type_id',$request->class_type_id)->get();
                return $data;
            }

            public function admissionAdd(Request $request){
                //dd($request);
                $Student = Enquiry::where('id',$request->registration_id)->update(['ad_status'=>'Admission']);
                //dd($Student);

                $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type', 'StudentAdmission')->get()->first();
                    if (!empty($BillCounter)) {
                        $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                        $BillCounterNo = $counter + 1;
                    }
                    
                    if ($request->isMethod('post')) {
                         // fresh query हर बार
                        $student_fields_required = DB::table('student_fields')
                            ->whereNull('deleted_at')
                            ->get(['field_name', 'status', 'required'])
                            ->keyBy('field_name'); // toArray() न करें
                    
                        $rules = [];
                    
                        foreach ($student_fields_required as $field => $obj) {
                            if ($obj->status == 0 && $obj->required == 0) {
                                if ($field === 'mobile' || $field === 'father_mobile') {
                                    $rules[$field] = 'required|digits:10';
                                } else {
                                    $rules[$field] = 'required';
                                }
                            }
                        }
                        $sessionId = Session::get('session_id');
                        $request->validate(array_merge($rules, [
                            'admissionNo' => [
                                'nullable',
                                Rule::unique('admissions', 'admissionNo')
                                    ->where(function ($query) use ($sessionId) {
                                        $query->where('session_id', $sessionId)->whereNull('deleted_at');
                                    }),
                            ],
                            'mobile' => [
                                'nullable',
                                'digits:10',
                                Rule::unique('admissions', 'mobile')
                                    ->where(function ($query) use ($sessionId) {
                                        $query->where('session_id', $sessionId)->whereNull('deleted_at');
                                    }),
                            ],
                        ]));
                           
                        $student_image = '';
                    if ($request->file('student_img')) {
                        $image = $request->file('student_img');
                        $ext = $image->getClientOriginalExtension(); // jpg, png, jpeg आदि
                        $student_image = ($request->admissionNo ??  uniqid()). '.' . $ext;
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if (isset($data->image) && File::exists($destinationPath . $data->image)) {
                        File::delete($destinationPath . $data->image);
                    }
                    $compressedImage = Image::make($image)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80); // Adjust quality as needed
                        $compressedImage->save($destinationPath . $student_image);
                        
                    }
                   
                    $father_image = '';
                    if ($request->file('father_img')) {
                        $image = $request->file('father_img');
                        $father_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_image/';
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if (isset($data->father_image) && File::exists($destinationPath . $data->father_image)) {
                        File::delete($destinationPath . $data->father_image);
                    }
                    $compressedImage = Image::make($image)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80); // Adjust quality as needed
                        $compressedImage->save($destinationPath . $father_image);
                        
                    }
                  
                    $mother_image = '';
                    if ($request->file('mother_img')) {
                        $image = $request->file('mother_img');
                        $mother_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_image/';
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if (isset($data->mother_img) && File::exists($destinationPath . $data->mother_img)) {
                        File::delete($destinationPath . $data->mother_img);
                    }
                    $compressedImage = Image::make($image)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80); // Adjust quality as needed
                        $compressedImage->save($destinationPath . $mother_image);
                       
                    }
                   
                        $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                        $BillCounter->counter = $counter + 1;
                        $BillCounter->save();
                        $maxId = Admission::selectRaw('MAX(CAST(attendance_unique_id AS UNSIGNED)) as max_id')
            ->value('max_id');

                        $addadmission = new Admission(); //model name
                        $addadmission->user_id = Session::get('id');
                        $addadmission->session_id = Session::get('session_id');
                        $addadmission->branch_id = Session::get('branch_id');
                        
                        
                        $addadmission->admissionNo = $request->admissionNo;
                        $addadmission->ledger_no = $request->ledger_no;
                        $addadmission->student_pen = $request->student_pen;
                        $addadmission->apaar_id = $request->apaar_id;
                        $addadmission->school = '1';
                        $addadmission->library = '0';
                        $addadmission->hostel = '0';
                        $addadmission->roll_no = $request->roll_no;
                        $addadmission->admission_date = $request->admission_date;
                        $addadmission->admission_type_id = $request->admission_type_id;
                        $addadmission->class_type_id = $request->class_type_id;
                            if(!empty($request->stream_subject)){
                                $addadmission->stream_subject = implode(',', $request->stream_subject);
                            }
                            $addadmission->attendance_unique_id = str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
                            $addadmission->first_name = $request->first_name;
                            $addadmission->last_name = $request->last_name;
                            $addadmission->aadhaar = $request->aadhaar;
                            $addadmission->jan_aadhaar = $request->jan_aadhaar;
                            $addadmission->previous_school = $request->previous_school;
                            $addadmission->email = $request->email;
                            $addadmission->mobile = $request->mobile;
                            $addadmission->father_name = $request->father_name;
                            $addadmission->mother_name = $request->mother_name;
                            $addadmission->father_mobile = $request->father_mobile;
                            $addadmission->dob = $request->dob;
                            $addadmission->relation_student = $request->relation_student;
                            $addadmission->school_namestudied_last_year = $request->school_namestudied_last_year;
                            $addadmission->house = $request->house;
                            $addadmission->height = $request->height;
                            $addadmission->weight = $request->weight;
                            $addadmission->gender_id = $request->gender_id;
                            $addadmission->admission_type_id = $request->admission_type_id;
                            $addadmission->blood_group = $request->blood_group;
                            $addadmission->medium = $request->medium;
                            $addadmission->address = $request->address;
                            $addadmission->country_id = $request->country;
                            $addadmission->village_city = $request->village_city;
                            $addadmission->city_id = $request->city;
                            $addadmission->state_id = $request->state;
                            $addadmission->pincode = $request->pincode;
                            $addadmission->family_id = $request->family_id;
                            $addadmission->religion = $request->religion;
                            $addadmission->category = $request->category;
                            $addadmission->caste_category = $request->caste_category;
                            $addadmission->transport = $request->transport;
                            $addadmission->bus_number = $request->bus_number;
                            $addadmission->bus_route = $request->bus_route;
                            $addadmission->stoppage = $request->stoppage;
                            $addadmission->transpor_charges = $request->transpor_charges;
                            $addadmission->guardian_name = $request->guardian_name;
                            $addadmission->guardian_mobile = $request->guardian_mobile;
                            $addadmission->mother_mob = $request->mother_mob;
                            $addadmission->father_aadhaar = $request->father_aadhaar;
                            $addadmission->mother_aadhaar = $request->mother_aadhaar;
                            $addadmission->family_annual_income = $request->family_annual_income;
                            $addadmission->bank_account = $request->bank_account;
                            $addadmission->bank_name = $request->bank_name;
                            $addadmission->branch_name = $request->branch_name;
                            $addadmission->ifsc = $request->ifsc;
                            $addadmission->micr_code = $request->micr_code;
                            $addadmission->image = $student_image;
                            $addadmission->father_img = $father_image;
                            $addadmission->mother_img = $mother_image;
                            $addadmission->remark_1 = $request->remark_1;
                            $addadmission->bank_account_holder = $request->bank_account_holder;
                            $addadmission->district = $request->district;
                            $addadmission->tehsil = $request->tehsil;
                            $addadmission->father_pancard = $request->father_pancard;
                            $addadmission->mother_pancard = $request->mother_pancard;
                            $addadmission->bpl = $request->bpl;
                            $addadmission->bpl_certificate_no = $request->bpl_certificate_no;
                            $addadmission->father_occupation = $request->father_occupation;
                            $addadmission->mother_occupation = $request->mother_occupation;
                            $addadmission->password = Hash::make($request->admissionNo);
                            $addadmission->confirm_password = $request->admissionNo;
                            $status = 1;
                            if(($request->newStudentRegistration ?? '') == 'newStudentRegistration'){
                                $status = 'newStudentRegistration';
                            }
                            $addadmission->status = $status;
                            
                            $class_name = ClassType::find($request->class_type_id);
                            $initials = substr($request->first_name, 0, 3);
                            $birthYear = date('Y', strtotime($request->dob));
                            $random_number = Str::random(10);
                            $cleanedMobile = preg_replace('/[^0-9]/', '', $request->mobile ?? $random_number);
                            $username = strtoupper($initials).strtoupper($class_name->name).substr($cleanedMobile, -3);
                            $addadmission->userName = $request->admissionNo;
                            
                            $studentFields = StudentField::where('branch_id', Session::get('branch_id'))->where('type', 'new_input')->get();
                            if(!empty($studentFields)){
                                foreach ($studentFields as $field) {
                                    if ($field->field_type == 'checkbox') {
                                        // Checkbox multiple values (array) → string में save
                                        $addadmission->{$field->field_name} = implode(',', $request->input($field->field_name, []));
                                    } 
                                    else {
                                        // बाकी सब direct save
                                        $addadmission->{$field->field_name} = $request->input($field->field_name);
                                    }
                                    }
                                }
                            $addadmission->save();

                            $addadmission->attendance_unique_id = 'AI' . $addadmission->id;
                            $addadmission->save();
                            
                            $addadmission_id = $addadmission->id;
                            $this->unique_system_id($addadmission_id);
                            
                            $feesGroup = new FeesAssign();
                            $feesGroup->user_id = Session::get('id');
                            $feesGroup->session_id = Session::get('session_id');
                            $feesGroup->branch_id = Session::get('branch_id');
                            $feesGroup->admission_id = $addadmission_id;
                            $feesGroup->save();
                            $feesGroupId = $feesGroup->id;
                
                            $assign_count =0;
                            $fees_group_amount =0;
                            $fees_group_discount =0;
                            
                            if (!empty($request->fees_master_id)) {
                                $assign_count = 0; // Ensure $assign_count is defined
                                if (is_array($request->fees_assign)) { // Check if $request->fees_assign is an array
                                    for ($count = 0; $count < count($request->fees_master_id); $count++) {
                                        if (in_array($request->fees_master_id[$count], $request->fees_assign)) {
                                            $feesGroupDetail = new FeesAssignDetail(); // model name
                                            $feesGroupDetail->user_id = Session::get('id');
                                            $feesGroupDetail->session_id = Session::get('session_id');
                                            $feesGroupDetail->branch_id = Session::get('branch_id');
                                            $feesGroupDetail->fees_group_id = $request->fees_group_id[$count];
                                            $feesGroupDetail->fees_master_id = $request->fees_assign[$assign_count];
                                            $feesGroupDetail->fees_group_amount = $request->fees_group_amount[$count];
                                            $feesGroupDetail->class_type_id = $request->class_type_id ?? null;
                                            $fees_group_amount += $request->fees_group_amount[$count];
                                            $feesGroupDetail->discount = $request->discount[$count];
                                            $fees_group_discount += $request->discount[$count];
                                            $feesGroupDetail->fees_breakdown = $request->fees_breakdown[$count];
                                            $feesGroupDetail->fees_assign_id = $feesGroupId;
                                            $feesGroupDetail->admission_id = $addadmission_id;
                                            $feesGroupDetail->save();
                                            $assign_count++;
                                        }
                                    }
                                } else {
                                   
                                }
                            }
                         
                            $feesGroup->total_amount =$fees_group_amount;
                            $feesGroup->total_discount = $fees_group_discount;
                            $feesGroup->net_amount = $fees_group_amount-$fees_group_discount;
                            $feesGroup->save();
                             
                            $template = MessageTemplate::select('message_templates.*', 'message_types.slug','message_types.status as message_type_status')
                                    ->leftJoin('message_types', 'message_types.id', 'message_templates.message_type_id')
                                    ->where('message_types.slug', 'student-admission')
                                    ->first();
                                
                                $branch = Branch::find(Session::get('branch_id'));
                                $setting = Setting::where('branch_id', Session::get('branch_id'))->first();
                                
                                $arrey1 = [
                                    '{#name#}',
                                    '{#school_name#}',
                                    '{#user_name#}',
                                    '{#password#}',
                                    '{#email#}',
                                    '{#mobile#}',
                                ];
                                
                                $arrey2 = [
                                    $addadmission->first_name . " " . $addadmission->last_name,
                                    $setting->name ?? '',
                                    $addadmission->userName ?? '',
                                    $addadmission->confirm_password ?? '',
                                    $addadmission->email ?? '',
                                    $addadmission->mobile ?? '',
                                ];
                                
                                $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                
                                // ✅ Firebase Notification 
                                if ($setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Admission Notification',
                                        $whatsapp,
                                        'student',
                                        $addadmission->id // instead of $attendance['admission_id']
                                    ); 
                                }
                                 
                                // ✅ WhatsApp Message (only if template active)
                                if ($template->message_type_status == 1) {
                                    if ($branch->whatsapp_srvc == 1) {
                                        $mobile = $addadmission->mobile ?? $request->mobile ?? '';
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile, $whatsapp);
                                        }
                                    }
                                }

                                          
            self::clearAdmissionCache($addadmission->branch_id ?? null, $addadmission->session_id ?? null);
           return response()->json([ 'status' => 'success','message' => 'Admission Added Successfully.','print_url' => url('/admissionStudentPrint/' . $addadmission->id) 
            ]);
           
                }
                 return view('students.admission.add', ['BillCounter' => $BillCounterNo]);
            }

            public function sendStudentPushNotification(Request $request)
            {
                $request->validate([
                    'admission_ids' => 'required|array',
                    'title' => 'required|string|max:150',
                    'message' => 'required|string|max:1000',
                ]);

                $admissionIds = $request->admission_ids;
                $title = $request->title;
                $message = $request->message;
                $branchId = Session::get('branch_id');
                $sessionId = Session::get('session_id');

                $response = Helper::sendNotification(
                    $title,
                    $message,
                    'student',
                    $admissionIds,
                    null,
                    null,
                    [
                        'notification_type' => 'student_notification',
                        'title' => $title,
                        'body' => $message
                    ],
                    'high'
                );

                foreach ($admissionIds as $admissionId) {
                    \App\Models\Notification::create([
                        'title' => $title,
                        'content' => $message,
                        'type' => 'student_notification',
                        'admission_id' => $admissionId,
                        'user_id' => null,
                        'device_token' => null,
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                        'message_seen' => 0,
                        'show_status' => 1,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Push notifications sent successfully!',
                    'response' => $response
                ]);
            }

           public function admissionView(Request $request)
{
    $dataTable = $this->admissionVisibleColumns();

    $branchId = Session::get('branch_id');
    $sessionId = Session::get('session_id');

    /*
    |--------------------------------------------------------------------------
    | Pagination Parameters
    |--------------------------------------------------------------------------
    */
    $page = max(1, (int) $request->input('page', 1));
    $perPageRaw = $request->input('per_page', 25);
    $perPage = ($perPageRaw === 'all') ? 'all' : (int) $perPageRaw;
    if ($perPage !== 'all') {
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
    }

    /*
    |--------------------------------------------------------------------------
    | Default Search / Filter Values
    |--------------------------------------------------------------------------
    */
    $search = [
        'admissionNo'       => (string) $request->input('admissionNo', ''),
        'class_type_id'     => (string) $request->input('class_type_id', ''),
        'category'          => (string) $request->input('category', ''),
        'gender_id'         => (string) $request->input('gender_id', ''),
        'admission_type_id' => (string) $request->input('admission_type_id', ''),
        'blood_group'       => (string) $request->input('blood_group', ''),
        'status'            => $request->has('status') ? (string) $request->input('status') : '1',
        'name'              => (string) $request->input('name', ''),
        'father_name'       => (string) $request->input('father_name', ''),
        'mother_name'       => (string) $request->input('mother_name', ''),
        'mobile'            => (string) $request->input('mobile', ''),
        'from_date'         => (string) $request->input('from_date', ''),
        'to_date'           => (string) $request->input('to_date', ''),
        'biomax_id'         => (string) $request->input('biomax_id', ''),
        'search_type'       => (string) $request->input('search_type', ''),
    ];

    /*
    |--------------------------------------------------------------------------
    | RESET FILTER
    |--------------------------------------------------------------------------
    */
    if ($request->has('reset')) {
        Session::forget('admission_search_filters');
        Session::forget('admission_search_branch_id');
        Session::forget('admission_search_session_id');
        Session::forget('admission_search_applied');

        return redirect('admissionView');
    }

    /*
    |--------------------------------------------------------------------------
    | POST FILTER SEARCH (Preserve backward compatibility)
    |--------------------------------------------------------------------------
    */
    if ($request->isMethod('post') && !$request->ajax()) {
        $request->validate([
            'search_type' => 'nullable',
            'name' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($request->search_type) && empty($value)) {
                        $fail('Search By Keywords is required when Search Type is selected.');
                    }
                },
            ],
        ]);

        Session::put('admission_search_filters', $search);
        Session::put('admission_search_branch_id', $branchId);
        Session::put('admission_search_session_id', $sessionId);
        Session::put('admission_search_applied', true);
    } elseif ($request->has('from_profile') || Str::contains((string) $request->headers->get('referer'), 'studentDetail')) {
        if (
            Session::has('admission_search_applied') &&
            Session::get('admission_search_applied') === true &&
            Session::get('admission_search_branch_id') == $branchId &&
            Session::get('admission_search_session_id') == $sessionId
        ) {
            $savedSearch = Session::get('admission_search_filters');
            if (!empty($savedSearch) && is_array($savedSearch)) {
                $search = array_merge($search, $savedSearch);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMISSION QUERY & CACHING (Multi-User Partitioned Page Cache)
    |--------------------------------------------------------------------------
    */
    $cacheVer = self::getAdmissionCacheVersion($branchId, $sessionId);
    $filterHash = md5(json_encode([
        'search' => $search,
        'branch_id' => $branchId,
        'session_id' => $sessionId,
        'page' => $page,
        'per_page' => $perPage,
        'has_fees' => in_array('Fees Progress', $dataTable, true),
    ]));
    $cacheKey = "admission_page_{$branchId}_{$sessionId}_v{$cacheVer}_{$filterHash}";

    $pageBundle = Cache::remember($cacheKey, 1800, function () use ($branchId, $sessionId, $search, $dataTable, $page, $perPage) {
        $query = Admission::select(
                'admissions.*',
                'class.name as class_name'
            )
            ->with([
                'City:id,name',
                'State:id,name',
                'ClassTypes:id,name'
            ])
            ->leftJoin(
                'class_types as class',
                'class.id',
                '=',
                'admissions.class_type_id'
            )
            ->where('admissions.session_id', $sessionId)
            ->where('admissions.branch_id', $branchId)
            ->where('admissions.school', 1);

        // Keyword & Search Type
        $keyword = trim((string) ($search['name'] ?? ''));
        $searchType = (string) ($search['search_type'] ?? '');
        $searchableColumns = [
            'first_name',
            'admissionNo',
            'father_name',
            'mother_name',
            'mobile',
            'aadhaar',
            'jan_aadhaar',
            'address',
        ];

        if ($keyword !== '') {
            if (!empty($searchType) && in_array($searchType, $searchableColumns, true)) {
                $query->where('admissions.' . $searchType, 'LIKE', '%' . $keyword . '%');
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('admissions.first_name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('admissions.last_name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('admissions.admissionNo', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('admissions.father_name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('admissions.mobile', 'LIKE', '%' . $keyword . '%')
                      ->orWhereRaw("CONCAT_WS(' ', admissions.first_name, admissions.last_name) LIKE ?", ['%' . $keyword . '%']);
                });
            }
        }

        // In-column individual filters
        if (!empty($search['admissionNo'])) {
            $query->where('admissions.admissionNo', 'LIKE', '%' . $search['admissionNo'] . '%');
        }
        if (!empty($search['father_name'])) {
            $query->where('admissions.father_name', 'LIKE', '%' . $search['father_name'] . '%');
        }
        if (!empty($search['mother_name'])) {
            $query->where('admissions.mother_name', 'LIKE', '%' . $search['mother_name'] . '%');
        }
        if (!empty($search['mobile'])) {
            $query->where(function($q) use ($search) {
                $q->where('admissions.mobile', 'LIKE', '%' . $search['mobile'] . '%')
                  ->orWhere('admissions.father_mobile', 'LIKE', '%' . $search['mobile'] . '%');
            });
        }
        if (!empty($search['class_type_id'])) {
            if (is_numeric($search['class_type_id'])) {
                $query->where('admissions.class_type_id', (int) $search['class_type_id']);
            } else {
                $query->where('class.name', 'LIKE', '%' . $search['class_type_id'] . '%');
            }
        }
        if (!empty($search['category'])) {
            $query->where('admissions.category', 'LIKE', '%' . $search['category'] . '%');
        }
        if (!empty($search['gender_id'])) {
            if (is_numeric($search['gender_id'])) {
                $query->where('admissions.gender_id', (int) $search['gender_id']);
            } else {
                $gLow = strtolower($search['gender_id']);
                if ($gLow === 'male' || $gLow === '1') {
                    $query->where('admissions.gender_id', 1);
                } elseif ($gLow === 'female' || $gLow === '2') {
                    $query->where('admissions.gender_id', 2);
                }
            }
        }
        if (!empty($search['admission_type_id'])) {
            $query->where('admissions.admission_type_id', (int) $search['admission_type_id']);
        }
        if (!empty($search['blood_group'])) {
            $query->where('admissions.blood_group', $search['blood_group']);
        }
        if ($search['status'] !== '' && $search['status'] !== 'all') {
            $query->where('admissions.status', (int) $search['status']);
        }
        if (!empty($search['from_date'])) {
            $query->whereDate('admissions.admission_date', '>=', $search['from_date']);
        }
        if (!empty($search['to_date'])) {
            $query->whereDate('admissions.admission_date', '<=', $search['to_date']);
        }
        if (!empty($search['biomax_id'])) {
            $query->where(function($q) use ($search) {
                $q->where('admissions.attendance_unique_id', 'LIKE', '%' . $search['biomax_id'] . '%')
                  ->orWhere('admissions.biomax_id', 'LIKE', '%' . $search['biomax_id'] . '%');
            });
        }

        $totalCount = $query->count();
        $query->orderBy('admissions.first_name', 'ASC');

        if ($perPage === 'all') {
            $paginated = $query->get();
            $startIndex = 0;
        } else {
            $paginated = $query->forPage($page, $perPage)->get();
            $startIndex = ($page - 1) * $perPage;
        }

        /* Pre-aggregate Fees Progress ONLY for the students on this page */
        $feesAssignLookup = [];
        $feesPaidLookup = [];
        if (in_array('Fees Progress', $dataTable, true)) {
            $admissionIds = $paginated->pluck('id')->filter()->all();
            if (!empty($admissionIds)) {
                $feesAssignLookup = DB::table('fees_assign_details')
                    ->whereIn('admission_id', $admissionIds)
                    ->groupBy('admission_id')
                    ->select('admission_id', DB::raw('SUM(fees_group_amount) as total_assign'))
                    ->pluck('total_assign', 'admission_id')
                    ->all();

                $feesPaidLookup = DB::table('fees_detail')
                    ->where('session_id', $sessionId)
                    ->whereIn('admission_id', $admissionIds)
                    ->whereNull('deleted_at')
                    ->whereIn('status', [0, 1])
                    ->groupBy('admission_id')
                    ->select('admission_id', DB::raw('SUM(total_amount) as total_paid'))
                    ->pluck('total_paid', 'admission_id')
                    ->all();
            }
        }

        return [
            'paginated' => $paginated,
            'totalCount' => $totalCount,
            'feesAssignLookup' => $feesAssignLookup,
            'feesPaidLookup' => $feesPaidLookup,
            'startIndex' => $startIndex,
        ];
    });

    $paginated = $pageBundle['paginated'];
    $totalCount = $pageBundle['totalCount'];
    $feesAssignLookup = $pageBundle['feesAssignLookup'];
    $feesPaidLookup = $pageBundle['feesPaidLookup'];
    $startIndex = $pageBundle['startIndex'];

    /*
    |--------------------------------------------------------------------------
    | Admission Overall Statistics (Cached)
    |--------------------------------------------------------------------------
    */
    $statCacheKey = "admission_stats_{$branchId}_{$sessionId}_v{$cacheVer}";
    $admissionStats = Cache::remember($statCacheKey, 86400, function() use ($branchId, $sessionId) {
        $raw = DB::table('admissions')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('school', 1)
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN gender_id = 1 THEN 1 ELSE 0 END), 0) as male,
                COALESCE(SUM(CASE WHEN gender_id = 2 THEN 1 ELSE 0 END), 0) as female,
                COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) as active,
                COALESCE(SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END), 0) as inactive
            ")
            ->first();

        return [
            'total' => (int) ($raw->total ?? 0),
            'male' => (int) ($raw->male ?? 0),
            'female' => (int) ($raw->female ?? 0),
            'active' => (int) ($raw->active ?? 0),
            'inactive' => (int) ($raw->inactive ?? 0),
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Lookup Data (Cached for 24 hours)
    |--------------------------------------------------------------------------
    */
    $bloodGroupLookup = Cache::remember('blood_group_lookup_all', 86400, function () {
        return DB::table('blood_groups')->whereNull('deleted_at')->pluck('name', 'id')->all();
    });

    $genderLookup = Cache::remember('gender_lookup_all', 86400, function () {
        return DB::table('gender')->whereNull('deleted_at')->pluck('name', 'id')->all();
    });

    $lastPage = $perPage === 'all' ? 1 : max(1, (int) ceil($totalCount / $perPage));

    /*
    |--------------------------------------------------------------------------
    | AJAX Real-Time Response
    |--------------------------------------------------------------------------
    */
    if ($request->ajax() || $request->wantsJson() || $request->input('ajax') == '1') {
        $permission = Helper::permissioncheck(3);
        $html = view('students.admission.table_rows', [
            'data' => $paginated,
            'dataTable' => $dataTable,
            'startIndex' => $startIndex,
            'permission' => $permission,
            'bloodGroupLookup' => $bloodGroupLookup,
            'genderLookup' => $genderLookup,
            'feesAssignLookup' => $feesAssignLookup,
            'feesPaidLookup' => $feesPaidLookup,
            'getAdmissionDatatableFields' => Helper::getAdmissionDatatableFields(),
        ])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'total' => $totalCount,
            'from' => $totalCount > 0 ? $startIndex + 1 : 0,
            'to' => $perPage === 'all' ? $totalCount : min($startIndex + count($paginated), $totalCount),
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'stats' => $admissionStats,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Standard Initial View
    |--------------------------------------------------------------------------
    */
    return Helper::view(
        'students.admission.view',
        [
            'data' => $paginated,
            'search' => $search,
            'dataTable' => $dataTable,
            'admissionStats' => $admissionStats,
            'bloodGroupLookup' => $bloodGroupLookup,
            'genderLookup' => $genderLookup,
            'feesAssignLookup' => $feesAssignLookup,
            'feesPaidLookup' => $feesPaidLookup,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'perPage' => $perPage,
            'lastPage' => $lastPage,
            'startIndex' => $startIndex,
        ]
    );
}

            public function admissionView2(Request $request){
                // dd($request);
                $search['admissionNo'] = $request->admissionNo;
                $search['class_type_id'] = $request->class_type_id;
                $search['state_id'] = $request->state_id;
                $search['city_id'] = $request->city_id;
                $search['name'] = $request->name;
                // $alladmission = Admission::orderBy('first_name', 'ASC')->where('session_id', Session::get('session_id'));
                $alladmission = Admission::select('admissions.*','class.name as class_name')
                    ->leftJoin('class_types as class','class.id','admissions.class_type_id')->orderBy('admissions.first_name', 'ASC')->where('admissions.session_id', Session::get('session_id'));
                    if (Session::get('role_id') > 1) {
                        $alladmission = $alladmission->where('admissions.branch_id', Session::get('branch_id'));
                    }
                $alladmission =  $alladmission->get();
                return view('students.admission.view_2', ['data' => $alladmission, 'search' => $search]);
            }

            public function admissionBulkEdit(Request $request){
                if ($request->isMethod('post') && ($request->has('update_students') || $request->has('students'))) {
                    $studentsData = $request->input('students', []);
                    $updatedCount = 0;

                    if (!empty($studentsData) && is_array($studentsData)) {
                        $getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();

                        foreach ($studentsData as $id => $item) {
                            $admission = Admission::where('id', $id)
                                ->where('session_id', Session::get('session_id'))
                                ->where('branch_id', Session::get('branch_id'))
                                ->first();

                            if ($admission) {
                                $fieldsToUpdate = [
                                    'attendance_unique_id', 'admissionNo', 'ledger_no', 'first_name',
                                    'last_name', 'father_name', 'mother_name', 'mobile', 'father_mobile',
                                    'aadhaar', 'jan_aadhaar', 'roll_no', 'gender_id', 'dob', 'admission_date',
                                    'class_type_id', 'category', 'blood_group', 'admission_type_id',
                                    'state_id', 'city_id', 'biomax_id', 'address', 'status',
                                    'student_pen', 'apaar_id', 'religion', 'medium', 'house', 'height', 'weight', 'pincode'
                                ];

                                foreach ($fieldsToUpdate as $f) {
                                    if (array_key_exists($f, $item)) {
                                        $val = $item[$f];
                                        $admission->{$f} = is_string($val) ? trim($val) : $val;
                                    }
                                }

                                if (!empty($getAdmissionDatatableFields) && is_array($getAdmissionDatatableFields)) {
                                    foreach ($getAdmissionDatatableFields as $label => $fieldName) {
                                        if ($fieldName && !in_array($fieldName, ['id', 'FeesProgress'], true) && array_key_exists($fieldName, $item)) {
                                            $val = $item[$fieldName];
                                            if (Schema::hasColumn('admissions', $fieldName)) {
                                                $admission->{$fieldName} = is_string($val) ? trim($val) : $val;
                                            }
                                        }
                                    }
                                }

                                if ($request->hasFile("students.{$id}.image")) {
                                    $img = $request->file("students.{$id}.image");
                                    if ($img && $img->isValid()) {
                                        $ext = $img->getClientOriginalExtension();
                                        $student_image = ($admission->admissionNo ? $admission->admissionNo : 'std_' . $id) . '_' . time() . '.' . $ext;
                                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
                                        if (!file_exists($destinationPath)) {
                                            mkdir($destinationPath, 0755, true);
                                        }
                                        if (!empty($admission->image) && File::exists($destinationPath . $admission->image)) {
                                            File::delete($destinationPath . $admission->image);
                                        }
                                        try {
                                            if (class_exists('\Image') && method_exists('\Image', 'make')) {
                                                $compressedImage = \Image::make($img)
                                                    ->resize(600, null, function ($constraint) {
                                                        $constraint->aspectRatio();
                                                        $constraint->upsize();
                                                    })
                                                    ->encode($ext, 80);
                                                $compressedImage->save($destinationPath . $student_image);
                                            } else {
                                                $img->move($destinationPath, $student_image);
                                            }
                                        } catch (\Exception $e) {
                                            $img->move($destinationPath, $student_image);
                                        }
                                        $admission->image = $student_image;
                                    }
                                }

                                $admission->save();
                                $updatedCount++;
                            }
                        }
                    }

                    self::clearAdmissionCache();
                    $redirectUrl = 'admissionBulkEdit';
                    $queryParams = [];
                    if ($request->filled('class_type_id')) $queryParams['class_type_id'] = $request->class_type_id;
                    if ($request->filled('category')) $queryParams['category'] = $request->category;
                    if ($request->filled('gender_id')) $queryParams['gender_id'] = $request->gender_id;
                    if ($request->filled('admission_type_id')) $queryParams['admission_type_id'] = $request->admission_type_id;
                    if ($request->filled('blood_group')) $queryParams['blood_group'] = $request->blood_group;
                    if ($request->filled('status')) $queryParams['status'] = $request->status;
                    if ($request->filled('search_type')) $queryParams['search_type'] = $request->search_type;
                    if ($request->filled('name')) $queryParams['name'] = $request->name;

                    if (!empty($queryParams)) {
                        $redirectUrl .= '?' . http_build_query($queryParams);
                    }

                    return redirect($redirectUrl)->with('message', "Bulk Edit successful! {$updatedCount} student record(s) updated.");
                }

                $search['class_type_id'] = $request->class_type_id;
                $search['category'] = $request->category;
                $search['gender_id'] = $request->gender_id;
                $search['admission_type_id'] = $request->admission_type_id;
                $search['blood_group'] = $request->blood_group;
                $search['status'] = $request->has('status') ? (string) $request->status : '1';
                $search['search_type'] = $request->search_type;
                $search['name'] = $request->name;

                $query = Admission::select('admissions.*', 'class.name as class_name')
                    ->leftJoin('class_types as class', 'class.id', 'admissions.class_type_id')
                    ->with(['City', 'State'])
                    ->where('admissions.session_id', Session::get('session_id'))
                    ->where('admissions.branch_id', Session::get('branch_id'));

                if (!empty($request->class_type_id)) {
                    $query->where('admissions.class_type_id', $request->class_type_id);
                }
                if (!empty($request->category)) {
                    $query->where('admissions.category', $request->category);
                }
                if (!empty($request->gender_id)) {
                    $query->where('admissions.gender_id', $request->gender_id);
                }
                if (!empty($request->admission_type_id)) {
                    $query->where('admissions.admission_type_id', $request->admission_type_id);
                }
                if (!empty($request->blood_group)) {
                    $query->where('admissions.blood_group', $request->blood_group);
                }

                $studentStatus = (string) $search['status'];
                if (in_array($studentStatus, ['0', '1'], true)) {
                    $query->where('admissions.status', (int) $studentStatus);
                }

                $keyword = trim((string) $request->name);
                $searchType = (string) $request->search_type;
                $searchableColumns = [
                    'first_name', 'admissionNo', 'father_name', 'mother_name',
                    'mobile', 'aadhaar', 'jan_aadhaar', 'address'
                ];

                if ($keyword !== '') {
                    if (in_array($searchType, $searchableColumns, true)) {
                        $query->where('admissions.' . $searchType, 'LIKE', '%' . $keyword . '%');
                    } else {
                        $query->where(function ($q) use ($keyword, $searchableColumns) {
                            foreach ($searchableColumns as $index => $column) {
                                $method = $index === 0 ? 'where' : 'orWhere';
                                $q->{$method}('admissions.' . $column, 'LIKE', '%' . $keyword . '%');
                            }
                            $q->orWhere('admissions.last_name', 'LIKE', '%' . $keyword . '%')
                                ->orWhereRaw("CONCAT_WS(' ', admissions.first_name, admissions.last_name) LIKE ?", ['%' . $keyword . '%']);
                        });
                    }
                }

                $data = $query->orderBy('admissions.first_name', 'ASC')->get();

                $classType = Helper::classType();
                $getgenders = Helper::getgender();
                $getState = Helper::getState();
                $getcitie = Helper::getCity();
                $bloodGroupType = Helper::bloodGroupType();
                $dataTable = $this->admissionVisibleColumns();
                $getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();

                return view('students.admission.bulk_edit', [
                    'data' => $data,
                    'search' => $search,
                    'classType' => $classType,
                    'getgenders' => $getgenders,
                    'getState' => $getState,
                    'getcitie' => $getcitie,
                    'bloodGroupType' => $bloodGroupType,
                    'dataTable' => $dataTable,
                    'getAdmissionDatatableFields' => $getAdmissionDatatableFields,
                ]);
            }

            public function admissionEdit(Request $request, $id){
                $data = Admission::find($id);
                if ($request->isMethod('post')) {
                        $sessionId = Session::get('session_id');
                        $request->validate([
                           //  'admissionNo' => ['nullable',Rule::unique('admissions')->where(function ($query) use ($sessionId) {
                            // $query->where('session_id', $sessionId);})->ignore($id)],
                            // 'aadhaar' => 'unique:admissions,aadhaar',
                            // 'mobile' => 'unique:admissions,mobile',
                             'first_name' => 'required',
                                'gender_id' => 'required',
                                'mobile' => 'required|digits:10',
                                'father_name' => 'required',
                                'mother_name' => 'required',
                                'dob' => 'required',
                                'mobile' => 'required',
                                'father_mobile' => 'required',
                             'admission_type_id' => 'required',
                             'admissionNo' => [
                                'nullable',
                                Rule::unique('admissions', 'admissionNo')
                                    ->where(function ($query) use ($sessionId) {
                                        $query->where('session_id', $sessionId)->whereNull('deleted_at');
                                    })->ignore($id),
                             ],
                             'mobile' => [
                                'required',
                                'digits:10',
                                Rule::unique('admissions', 'mobile')
                                    ->where(function ($query) use ($sessionId) {
                                        $query->where('session_id', $sessionId)->whereNull('deleted_at');
                                    })->ignore($id),
                             ],
                        ]);
                         if ($request->file('student_img')) {
                            $image = $request->file('student_img');
                             $ext = $image->getClientOriginalExtension(); // jpg, png, jpeg आदि
                             $student_image = ($request->admissionNo ??  uniqid()). '.' . $ext;
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            if (isset($data->image) && File::exists($destinationPath . $data->image)) {
                                File::delete($destinationPath . $data->image);
                            }
                                $compressedImage = Image::make($image)
                                    ->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 80); // Adjust quality as needed
                            $compressedImage->save($destinationPath . $student_image);
                            $data->image = $student_image;
                        }
                
                        if ($request->file('father_img')) {
                            $image = $request->file('father_img');
                            $father_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_image/';
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            if (isset($data->father_image) && File::exists($destinationPath . $data->father_image)) {
                                File::delete($destinationPath . $data->father_image);
                            }
                            $compressedImage = Image::make($image)
                                ->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 80); // Adjust quality as needed
                            $compressedImage->save($destinationPath . $father_image);
                            $data->father_img = $father_image;
                        }
                        if ($request->file('mother_img')) {
                            $image = $request->file('mother_img');
                            $mother_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_image/';
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            if (isset($data->mother_img) && File::exists($destinationPath . $data->mother_img)) {
                                File::delete($destinationPath . $data->mother_img);
                            }
                            $compressedImage = Image::make($image)
                            ->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 80); // Adjust quality as needed
                            $compressedImage->save($destinationPath . $mother_image);
                            $data->mother_img = $mother_image;
                        }
                       
                        $data->user_id = Session::get('id');
                        
                        $data->admissionNo = $request->admissionNo;
                        $data->ledger_no = $request->ledger_no;
                        $data->student_pen = $request->student_pen;
                        $data->apaar_id = $request->apaar_id;
                        $data->roll_no = $request->roll_no;
                        $data->admission_date = $request->admission_date;
                        $data->admission_type_id = $request->admission_type_id;
                        $data->class_type_id = $request->class_type_id;
                        if(!empty($request->stream_subject)){
                            $data->stream_subject = implode(',', $request->stream_subject);
                        }
                        $data->first_name = $request->first_name;
                        $data->aadhaar = $request->aadhaar;
                        $data->jan_aadhaar = $request->jan_aadhaar;
                        $data->previous_school = $request->previous_school;
                        $data->email = $request->email;
                        $data->mobile = $request->mobile;
                        $data->father_name = $request->father_name;
                        $data->mother_name = $request->mother_name;
                        $data->father_mobile = $request->father_mobile;
                        $data->dob = $request->dob;
                        $data->gender_id = $request->gender_id;
                        $data->admission_type_id = $request->admission_type_id;
                        $data->blood_group = $request->blood_group;
                        $data->medium = $request->medium;
                        $data->address = $request->address;
                        $data->country_id = $request->country;
                        $data->village_city = $request->village_city;
                        $data->family_id = $request->family_id;
                        $data->religion = $request->religion;
                        // $data->nationalty = $request->nationalty;
                        $data->category = $request->category;
                        $data->caste_category = $request->caste_category;
                        $data->transport = $request->transport;
                        $data->bus_number = $request->bus_number;
                        $data->bus_route = $request->bus_route;
                        $data->stoppage = $request->stoppage;
                        $data->transpor_charges = $request->transpor_charges;
                        $data->guardian_name = $request->guardian_name;
                        $data->guardian_mobile = $request->guardian_mobile;
                        $data->mother_mob = $request->mother_mob;
                        $data->father_aadhaar = $request->father_aadhaar;
                        $data->mother_aadhaar = $request->mother_aadhaar;
                        $data->family_annual_income = $request->family_annual_income;
                        $data->bank_account = $request->bank_account;
                        $data->bank_name = $request->bank_name;
                        $data->branch_name = $request->branch_name;
                        $data->ifsc = $request->ifsc;
                        $data->micr_code = $request->micr_code;
                        $data->city_id = $request->city;
                        $data->state_id = $request->state;
                        $data->relation_student = $request->relation_student;
                        $data->school_namestudied_last_year = $request->school_namestudied_last_year;
                        $data->house = $request->house;
                        $data->height = $request->height;
                        $data->weight = $request->weight;
                        $data->pincode = $request->pincode;
                        $data->remark_1 = $request->remark_1;
                        $data->bank_account_holder = $request->bank_account_holder;
                        $data->district = $request->district;
                        $data->tehsil = $request->tehsil;
                        $data->father_pancard = $request->father_pancard;
                        $data->mother_pancard = $request->mother_pancard;
                        $data->bpl = $request->bpl;
                        $data->bpl_certificate_no = $request->bpl_certificate_no;
                        $data->father_occupation = $request->father_occupation;
                        $data->mother_occupation = $request->mother_occupation;
                        
                        $data->save();
                        $addadmission_id = $id;
                        $this->unique_system_id($id);
                        self::clearAdmissionCache($data->branch_id ?? null, $data->session_id ?? null);
                     
                         return response()->json(['status' => 'success', 'message' => 'Admission Updated Successfully.']);
                       
                }
                 
                $getstate = State::where('country_id', $data['country_id'])->get();
                $getcitie = City::where('state_id', $data['state_id'])->get();
                  
                   return view('students.admission.edit', ['data' => $data, 'getState' => $getstate, 'getCity' => $getcitie]);
            }

                // 1. Data Check करने के लिए AJAX फंक्शन
                public function checkStudentData(Request $request){
                    $id = $request->id;
                    
                    // सभी टेबल्स में डेटा चेक करें
                    $fessCount = FeesDetail::where('admission_id', $id)->count();
                    $collectCount = FeesCollect::where('admission_id', $id)->count();
                    $invoiceCount = FeesDetailsInvoices::where('admission_id', $id)->count();
                    $assignCount = FeesAssign::where('admission_id', $id)->count();
                    $assignDetailCount = FeesAssignDetail::where('admission_id', $id)->count();
                    $marksCount = FillMarks::where('admission_id', $id)->count();

                    // अगर किसी भी एक टेबल में डेटा है, तो true भेजें
                    if ($fessCount > 0 || $collectCount > 0 || $invoiceCount > 0 || $assignCount > 0 || $assignDetailCount > 0 || $marksCount > 0) {
                        return response()->json(['has_data' => true]);
                    }
                    
                    return response()->json(['has_data' => false]);
                }

                // 2. आपका Delete फंक्शन (अपडेटेड)
                public function admissionDelete(Request $request){
                    $id = $request->delete_id;
                    $admission = Admission::find($id);

                    if($admission) {
                        $branchId = $admission->branch_id;
                        $sessionId = $admission->session_id;

                        // इन सभी टेबल्स से डेटा डिलीट (या Soft Delete) करें
                        FeesDetail::where('admission_id', $admission->id)->delete();
                        FeesCollect::where('admission_id', $admission->id)->delete();
                        FeesDetailsInvoices::where('admission_id', $admission->id)->delete();
                        FeesAssign::where('admission_id', $admission->id)->delete();
                        FeesAssignDetail::where('admission_id', $admission->id)->delete();
                        FillMarks::where('admission_id', $admission->id)->delete();

                        // पिता की इमेज डिलीट करें
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'father_image/' . $admission->father_img)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'father_image/' . $admission->father_img);
                        }

                        // अंत में स्टूडेंट का एडमिशन डिलीट करें
                        $admission->delete();
                        self::clearAdmissionCache($branchId, $sessionId);

                        return redirect::to('admissionView')->with('message', 'Admission & Related Data Deleted Successfully !');
                    }

                    return redirect::to('admissionView')->with('error', 'Student not found.');
                }
            public function admissionStudentSearch(Request $request){
                $search['name'] = $request->name;
                    if ($request->isMethod('post')) {
                        $request->validate([]);
                        $data = Enquiry::with('ClassTypes')->where('session_id', Session::get('session_id'));
                  
                    if (!empty($request->name)) {
                        $data = $data
                        ->where('first_name', 'like', '%' . $request->name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->name . '%')
                        ->orWhere('mobile', 'like', '%' . $request->name . '%')
                        ->orWhere('email', 'like', '%' . $request->name . '%')
                        ->orWhere('father_name', 'like', '%' . $request->name . '%')
                        ->orWhere('mother_name', 'like', '%' . $request->name . '%')
                        ->orWhere('address', 'like', '%' . $request->name . '%');
                    }
                    if (!empty($request->registration_no)) {
                        $data = $data->where("registration_no", $request->registration_no);
                    }
                    if (!empty($request->class_search_id)) {
                        $data = $data->where("class_type_id", $request->class_search_id);
                    }
                    $allstudents = $data->orderBy('id', 'DESC')->get();
                }
                return view('students.admission.studentSearchView', ['data' => $allstudents]);
            }

            public function admissionStudentOnClick(Request $request)
{
    $student = Enquiry::where('id', $request->student_id)->first();

    if (!$student) {
        return response()->json([
            'status' => 'error',
            'message' => 'Student not found.'
        ]);
    }

    // Agar already Admission hai
    if ($student->ad_status == 'Admission') {
        return response()->json([
            'status' => 'already_admitted',
            'message' => 'This student is already admitted.',
            'stu_data' => $student
        ]);
    }

    // Agar ad_status NULL hai
    return response()->json([
        'status' => 'success',
        'stu_data' => $student
    ]);
}

     

           
  
            
            public function admissionStudentIdPrint(Request $request, $id){
                // $student_id = Admission::find($id);
                $student_id =  Admission::Select('admissions.*','sessions.from_year','class_types.name as class_name','sessions.to_year')
                ->leftjoin('sessions','sessions.id','admissions.session_id')
                ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                ->where('admissions.id', $id)->first();
                // $printPreviewId = Helper::printPreview('Student Id Print');
                // //dd($printPreviewId);
                // return view($printPreviewId, ['data' => $student_id]);

                return view('master.printFilePanel.StudentManagement.template14', ['data' => $student_id]);
                // return view('print_file.student_print.admissionStudentIdPrint', ['data' => $student_id]);
            }
            
          

            public function studentDetail($id){
                
                        $data = Admission::select('admissions.*','sessions.from_year','sessions.to_year','class.name as class_name','gender.name as genderName','countries.name as country_name','states.name as state_name','citys.name as city_name')
                        ->leftJoin('class_types as class','class.id','admissions.class_type_id')
                        ->leftJoin('gender','gender.id','admissions.gender_id')
                        ->leftjoin('countries','countries.id','admissions.country_id')
                        ->leftjoin('states','states.id','admissions.state_id')
                        ->leftjoin('citys','citys.id','admissions.city_id')
                        ->leftjoin('sessions','sessions.id','admissions.session_id')
                        ->orderBy('class.orderBy', 'ASC')
                        ->where([['admissions.session_id', Session::get('session_id')],
                                ['admissions.branch_id', Session::get('branch_id')],
                                ['admissions.id', $id]])->first();
                                $promotion_history = collect();
                                if (!empty($data)) {
                                    $promotion_history = Admission::select('admissions.*','class.name as class_name','gender.name as genderName','sessions.from_year','sessions.to_year')
                                ->leftJoin('class_types as class', 'class.id', 'admissions.class_type_id')
                                ->leftJoin('gender', 'gender.id', 'admissions.gender_id')
                                ->leftJoin('sessions', 'sessions.id', 'admissions.session_id')
                                ->where('admissions.unique_system_id', $data->unique_system_id)
                                ->orderBy('class.orderBy', 'ASC')
                                ->get();
                                }
                             $siblings = [];
                            if ($data) {
                                $siblings = Admission::select('admissions.*','class.name as class_name','gender.name as genderName' )
                                    ->leftJoin('class_types as class', 'class.id', 'admissions.class_type_id')
                                    ->leftJoin('gender', 'gender.id', 'admissions.gender_id')
                                    ->where('admissions.father_name', $data->father_name)
                                    ->where('admissions.father_mobile', $data->father_mobile)
                                    ->where('admissions.id', '!=', $id) // Exclude the main student
                                    ->where([
                                        ['admissions.session_id', Session::get('session_id')],
                                        ['admissions.branch_id', Session::get('branch_id')]
                                    ])
                                    ->orderBy('class.orderBy', 'ASC')
                                    ->get();
                            }
                              $getDocuments = collect();
                                if (!empty($data)) {
                                    $getDocuments = StudentDocument::select('student_documents.*')
                                        ->where('admission_id', $data->id)
                                        ->get();
                                }
                            $getFees='';
                            $getPaidFees='';
                            $feeHeadLedger = collect();
                            if (!empty($data)) {
                                 $getFees = FeesAssignDetail::select('fees_assign_details.*', 'fees_group.name as group_name')
                                ->join('fees_group', 'fees_group.id', '=', 'fees_assign_details.fees_group_id')
                                ->where('admission_id', $data->id)
                                ->where('fees_assign_details.session_id', Session::get('session_id'))
                                ->where('fees_assign_details.branch_id', Session::get('branch_id'))
                                ->get();
                        
                            $getPaidFees = FeesDetailsInvoices::select('fees_details_invoices.*', 'payment_modes.name as payment_mode')
                                ->join('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
                                ->whereIn('status', [0, 1])
                                ->where('admission_id', $data->id)
                                ->where('fees_details_invoices.session_id', Session::get('session_id'))
                                ->where('fees_details_invoices.branch_id', Session::get('branch_id'))
                                ->get();

                            $feePaymentsByHead = FeesDetail::select(
                                    'fees_detail.*',
                                    'payment_modes.name as payment_mode'
                                )
                                ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_detail.payment_mode_id')
                                ->where('fees_detail.admission_id', $data->id)
                                ->where('fees_detail.session_id', Session::get('session_id'))
                                ->where('fees_detail.branch_id', Session::get('branch_id'))
                                ->where(function ($query) {
                                    $query->where('fees_detail.fees_type', 0)
                                        ->orWhereNull('fees_detail.fees_type');
                                })
                                ->whereIn('fees_detail.status', [0, 1])
                                ->orderByDesc('fees_detail.date')
                                ->orderByDesc('fees_detail.id')
                                ->get()
                                ->groupBy('fees_group_id');

                            $feeHeadLedger = collect($getFees)
                                ->groupBy('fees_group_id')
                                ->map(function ($assignments, $feeGroupId) use ($feePaymentsByHead) {
                                    $payments = $feePaymentsByHead->get($feeGroupId, collect())->values();
                                    $assignedAmount = round((float) $assignments->sum('fees_group_amount'), 2);
                                    $assignmentDiscount = round((float) $assignments->sum('discount'), 2);
                                    $paidAmount = round((float) $payments->sum('paid_amount'), 2);
                                    $paymentDiscount = round((float) $payments->sum('discount'), 2);
                                    $fineAmount = round((float) $payments->sum('installment_fine'), 2);
                                    $dueAmount = round(max(
                                        0,
                                        $assignedAmount - $assignmentDiscount - $paidAmount - $paymentDiscount
                                    ), 2);

                                    return (object) [
                                        'fees_group_id' => $feeGroupId,
                                        'name' => $assignments->first()->group_name ?: 'Fee Head',
                                        'assigned_amount' => $assignedAmount,
                                        'discount' => $assignmentDiscount + $paymentDiscount,
                                        'paid_amount' => $paidAmount,
                                        'fine_amount' => $fineAmount,
                                        'due_amount' => $dueAmount,
                                        'payments' => $payments,
                                    ];
                                })
                                ->values();
                            }          

                        if (!$data) {
                            return redirect('admissionView')->with('error', 'Student record not found.');
                        }

                        $assignedFees = round((float) collect($getFees)->sum('fees_group_amount'), 2);
                        $paidFees = round((float) collect($getPaidFees)->sum('amount'), 2);
                        $feeDiscount = round((float) collect($getPaidFees)->sum('discount'), 2);
                        $feeFine = round((float) collect($getPaidFees)->sum('total_fine'), 2);
                        $dueFees = round(max(0, $assignedFees + $feeFine - $feeDiscount - $paidFees), 2);
                        $feePaidPercentage = $assignedFees > 0
                            ? round(min(100, ($paidFees / $assignedFees) * 100), 2)
                            : 0;

                        $attendanceUniqueId = trim((string) $data->attendance_unique_id);
                        if ($attendanceUniqueId === '') {
                            $attendanceUniqueId = trim((string) $data->admissionNo);
                        }
                        if ($attendanceUniqueId === '') {
                            $attendanceUniqueId = 'STU-'.$data->id;
                        }

                        // The current attendance module stores the stable student unique ID,
                        // not the session-specific admissions.id created after promotion.
                        $attendanceMarks = AttendanceMark::where('branch_id', Session::get('branch_id'))
                            ->where('session_id', Session::get('session_id'))
                            ->where('entity_type', 'student')
                            ->where('unique_id', $attendanceUniqueId)
                            ->orderBy('date', 'desc')
                            ->orderBy('updated_at', 'desc')
                            ->get()
                            ->groupBy('date')
                            ->map(function ($marks) {
                                return $marks->first();
                            })
                            ->values();

                        if ($attendanceMarks->isNotEmpty()) {
                            $attendanceTotal = $attendanceMarks->count();
                            $attendancePresent = $attendanceMarks->whereIn('status', ['present', 'late', 'early_out'])->count();
                            $attendanceAbsent = $attendanceMarks->where('status', 'absent')->count();
                            $attendanceCredit = $attendancePresent + ($attendanceMarks->where('status', 'halfday')->count() * 0.5);
                            $attendancePercentage = $attendanceTotal > 0
                                ? round(($attendanceCredit / $attendanceTotal) * 100, 2)
                                : 0;
                            $recentAttendance = $attendanceMarks->take(8)->map(function ($mark) {
                                $mark->profile_status = ucwords(str_replace('_', ' ', (string) $mark->status));
                                $mark->profile_status_key = strtolower((string) $mark->status);
                                $mark->time = $mark->in_time;
                                return $mark;
                            });
                        } else {
                            // Keep historical compatibility for attendance saved in the legacy table.
                            $legacyAttendanceIds = collect([$data->id, $attendanceUniqueId])
                                ->filter(function ($value) {
                                    return is_numeric($value);
                                })
                                ->map(function ($value) {
                                    return (int) $value;
                                })
                                ->unique()
                                ->values();
                            $attendanceQuery = StudentAttendance::where('branch_id', Session::get('branch_id'))
                                ->where('session_id', Session::get('session_id'))
                                ->whereIn('admission_id', $legacyAttendanceIds);
                            $legacyAttendance = (clone $attendanceQuery)
                                ->with('AttendanceStatus:id,name,simbel')
                                ->orderBy('date', 'desc')
                                ->orderBy('id', 'desc')
                                ->get()
                                ->groupBy('date')
                                ->map(function ($marks) {
                                    return $marks->first();
                                })
                                ->values();
                            $attendanceTotal = $legacyAttendance->count();
                            $attendancePresent = $legacyAttendance->filter(function ($mark) {
                                return in_array(strtolower((string) optional($mark->AttendanceStatus)->name), ['in', 'out', 'present'], true);
                            })->count();
                            $attendanceAbsent = $legacyAttendance->filter(function ($mark) {
                                return strtolower((string) optional($mark->AttendanceStatus)->name) === 'absent';
                            })->count();
                            $attendancePercentage = $attendanceTotal > 0
                                ? round(($attendancePresent / $attendanceTotal) * 100, 2)
                                : 0;
                            $recentAttendance = $legacyAttendance->take(8)->map(function ($mark) {
                                $mark->profile_status = optional($mark->AttendanceStatus)->name ?: 'Marked';
                                $mark->profile_status_key = strtolower((string) $mark->profile_status);
                                return $mark;
                            });
                        }

                        $examMarks = FillMarks::select(
                                'fill_marks.*',
                                'exams.name as exam_name',
                                'exams.exam_date',
                                'assign_exams.exam_date as assigned_exam_date',
                                'subject.name as subject_name',
                                'fill_min_max_marks.exam_maximum_marks as configured_max_marks',
                                'fill_min_max_marks.exam_minimum_marks as configured_min_marks'
                            )
                            ->leftJoin('exams', 'exams.id', '=', 'fill_marks.exam_id')
                            ->join('exam_result_publications', function ($join) {
                                $join->on('exam_result_publications.exam_id', '=', 'fill_marks.exam_id')
                                    ->on('exam_result_publications.class_type_id', '=', 'fill_marks.class_type_id')
                                    ->on('exam_result_publications.branch_id', '=', 'fill_marks.branch_id')
                                    ->on('exam_result_publications.session_id', '=', 'fill_marks.session_id')
                                    ->whereNotNull('exam_result_publications.published_at');
                            })
                            ->leftJoin('assign_exams', function ($join) {
                                $join->on('assign_exams.exam_id', '=', 'fill_marks.exam_id')
                                    ->on('assign_exams.class_type_id', '=', 'fill_marks.class_type_id')
                                    ->on('assign_exams.branch_id', '=', 'fill_marks.branch_id')
                                    ->on('assign_exams.session_id', '=', 'fill_marks.session_id')
                                    ->whereNull('assign_exams.deleted_at');
                            })
                            ->leftJoin('subject', 'subject.id', '=', 'fill_marks.subject_id')
                            ->leftJoin('fill_min_max_marks', 'fill_min_max_marks.id', '=', 'fill_marks.fill_min_max_marks_id')
                            ->where('fill_marks.branch_id', Session::get('branch_id'))
                            ->where('fill_marks.session_id', Session::get('session_id'))
                            ->where('fill_marks.admission_id', $data->id)
                            ->whereNull('exams.deleted_at')
                            ->whereNull('subject.deleted_at')
                            ->orderByRaw('COALESCE(assign_exams.exam_date, exams.exam_date, fill_marks.created_at) DESC')
                            ->orderBy('fill_marks.exam_id', 'desc')
                            ->orderBy('subject.sort_by')
                            ->get();

                        $examResults = $examMarks->groupBy('exam_id')->map(function ($marks) {
                            $obtained = 0;
                            $maximum = 0;
                            $numericSubjects = 0;
                            $subjects = $marks->map(function ($mark) use (&$obtained, &$maximum, &$numericSubjects) {
                                $rawMark = trim((string) $mark->student_marks);
                                $maxMark = (float) ($mark->configured_max_marks ?: $mark->exam_maximum_marks ?: 0);
                                $isNumeric = is_numeric($rawMark);
                                if ($isNumeric) {
                                    $obtained += (float) $rawMark;
                                    $maximum += $maxMark;
                                    $numericSubjects++;
                                }

                                return (object) [
                                    'id' => $mark->subject_id,
                                    'name' => $mark->subject_name ?: 'Subject',
                                    'marks' => $rawMark !== '' ? $rawMark : '-',
                                    'maximum' => $maxMark,
                                    'minimum' => (float) ($mark->configured_min_marks ?: 0),
                                    'is_numeric' => $isNumeric,
                                ];
                            })->values();

                            return (object) [
                                'exam_id' => $marks->first()->exam_id,
                                'name' => $marks->first()->exam_name ?: 'Exam',
                                'date' => $marks->first()->assigned_exam_date
                                    ?: ($marks->first()->exam_date ?: $marks->first()->created_at),
                                'obtained' => round($obtained, 2),
                                'maximum' => round($maximum, 2),
                                'percentage' => $maximum > 0 ? round(($obtained / $maximum) * 100, 2) : 0,
                                'subject_count' => $numericSubjects,
                                'subjects' => $subjects,
                            ];
                        })->values();

                        $subjectPerformance = $examMarks->groupBy('subject_id')->map(function ($marks) {
                            $obtained = 0;
                            $maximum = 0;
                            foreach ($marks as $mark) {
                                if (!is_numeric(trim((string) $mark->student_marks))) {
                                    continue;
                                }
                                $obtained += (float) $mark->student_marks;
                                $maximum += (float) ($mark->configured_max_marks ?: $mark->exam_maximum_marks ?: 0);
                            }
                            return (object) [
                                'name' => $marks->first()->subject_name ?: 'Subject',
                                'percentage' => $maximum > 0 ? round(($obtained / $maximum) * 100, 2) : 0,
                                'exams' => $marks->filter(function ($mark) {
                                    return is_numeric(trim((string) $mark->student_marks));
                                })->count(),
                            ];
                        })->filter(function ($subject) {
                            return $subject->exams > 0;
                        })->sortByDesc('percentage')->values();

                        $overallExamPercentage = $examResults->sum('maximum') > 0
                            ? round(($examResults->sum('obtained') / $examResults->sum('maximum')) * 100, 2)
                            : 0;
                        $assignedSubjects = Subject::where('branch_id', Session::get('branch_id'))
                            ->where('session_id', Session::get('session_id'))
                            ->where('class_type_id', $data->class_type_id)
                            ->orderBy('sort_by')
                            ->get(['id', 'name']);

                        return view('students.admission.studentDetail', compact(
                            'data',
                            'getFees',
                            'getPaidFees',
                            'feeHeadLedger',
                            'siblings',
                            'getDocuments',
                            'promotion_history',
                            'assignedFees',
                            'paidFees',
                            'feeDiscount',
                            'feeFine',
                            'dueFees',
                            'feePaidPercentage',
                            'attendanceTotal',
                            'attendancePresent',
                            'attendanceAbsent',
                            'attendancePercentage',
                            'recentAttendance',
                            'examResults',
                            'subjectPerformance',
                            'overallExamPercentage',
                            'assignedSubjects'
                        ));
                    }
 
      
            
       
            
            public function bulkIdPrint(Request $request){
                $classtype = $request->class_type_id;
                $admission_ids = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('class_type_id',$classtype)->pluck('id')->implode(',');
                if(!empty($admission_ids)){
                    $data= explode(',',$admission_ids);
                }
                return view('students.student_id.bulkIdPrint',['admission_ids'=>$data]);
            }
    
  public function studentBulkImageUpload(Request $request)
            {
                if ($request->isMethod('post')) {
                    if ($request->file('image')) {
                        foreach ($request->file('image') as $img) {
                            $originalName = $img->getClientOriginalName();
                            $filenameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
            
                            $admission = Admission::where('branch_id', Session::get('branch_id'))
                                ->where('session_id',Session::get('session_id'))
                                ->where('admissionNo', $filenameWithoutExtension)
                                ->first();
            
                            if ($admission) {
                                // Old image delete
                                $oldImagePath = env('IMAGE_UPLOAD_PATH') . 'profile/' . $admission->image;
                                if ($admission->image && File::exists($oldImagePath)) {
                                    File::delete($oldImagePath);
                                }
            
                                // New image name with extension
                                 $ext = $image->getClientOriginalExtension(); // jpg, png, jpeg आदि
                                $student_image = ($admission->admissionNo ?? uniqid()) . '.'.$ext;
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
            
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0755, true);
                                }
            
                                // Compress and save new image
                                $compressedImage = \Image::make($img)
                                    ->resize(600, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })
                                    ->encode('jpg', 80); // quality 80%
            
                                $compressedImage->save($destinationPath . $student_image);
            
                                // Update DB
                                $admission->image = $student_image;
                                $admission->save();
                            }
                        }
            
                        return redirect('admissionView')->with('message', 'Images Updated Successfully');
                    }
                }
            }
            

            public function category_wise_report(Request $request){
                $classType = Helper::classType();
                $categories = ['GENERAL', 'OBC', 'ST', 'SC', 'BC', 'SBC'];
                $rows = Admission::query()
                    ->selectRaw('class_type_id, category, gender_id, COUNT(*) as total')
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->whereNull('deleted_at')
                    ->where('status', 1)
                    ->groupBy('class_type_id', 'category', 'gender_id')
                    ->get();
                $categoryIndex = [];
                foreach ($rows as $row) {
                    $categoryIndex[(int) $row->class_type_id][$row->category][(int) $row->gender_id] = (int) $row->total;
                }

                $classTotals = [];
                $grandTotals = ['boys' => 0, 'girls' => 0, 'total' => 0];

                foreach ($classType as $type) {
                    $classTotals[$type->id] = ['boys' => 0, 'girls' => 0, 'total' => 0, 'categories' => []];
                    foreach ($categories as $category) {
                        $boys = (int) ($categoryIndex[$type->id][$category][1] ?? 0);
                        $girls = (int) ($categoryIndex[$type->id][$category][2] ?? 0);
                        $classTotals[$type->id]['categories'][$category] = ['boys' => $boys, 'girls' => $girls, 'total' => $boys + $girls];
                        $classTotals[$type->id]['boys'] += $boys;
                        $classTotals[$type->id]['girls'] += $girls;
                        $classTotals[$type->id]['total'] += $boys + $girls;
                    }
                    $grandTotals['boys'] += $classTotals[$type->id]['boys'];
                    $grandTotals['girls'] += $classTotals[$type->id]['girls'];
                    $grandTotals['total'] += $classTotals[$type->id]['total'];
                }

                return view('students.report.category_wise_report', compact('classType', 'categories', 'classTotals', 'grandTotals'));
            }
    
            public function streamUpdate(Request $request){
                $data = collect();
                $list_subject = collect();
                $assignedSubjectsByStudent = [];
                $search["class_type_id"] = (int) $request->class_type_id;

                if ($search["class_type_id"] > 0) {
                    $data = Admission::where('class_type_id', $search["class_type_id"])
                        ->where("branch_id", Session::get("branch_id"))
                        ->where('session_id', Session::get("session_id"))
                        ->where('status', 1)
                        ->orderBy('first_name', 'ASC')
                        ->get(['id', 'admissionNo', 'first_name', 'last_name', 'stream_subject']);
                    $list_subject = Subject::where("class_type_id", $search["class_type_id"])
                        ->where('session_id', Session::get("session_id"))
                        ->where("branch_id", Session::get("branch_id"))
                        ->orderBy("sort_by", "ASC")
                        ->get(['id', 'name']);

                    $assignedIds = $data->flatMap(function ($student) {
                        return collect(explode(',', (string) $student->stream_subject))
                            ->map(function ($id) { return (int) trim($id); })
                            ->filter();
                    })->unique()->values();
                    $assignedSubjectLookup = Subject::withTrashed()
                        ->whereIn('id', $assignedIds)
                        ->get(['id', 'name'])
                        ->keyBy('id');

                    foreach ($data as $student) {
                        $assignedSubjectsByStudent[$student->id] = collect(explode(',', (string) $student->stream_subject))
                            ->map(function ($id) use ($assignedSubjectLookup) {
                                return $assignedSubjectLookup->get((int) trim($id));
                            })
                            ->filter()
                            ->unique('id')
                            ->values();
                    }
                }
                
                return view('students.admission.stream_update', [
                    'search' => $search,
                    'data' => $data,
                    'list_subject' => $list_subject,
                    'assignedSubjectsByStudent' => $assignedSubjectsByStudent,
                ]);
            }
    
            public function streamUpdateSave(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'class_type_id' => 'required|integer',
                        'admission_id' => 'required|array|min:1',
                        'subject_id' => 'required|array|min:1',
                    ]);

                    $subjectIds = Subject::whereIn('id', $request->subject_id)
                        ->where('class_type_id', $request->class_type_id)
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->pluck('id')->map(function ($id) { return (int) $id; })->all();

                    if (empty($subjectIds)) {
                        return redirect('stream_update?class_type_id='.$request->class_type_id)
                            ->with('error', 'Please select valid subjects for this class.');
                    }

                    $students = Admission::whereIn('id', $request->admission_id)
                        ->where('class_type_id', $request->class_type_id)
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->get();

                    foreach ($students as $student) {
                        $existingIds = collect(explode(',', (string) $student->stream_subject))
                            ->map(function ($id) { return (int) trim($id); })
                            ->filter();
                        $student->stream_subject = $existingIds->merge($subjectIds)->unique()->implode(',');
                        $student->save();
                    }

                    return redirect('stream_update?class_type_id='.$request->class_type_id)
                        ->with('message', 'Subjects assigned successfully.');
                }
            }
            
            public function streamRemove(Request $request,$admission_id,$subject_id){
                $data = Admission::where('id', $admission_id)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->first();
                if (!$data) {
                    return response()->json(['error' => 'Admission not found.'], 404);
                }

                $subjectId = (int) $subject_id;
                $existingIds = collect(explode(',', (string) $data->stream_subject))
                    ->map(function ($id) { return (int) trim($id); })
                    ->filter();
                if (!$existingIds->contains($subjectId)) {
                    return response()->json(['error' => 'Subject assignment not found.'], 404);
                }

                $data->stream_subject = $existingIds
                    ->reject(function ($id) use ($subjectId) { return $id === $subjectId; })
                    ->unique()
                    ->implode(',');
                $data->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Subject removed successfully.',
                    'admission_id' => (int) $admission_id,
                    'subject_id' => $subjectId,
                ]);
            }
            
            public function folderCompressor(Request $request) {
                $path = '/home/rusoft/public_html/greengarden/schoolimage/profile/';
                $images = \File::allFiles($path);
                foreach ($images as $file) {
                    $filePath = $file->getRealPath();
                    // Check if the file is a valid image
                        if (!@getimagesize($filePath)) {
                            \Log::warning("Invalid image file: " . $file->getFilename());
                            continue; // Skip invalid images
                        }
                        $student_image = $file->getFilename();
                        $destinationPath = $path;
                        // Create the destination directory if it doesn't exist
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        // Delete old image if it exists
                        if (isset($data->image) && \File::exists($destinationPath . $data->image)) {
                            \File::delete($destinationPath . $data->image);
                        }
                        $compressedImage = Image::make($filePath)
                            ->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 80); // Adjust quality as needed
                        // Save the compressed image
                        $compressedImage->save($destinationPath . $student_image);
                    }
                    return redirect::to('/')->with('message', 'folder Compressor Successfully!');
                }
                 
      
            public function login_credential_reports(Request $request)
            {
                $branchId = (int) Session::get('branch_id');
                $sessionId = (int) Session::get('session_id');

                $page = max(1, (int) $request->input('page', 1));
                $perPageRaw = $request->input('per_page', 25);
                $perPage = ($perPageRaw === 'all') ? 'all' : (in_array((int) $perPageRaw, [10, 25, 50, 100], true) ? (int) $perPageRaw : 25);

                $search = [
                    'class_type_id' => $request->filled('class_type_id') ? (int) $request->input('class_type_id') : 0,
                    'student_status' => $request->filled('student_status') && in_array((int) $request->input('student_status'), [0, 1], true) ? (int) $request->input('student_status') : 1,
                    'name' => trim((string) $request->input('name', '')),
                    'father_name' => trim((string) $request->input('father_name', '')),
                    'mobile' => trim((string) $request->input('mobile', '')),
                    'userName' => trim((string) $request->input('userName', '')),
                    'credential_status' => trim((string) $request->input('credential_status', '')),
                ];

                $cacheVersion = self::getAdmissionCacheVersion($branchId, $sessionId);

                // Cached Class Types
                $classType = Cache::remember(
                    "login_credentials_classes_{$branchId}_{$sessionId}_v{$cacheVersion}",
                    1800,
                    function () {
                        return Helper::classType();
                    }
                );

                // Cached Single-query Summary Stats
                $statsCacheKey = "login_credentials_stats_{$branchId}_{$sessionId}_v{$cacheVersion}";
                $credentialStats = Cache::remember($statsCacheKey, 1800, function () use ($branchId, $sessionId) {
                    $raw = DB::table('admissions')
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->where('school', 1)
                        ->whereNull('deleted_at')
                        ->selectRaw("
                            COUNT(*) as total,
                            COALESCE(SUM(CASE WHEN (userName IS NOT NULL AND userName != '' AND confirm_password IS NOT NULL AND confirm_password != '') THEN 1 ELSE 0 END), 0) as `generated`,
                            COALESCE(SUM(CASE WHEN (userName IS NULL OR userName = '' OR confirm_password IS NULL OR confirm_password = '') THEN 1 ELSE 0 END), 0) as `pending`
                        ")
                        ->first();

                    return [
                        'total' => (int) ($raw->total ?? 0),
                        'generated' => (int) ($raw->generated ?? 0),
                        'pending' => (int) ($raw->pending ?? 0),
                    ];
                });

                // Build filtered student query
                $query = Admission::query()
                    ->select(
                        'admissions.id',
                        'admissions.first_name',
                        'admissions.last_name',
                        'admissions.father_name',
                        'admissions.mobile',
                        'admissions.admissionNo',
                        'admissions.attendance_unique_id',
                        'admissions.userName',
                        'admissions.confirm_password',
                        'admissions.status',
                        'class.name as class_name'
                    )
                    ->leftJoin('class_types as class', 'class.id', '=', 'admissions.class_type_id')
                    ->where('admissions.branch_id', $branchId)
                    ->where('admissions.session_id', $sessionId)
                    ->where('admissions.school', 1)
                    ->whereNull('admissions.deleted_at');

                if ($search['student_status'] !== 'all' && ($search['student_status'] === 0 || $search['student_status'] === 1)) {
                    $query->where('admissions.status', (int) $search['student_status']);
                }

                if (!empty($search['class_type_id'])) {
                    $query->where('admissions.class_type_id', (int) $search['class_type_id']);
                }

                if ($search['name'] !== '') {
                    $name = $search['name'];
                    $query->where(function ($q) use ($name) {
                        $q->where('admissions.first_name', 'LIKE', "%{$name}%")
                          ->orWhere('admissions.last_name', 'LIKE', "%{$name}%")
                          ->orWhere('admissions.admissionNo', 'LIKE', "%{$name}%")
                          ->orWhereRaw("CONCAT_WS(' ', admissions.first_name, admissions.last_name) LIKE ?", ["%{$name}%"]);
                    });
                }

                if ($search['father_name'] !== '') {
                    $query->where('admissions.father_name', 'LIKE', '%' . $search['father_name'] . '%');
                }

                if ($search['mobile'] !== '') {
                    $mobile = $search['mobile'];
                    $query->where(function ($q) use ($mobile) {
                        $q->where('admissions.mobile', 'LIKE', "%{$mobile}%")
                          ->orWhere('admissions.father_mobile', 'LIKE', "%{$mobile}%");
                    });
                }

                if ($search['userName'] !== '') {
                    $query->where('admissions.userName', 'LIKE', '%' . $search['userName'] . '%');
                }

                if ($search['credential_status'] === 'generated') {
                    $query->whereNotNull('admissions.userName')
                          ->where('admissions.userName', '!=', '')
                          ->whereNotNull('admissions.confirm_password')
                          ->where('admissions.confirm_password', '!=', '');
                } elseif ($search['credential_status'] === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNull('admissions.userName')
                          ->orWhere('admissions.userName', '')
                          ->orWhereNull('admissions.confirm_password')
                          ->orWhere('admissions.confirm_password', '');
                    });
                }

                $totalCount = $query->count();
                $query->orderBy('admissions.first_name', 'ASC')->orderBy('admissions.last_name', 'ASC');

                if ($perPage === 'all') {
                    $paginated = $query->get();
                    $startIndex = 0;
                    $lastPage = 1;
                } else {
                    $paginated = $query->forPage($page, $perPage)->get();
                    $startIndex = ($page - 1) * $perPage;
                    $lastPage = max(1, (int) ceil($totalCount / $perPage));
                }

                $setting = Cache::remember("setting_branch_{$branchId}", 3600, function () use ($branchId) {
                    return DB::table('settings')->where('branch_id', $branchId)->first();
                });

                if ($request->ajax() || $request->wantsJson() || $request->input('ajax') == '1') {
                    $html = view('students.login_credential.table_rows', [
                        'data' => $paginated,
                        'startIndex' => $startIndex,
                        'setting' => $setting,
                    ])->render();

                    return response()->json([
                        'status' => true,
                        'html' => $html,
                        'total' => $totalCount,
                        'from' => $totalCount > 0 ? $startIndex + 1 : 0,
                        'to' => $perPage === 'all' ? $totalCount : min($startIndex + count($paginated), $totalCount),
                        'current_page' => $page,
                        'last_page' => $lastPage,
                        'per_page' => $perPage,
                        'stats' => $credentialStats,
                    ]);
                }

                return view('students.login_credential.view', [
                    'data' => $paginated,
                    'search' => $search,
                    'classType' => $classType,
                    'credentialStats' => $credentialStats,
                    'totalCount' => $totalCount,
                    'currentPage' => $page,
                    'perPage' => $perPage,
                    'lastPage' => $lastPage,
                    'startIndex' => $startIndex,
                    'setting' => $setting,
                ]);
            }

            public function loginCredentialReportsPdf(Request $request)
            {
                $validated = $request->validate([
                    'class_type_id' => ['nullable', 'integer'],
                    'student_status' => ['nullable', 'integer', 'in:0,1'],
                ]);

                $branchId = Session::get('branch_id');
                $sessionId = Session::get('session_id');
                $studentStatus = (int) ($validated['student_status'] ?? 1);

                $query = Admission::select(
                        'admissions.first_name',
                        'admissions.last_name',
                        'admissions.father_name',
                        'admissions.mobile',
                        'admissions.admissionNo',
                        'admissions.attendance_unique_id',
                        'admissions.unique_system_id',
                        'admissions.userName',
                        'admissions.confirm_password',
                        'admissions.status',
                        'class.name as class_name'
                    )
                    ->leftJoin('class_types as class', 'class.id', '=', 'admissions.class_type_id')
                    ->where('admissions.branch_id', $branchId)
                    ->where('admissions.session_id', $sessionId)
                    ->where('admissions.school', 1)
                    ->whereNull('admissions.deleted_at')
                    ->where('admissions.status', $studentStatus)
                    ->orderBy('admissions.first_name', 'ASC')
                    ->orderBy('admissions.last_name', 'ASC');

                if (!empty($validated['class_type_id'])) {
                    $query->where('admissions.class_type_id', $validated['class_type_id']);
                }

                $data = $query->get();
                abort_if($data->isEmpty(), 404, 'No students found for the selected criteria.');

                $setting = Setting::where('branch_id', $branchId)->first();
                $className = !empty($validated['class_type_id']) ? ($data->first()->class_name ?: 'Class') : 'All Classes';
                $safeClassName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $className);
                $statusLabel = $studentStatus === 1 ? 'active' : 'inactive';

                return PDF::loadView('students.login_credential.pdf', compact('data', 'setting', 'className', 'statusLabel'))
                    ->setPaper('a4', 'landscape')
                    ->download('login-credentials-' . $statusLabel . '-' . trim($safeClassName, '-') . '.pdf');
            }

            public function loginCredentialReportsExcel(Request $request)
            {
                $validated = $request->validate([
                    'class_type_id' => ['nullable', 'integer'],
                    'student_status' => ['nullable', 'integer', 'in:0,1'],
                ]);
                $studentStatus = (int) ($validated['student_status'] ?? 1);
                $branchId = Session::get('branch_id');
                $sessionId = Session::get('session_id');

                $query = Admission::select(
                        'admissions.first_name',
                        'admissions.last_name',
                        'admissions.father_name',
                        'admissions.mobile',
                        'admissions.admissionNo',
                        'admissions.attendance_unique_id',
                        'admissions.unique_system_id',
                        'admissions.userName',
                        'admissions.confirm_password',
                        'admissions.status',
                        'class.name as class_name'
                    )
                    ->leftJoin('class_types as class', 'class.id', '=', 'admissions.class_type_id')
                    ->where('admissions.branch_id', $branchId)
                    ->where('admissions.session_id', $sessionId)
                    ->where('admissions.school', 1)
                    ->whereNull('admissions.deleted_at')
                    ->where('admissions.status', $studentStatus)
                    ->orderBy('admissions.first_name', 'ASC')
                    ->orderBy('admissions.last_name', 'ASC');

                if (!empty($validated['class_type_id'])) {
                    $query->where('admissions.class_type_id', $validated['class_type_id']);
                }

                $data = $query->get();
                abort_if($data->isEmpty(), 404, 'No students found for the selected criteria.');

                $className = !empty($validated['class_type_id']) ? ($data->first()->class_name ?: 'Class') : 'All Classes';
                $safeClassName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $className), '-');
                $setting = Setting::where('branch_id', $branchId)->first();
                $statusLabel = $studentStatus === 1 ? 'active' : 'inactive';

                return Excel::download(
                    new LoginCredentialReportExport($data, $setting, $className, $statusLabel),
                    'login-credentials-' . $statusLabel . '-' . ($safeClassName ?: 'Class') . '.xlsx'
                );
            }

            public function student_logs(Request $request)
            {
                abort_unless((int) Session::get('role_id') === 1, 403);

                $branchId = Session::get('branch_id');
                $sessionId = Session::get('session_id');
                $selectedClassId = (int) $request->query('class_type_id', 0);
                $classType = Helper::classType();

                $latestTokens = DB::table('notification_tokens as nt')
                    ->selectRaw('nt.attendance_unique_id, MAX(nt.updated_at) as last_login')
                    ->where('nt.entity_type', 'student')
                    ->where('nt.branch_id', $branchId)
                    ->where('nt.session_id', $sessionId)
                    ->whereNull('nt.deleted_at')
                    ->whereNotNull('nt.attendance_unique_id')
                    ->where('nt.attendance_unique_id', '!=', '')
                    ->groupBy('nt.attendance_unique_id');

                $logQuery = DB::query()
                    ->fromSub($latestTokens, 'token_logs')
                    ->join('admissions as admission', function ($join) {
                        $join->on('admission.attendance_unique_id', '=', 'token_logs.attendance_unique_id');
                    })
                    ->leftJoin('class_types as class', 'class.id', '=', 'admission.class_type_id')
                    ->where('admission.branch_id', $branchId)
                    ->where('admission.session_id', $sessionId)
                    ->where('admission.status', 1)
                    ->where('admission.school', 1)
                    ->select(
                        'admission.id',
                        'admission.admissionNo',
                        'admission.first_name',
                        'admission.last_name',
                        'admission.father_name',
                        'admission.mobile',
                        'admission.attendance_unique_id',
                        'class.name as class_name',
                        'class.orderBy as class_order',
                        'token_logs.last_login'
                    );

                $search = [
                    'class_type_id' => $request->query('class_type_id', ''),
                    'admissionNo' => $request->query('admissionNo', ''),
                    'name' => $request->query('name', ''),
                ];

                if (!empty($search['class_type_id'])) {
                    $logQuery->where('admission.class_type_id', (int) $search['class_type_id']);
                }

                if (!empty($search['admissionNo'])) {
                    $logQuery->where('admission.admissionNo', $search['admissionNo']);
                }

                if (!empty($search['name'])) {
                    $kw = $search['name'];
                    $logQuery->where(function ($q) use ($kw) {
                        $q->where('admission.first_name', 'LIKE', '%' . $kw . '%')
                            ->orWhere('admission.last_name', 'LIKE', '%' . $kw . '%')
                            ->orWhere('admission.father_name', 'LIKE', '%' . $kw . '%')
                            ->orWhere('admission.mobile', 'LIKE', '%' . $kw . '%')
                            ->orWhere('admission.attendance_unique_id', 'LIKE', '%' . $kw . '%');
                    });
                }

                // Analytics Summary Metrics
                $todayStart = Carbon::now()->startOfDay()->toDateTimeString();
                $weekStart = Carbon::now()->subDays(7)->toDateTimeString();
                $monthStart = Carbon::now()->subDays(30)->toDateTimeString();

                $summary = [
                    'total' => (int) (clone $logQuery)->count(),
                    'today' => (int) (clone $logQuery)->where('token_logs.last_login', '>=', $todayStart)->count(),
                    'week'  => (int) (clone $logQuery)->where('token_logs.last_login', '>=', $weekStart)->count(),
                    'month' => (int) (clone $logQuery)->where('token_logs.last_login', '>=', $monthStart)->count(),
                ];

                // Fetch logs with ordering
                $logs = (clone $logQuery)
                    ->orderByDesc('token_logs.last_login')
                    ->paginate(50)
                    ->appends($request->all());

                $selectedClassId = (int) ($search['class_type_id'] ?? 0);

                return view('students.student_logs', compact('logs', 'summary', 'classType', 'selectedClassId', 'search'));
            }


            
            

           

        public function studentUserNameCreate(Request $request)
        {
            // 1. Blade से आई हुई कॉमा सेपरेटेड IDs को Array में बदलें
            $studentIds = [];
            if ($request->student_ids) {
                $studentIds = explode(',', $request->student_ids);
            }

            // 2. चेक करें कि स्टूडेंट्स चुने गए हैं या नहीं
            if (empty($studentIds)) {
                return back()->with('error', 'No students selected for credential generation.');
            }

            // 3. फॉर्म से ऑर्डर्स और सेटिंग्स (जैसे name_letters, mobile_digits) प्राप्त करें
            $usernameOrder = $request->username_order ? explode(',', $request->username_order) : [];
            $passwordOrder = $request->password_order ? explode(',', $request->password_order) : [];
            $nameLetters = (int) ($request->name_letters ?? 4);
            $mobileDigits = (int) ($request->mobile_digits ?? 4);

            // 4. डेटाबेस से सिर्फ उन्हीं स्टूडेंट्स को Fetch करें जो सेलेक्ट किये गए थे
            $students = Admission::whereIn('id', $studentIds)
                        ->where('branch_id', Session::get('branch_id'))
                        ->where('session_id', Session::get('session_id'))
                        ->get();

            // 5. पासवर्ड और यूजरनेम जनरेट करने का लूप
            foreach ($students as $student) {
                
                // Username Generate करें
                $username = '';
                foreach ($usernameOrder as $key) {
                    $username .= $this->getFieldValue($student, $key, $nameLetters, $mobileDigits);
                }

                // Password Generate करें
                $password = '';
                foreach ($passwordOrder as $key) {
                    $password .= $this->getFieldValue($student, $key, $nameLetters, $mobileDigits);
                }

                // अगर यूजरनेम या पासवर्ड खाली न हो, तो डेटाबेस में सेव करें
                if (!empty($username) || !empty($password)) {
                    $student->userName = $username;
                    $student->confirm_password = $password;       // Plain text save karne ke liye
                    $student->password = Hash::make($password);   // Hashed password save karne ke liye
                    $student->save();
                }
            }

            self::clearAdmissionCache(Session::get('branch_id'), Session::get('session_id'));

            return back()->with('success', 'Credentials generated successfully for selected students!');
        }   



        private function getFieldValue($student, $key, $nameLetters = 4, $mobileDigits = 4)
        {
            switch ($key) {
                case 'name':
                    return substr(strtolower(preg_replace('/\s+/', '', $student->first_name)), 0, $nameLetters);
                case 'mobile':
                    return substr(preg_replace('/\D/', '', $student->mobile), -$mobileDigits);
                case 'dob':
                    if (!$student->dob) return '';
                    $dob = date('dmy', strtotime($student->dob)); // e.g. 150805
                    return $dob;
                case 'admission_no':
                    return $student->admissionNo;
                case 'class':
                    return strtolower(preg_replace('/\s+/', '', $student->class_name));
                default:
                    return '';
            }
        }
         public function imageRotateSave(Request $request)
        {
                    $id = $request->admission_id;
                    $admission = Admission::find($id);
                
                    if (!$admission) {
                        return response()->json(['status'=>'error','message'=>'Admission not found!']);
                    }
            $action = $request->action_type;
            if($action == 'upload'){
                if ($request->file('student_img')) {
                                $image = $request->file('student_img');
                                $ext = $image->getClientOriginalExtension(); // jpg, png, jpeg आदि
                                $student_image = ($admission->admissionNo ??  uniqid()). '.' . $ext;
                                $admission->image = $student_image;
                                $admission->save();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0755, true);
                                }
                                if (isset($data->image) && File::exists($destinationPath . $data->image)) {
                                    File::delete($destinationPath . $data->image);
                                }
                                $compressedImage = Image::make($image)
                                ->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->encode('jpg', 80); // Adjust quality as needed
                                $compressedImage->save($destinationPath . $student_image);
                               $image_url =  env('IMAGE_SHOW_PATH'). 'profile/'.$student_image;
                            }
                 return response()->json(['success'=>true,'image_url'=>$image_url,'message'=>' file uploaded']);
                }
                if($action == 'delete'){
                if($admission->image && file_exists(env('IMAGE_UPLOAD_PATH') . 'profile/'.$admission->image)){
                   // unlink(env('IMAGE_UPLOAD_PATH') . 'profile/'.$admission->image);
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'profile/' . $admission->image);
                }
                $admission->image = null;
                $admission->save();
                return response()->json(['success'=>true,'message'=>' The image was deleted successfully']);
               }
        
              return response()->json(['success'=>false,'message'=>'Invalid action']);    
                
        }
        



    }
