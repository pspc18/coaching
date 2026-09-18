<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\hostel\Hostel;
use App\Models\hostel\HostelBuilding;
use App\Models\BillCounter;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\FeesDetail;
use App\Models\Student;
use App\Models\Master\RegistrationTerms;
use App\Models\hostel\HostelFloor;
use App\Models\hostel\HostelRoom;
use App\Models\hostel\HostelBed;
use App\Models\hostel\StudentExpense;
use App\Models\hostel\HostelAssign;
use App\Models\hostel\HostelDetail;
use App\Models\hostel\HostelFeesDetail;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\Head;
use App\Models\Master\MessageTemplate;
use App\Models\hostel\HostelMeterUnit;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Hash;
use Helper;
use File;
use Str;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelAssignController extends Controller

{


            public function hostelAssign(Request $request){
                //dd($request);
                $admissionBillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('type', 'StudentAdmission')->get()->first();
                $bill  = BillCounter::where('type','HostelFees')->first();
                $receipt_no = $bill->counter + 1;
                if($request->isMethod('post')){
                    $request->validate([
                    ]);
                    if(!empty($request->admission_id)){
                        $admission_update = Admission::find($request->admission_id); 
                        $student_image ='';
                        if ($request->file('student_img')) {
                            $image = $request->file('student_img');
                            $path = $image->getRealPath();
                            $student_image = time() . uniqid() . $image->getClientOriginalName();
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                            $image->move($destinationPath, $student_image);
                            $admission_update->image = $student_image;
                        }
                        $father_photo ='';
                        if ($request->file('father_photo')) {
                            $image = $request->file('father_photo');
                            $father_photo = time() . uniqid() . $image->getClientOriginalName();
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_photo';
                            $image->move($destinationPath, $father_photo);
                            $admission_update->father_img = $father_photo;
                        }
                        $mother_photo='';
                        if ($request->file('mother_photo')) {
                            $image = $request->file('mother_photo');
                            $mother_photo = time() . uniqid() . $image->getClientOriginalName();
                            $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_photo';
                            $image->move($destinationPath, $mother_photo);
                            $admission_update->mother_img = $mother_photo;
                        }
                        $admission_update->hostel = 1;
                        $admission_update->aadhaar = $request->aadhaar;
                        $admission_update->dob = $request->dob;
                        $admission_update->first_name = $request->first_name;
                        //$admission_update->last_name = $request->last_name;
                        $admission_update->email = $request->email;
                        $admission_update->mobile = $request->mobile;
                        $admission_update->father_name = $request->father_name;
                        $admission_update->father_mobile = $request->father_mobile;
                        $admission_update->mother_name = $request->mother_name;
                        $admission_update->gender_id = $request->gender_id;
                        $admission_update->address = $request->address;
                        $admission_update->save();  
                        $admission_id = $request->admission_id;  
                        }else{
                            $student_image='';
                            if ($request->file('student_img')) {
                                $image = $request->file('student_img');
                                $path = $image->getRealPath();
                                $student_image = time() . uniqid() . $image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                                $image->move($destinationPath, $student_image);
                            }
                            $father_photo='';
                            if ($request->file('father_photo')) {
                                $image = $request->file('father_photo');
                                $father_photo = time() . uniqid() . $image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_photo';
                                $image->move($destinationPath, $father_photo);
                            }
                            $mother_photo='';
                            if ($request->file('mother_photo')) {
                                $image = $request->file('mother_photo');
                                $mother_photo = time() . uniqid() . $image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_photo';
                                $image->move($destinationPath, $mother_photo);
                            }
                            $addadmission = new Admission(); //model name
                            $addadmission->user_id = Session::get('id');
                            $addadmission->session_id = Session::get('session_id');
                            $addadmission->branch_id = Session::get('branch_id');
                            $addadmission->admissionNo = $request->admission_no;
                            $addadmission->branch_id = Session::get('branch_id');
                            $addadmission->school = 0;
                            $addadmission->library = 0;
                            $addadmission->hostel = '1';
                            $addadmission->admission_date = $request->admission_date;
                            $addadmission->aadhaar = $request->aadhaar;
                            $addadmission->dob = $request->dob;
                            $addadmission->image = $student_image;
                            $addadmission->mother_img = $mother_photo;
                            $addadmission->father_img = $father_photo;
                            $addadmission->first_name = $request->first_name;
                            // $addadmission->last_name = $request->last_name;
                            $addadmission->email = $request->email;
                            $addadmission->mobile = $request->mobile;
                            $addadmission->father_name = $request->father_name;
                            $addadmission->father_mobile = $request->father_mobile;
                            $addadmission->mother_name = $request->mother_name;
                            $addadmission->gender_id = $request->gender_id;
                            $addadmission->address = $request->address;
                            $addadmission->userName = $request->mobile;
                            $addadmission->password = Hash::make('12345678');
                            $addadmission->confirm_password = '12345678';
                            $addadmission->status = 1;
                            $addadmission->save();   
                            $admission_id = $addadmission->id;     
                            $counter = !empty($admissionBillCounter->counter) ? $admissionBillCounter->counter : 0 ;
                            $admissionBillCounter->counter = $counter + 1 ;
                            $admissionBillCounter->save();
                        }
                        $hostel = new HostelAssign;//model name
                        $hostel->user_id = Session::get('id');
                        $hostel->session_id = Session::get('session_id');
                        $hostel->branch_id = Session::get('branch_id');
                        $hostel->admission_id = $admission_id;
                        $hostel->hostel_renewal_date = $request->hostel_renewal_date;
                        $hostel->hostel_id = $request->hostel_id;
                        $hostel->building_id = $request->building_id;
                        $hostel->floor_id = $request->floor_id;
                        $hostel->room_id = $request->room_id;
                        $hostel->bed_id = $request->bed_id;
                        $hostel->hostel_fees = $request->hostel_fees;
            			$hostel->mothers_mobile = $request->mothers_mobile;
            			$hostel->guardian_whatsapp = $request->guardian_whatsapp;
                        $hostel->meter_unit = 0;
                        $hostel->date = $request->admission_date;
                        $hostel->status = 1;
                        $hostel->bed_status = 1;
                        $hostel->pay_status = 1;
                        $hostel->college = $request->college;
                        $hostel->Course = $request->Course;
                        $hostel->guardian_name = $request->guardian_name;
                        $hostel->guardian_mobile = $request->guardian_mobile;
                        $hostel->guardian_tel = $request->guardian_tel;
                        $hostel->guardian_address = $request->guardian_address;
                        $hostel->save();
                        $hostel_pay = new Invoice;
                        $hostel_pay->user_id = Session::get('id');
                        $hostel_pay->branch_id = Session::get('branch_id');
                        $hostel_pay->session_id = Session::get('session_id');
                        $hostel_pay->invoice_type = 1;
                        $hostel_pay->invoice_no = date('d').random_int(1000, 9999);
                        $hostel_pay->hostel_assign_id = $hostel->id;
                        $hostel_pay->hostel_renewal_date = $request->hostel_renewal_date;
                        $hostel_pay->admission_id = $admission_id;
                        $hostel_pay->discount = $request->discountAmount;
                        $hostel_pay->paid_amount = $request->amount;
                        $hostel_pay->total_amount = $request->hostel_amount;
                        $hostel_pay->due_amount = $request->duesAmount;
                        $hostel_pay->save();
                        $FeesDetail = new FeesDetail; //model name
                        $FeesDetail->user_id = Session::get('id');
                        $FeesDetail->session_id = Session::get('session_id');
                        $FeesDetail->branch_id = Session::get('branch_id');
                        $FeesDetail->invoice_id = $hostel_pay->id;
                        $FeesDetail->receipt_no = $request->editable_receipt_no;
                        $FeesDetail->fees_counter_id = Session::get('counter_id');
                        $FeesDetail->admission_id = $admission_id;
                        $FeesDetail->fees_type = 1;
                        $FeesDetail->discount = $request->discountAmount;
                        $FeesDetail->discount_type = $request->discountType;
                        $FeesDetail->discount_value = $request->discountValue;
                        $FeesDetail->discount_remark = $request->discount_remark;
                        $FeesDetail->paid_amount = $request->amount;
                        $FeesDetail->total_amount = $request->totalPayableAmount;
                        $FeesDetail->due_amount = $request->duesAmount;
                        $FeesDetail->date = date('Y-m-d');
                        $FeesDetail->payment_mode_id = $request->payment_mode_id;
                        $FeesDetail->save(); 
                        $template =  MessageTemplate::Select('message_templates.*', 'message_types.slug')
					    ->leftjoin('message_types', 'message_types.id', 'message_templates.message_type_id')
					    ->where('message_types.status', 1)->where('message_types.slug', 'hostelassign')->first();
				        $branch = Branch::find(Session::get('branch_id'));
                        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                        $hostel_details = HostelAssign::select('hostel_assign.*','admissions.email','admissions.mobile','admissions.first_name','admissions.last_name','hostel.name as hostel_name','hostel_bed.name as hostelbadname','hostel_floor.name as hostelFLoor','hostel_building.name as hostelBuildName','hostel_room.name as hostemRoom')
                        ->leftjoin('hostel','hostel.id','hostel_assign.hostel_id')
                        ->leftjoin('hostel_building','hostel_building.id','hostel_assign.building_id')
                        ->leftjoin('hostel_room','hostel_room.id','hostel_assign.room_id')
                        ->leftjoin('hostel_floor','hostel_floor.id','hostel_assign.floor_id')
                        ->leftjoin('hostel_bed','hostel_bed.id','hostel_assign.bed_id')
                        ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                        ->where('hostel_assign.id',$hostel->id)->first();
                        $arrey1 = array(
                        '{#name#}',
                        '{#hostel_name#}',
                        '{#building#}',
                        '{#floor_no#}',
                        '{#room_no#}',
                        '{#bed_no#}',
                        '{#assigned_date#}',
                        '{#support_no#}');
                        $arrey2 = array(
                        $hostel_details->first_name." ".$hostel_details->last_name,
                        $hostel_details->hostel_name,
                        $hostel_details->hostelBuildName,
                        $hostel_details->hostelFLoor,
                        $hostel_details->hostemRoom,
                        $hostel_details->hostelbadname,
                        date('d-m-Y'),
                        $setting->mobile);
                        if($template->status != 1){
                            if($hostel_details->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $hostel_details->email, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                            if($branch->whatsapp_srvc != 0){
                                if ($hostel_details->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($hostel_details->mobile,$whatsapp);
                                    }
                                }
                            }
                            if($branch->sms_srvc != 0){
                                if($hostel_details->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($hostel_details->mobile, $sms);
                                    }
                                }
                            }    
                        }
                        $url = '/hostel_student_print/'.$hostel->id;
                    ?>
                        <script type="text/javascript">
                            window.open("<?=$url?>", "_blank");
                        </script>
                    <?php
                    return redirect::to('assign_student_view')->with('message', 'Hostel Assign Successfully !');
                }
                return view('hostel.hostel_assign.add',['admissionBillCounter'=>$admissionBillCounter,'receipt_no'=>$receipt_no]);
            } 

	        public function assignStudentView(Request $request){
        		$search['hostel_id'] = $request->hostel_id;
        		$search['building_id'] = $request->building_id;
        		$search['floor_id'] = $request->floor_id;
        		$search['room_id'] = $request->room_id;
        		$search['bed_id'] = $request->bed_id;
		        $data = HostelAssign::Select('hostel_assign.*', 'hostel.name as hostel_name', 'hostel_building.name as building_name', 'hostel_floor.name as floor_name', 'hostel_room.name as room_name', 'hostel_bed.name as bad_name','admission.first_name','admission.last_name','admission.dob as student_dob')
    			->leftjoin('hostel as hostel', 'hostel.id', 'hostel_assign.hostel_id')
    			->leftjoin('hostel_building as hostel_building', 'hostel_building.id', 'hostel_assign.building_id')
    			->leftjoin('hostel_floor as hostel_floor', 'hostel_floor.id', 'hostel_assign.floor_id')
    			->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
    			->leftjoin('admissions as admission', 'admission.id', 'hostel_assign.admission_id')
    			->leftjoin('hostel_bed as hostel_bed', 'hostel_bed.id', 'hostel_assign.bed_id');
		        if ($request->isMethod('post')) {
		            if($request->filter == 'expired_last_15_days') {
                        $date = date('Y-m-d', strtotime("-15 day"));
                        $data = $data->where('hostel_assign.hostel_renewal_date','>=',$date)->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d'));
                    }
		            if($request->filter == 'expiring_7_days') {
                        $date = date('Y-m-d', strtotime("+7 day"));
                        $data = $data->where('hostel_assign.hostel_renewal_date','<=',$date)->where('hostel_assign.hostel_renewal_date','>=',date('Y-m-d'));
                    }
                    if($request->filter == 'expired_yesterday') {
                        $date = date('Y-m-d', strtotime("-1 day"));
                        $data = $data->where('hostel_assign.hostel_renewal_date','>=',$date)->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d'));
                    }
                    if($request->filter == 'expiring_today') {
                        $data = $data->where('hostel_assign.hostel_renewal_date',date('Y-m-d'));
                    }
                    if($request->filter == 'new_student_today') {
                        $data = $data->whereDate('hostel_assign.created_at',date('Y-m-d'));
                    }
                    if($request->filter == 'new_student_this_month') {
                        $data = $data->whereYear('hostel_assign.created_at', '=', date('Y-m-d'))->whereMonth('hostel_assign.created_at', '=', date('Y-m-d'));
                    }
                    if($request->filter == 'new_student_last_month') {
                        $data = $data->whereYear('hostel_assign.created_at', '=', date('Y-m-d'))->whereMonth('hostel_assign.created_at', '=', date('Y-m-d', strtotime("-1 month")));
                    }
        			if (!empty($request->hostel_id)) {
        				$data = $data->where("hostel.id", $request->hostel_id);
        			}
        			if (!empty($request->building_id)) {
        				$data = $data->where("hostel_building.id", $request->building_id);
        			}
        			if (!empty($request->floor_id)) {
        				$data = $data->where("hostel_floor.id", $request->floor_id);
        			}
        			if (!empty($request->room_id)) {
        				$data = $data->where("hostel_room.id", $request->room_id);
        			}
        			if (!empty($request->bed_id)) {
        				$data = $data->where("hostel_bed.id", $request->bed_id);
        			}
		        }
		        $data = $data->where('hostel_assign.session_id', Session::get('session_id'))->where('hostel_assign.branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
		        return view('hostel.hostel_assign.view', ['data' => $data, 'search' => $search]);
	        }

	        public function hostelStudentSearch(Request $request){
        		$hostel_id = $request->get('hostel_id');
        		$building_id = $request->get('building_id');
        		$floor_id = $request->get('floor_id');
        		$room_id = $request->get('room_id');
        		$bed_id = $request->get('bed_id');
        		$data =  HostelAssign::all();
        		if (!empty($hostel_id)) {
        			$data = $data->where("hostel_id", $hostel_id);
        		}
        		if (!empty($building_id)) {
        			$data = $data->where("building_id", $building_id);
        		}
        		if (!empty($floor_id)) {
        			$data = $data->where("floor_id", $floor_id);
        		}
        		if (!empty($room_id)) {
        			$data = $data->where("room_id", $room_id);
        		}
        		if (!empty($bed_id)) {
        			$data = $data->where("bed_id", $bed_id);
        		}
        		$allstudent = $data->all();
        		return  view('hostel.hostel_assign.student_search', ['data' => $allstudent]);
	        }

	        public function hostelStudentEdit(Request $request, $id){
                $admission = Admission::find($id);
                $hostel = HostelAssign::where('admission_id',$id)->get()->first();
		        if ($request->isMethod('post')) {
        			$request->validate([
        				'hostel_fees' => 'required',
        			]);
		            if ($request->file('student_img')) {
                        $image = $request->file('student_img');
                        $path = $image->getRealPath();
                        $student_image = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                        $image->move($destinationPath, $student_image);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'profile/' . $hostel->student_img)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'profile/' . $hostel->student_img);
                        }
                        $admission->image = $student_image;
                    }
                    if ($request->file('father_photo')) {
                        $image = $request->file('father_photo');
                        $father_photo = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_photo';
                        $image->move($destinationPath, $father_photo);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'father_photo/' . $hostel->father_photo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'father_photo/' . $hostel->father_photo);
                        }
                        $admission->father_img = $father_photo;
                    }
                    if ($request->file('mother_photo')) {
                        $image = $request->file('mother_photo');
                        $mother_photo = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_photo';
                        $image->move($destinationPath, $mother_photo);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'mother_photo/' . $hostel->mother_photo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'mother_photo/' . $hostel->mother_photo);
                        }
                        $admission->mother_img = $mother_photo;
                    }
                    $admission->aadhaar = $request->aadhaar;
                    $admission->dob = $request->dob;
                    $admission->first_name = $request->first_name;
                    $admission->last_name = $request->last_name;
                    $admission->email = $request->email;
                    $admission->mobile = $request->mobile;
                    $admission->father_name = $request->father_name;
                    $admission->father_mobile = $request->father_mobile;
                    $admission->mother_name = $request->mother_name;
                    $admission->gender_id = $request->gender_id;
                    $admission->address = $request->address;
                    $admission->save(); 
                    $hostel->user_id = Session::get('id');
                    $hostel->session_id = Session::get('session_id');
                    $hostel->branch_id = Session::get('branch_id');
                    $hostel->admission_id = $id;
                    $hostel->hostel_id = $request->hostel_id;
                    $hostel->building_id = $request->building_id;
                    $hostel->mothers_mobile = $request->mothers_mobile;
                    $hostel->floor_id = $request->floor_id;
                    $hostel->room_id = $request->room_id;
                    $hostel->bed_id = $request->bed_id;
                    $hostel->room_reference = $request->room_reference; 
                    $hostel->hostel_fees = $request->hostel_fees;
        			$hostel->mothers_mobile = $request->mothers_mobile;
        			$hostel->guardian_whatsapp = $request->guardian_whatsapp;
                    $hostel->meter_unit = 0;
                    $hostel->date = $request->admission_date;
                    $hostel->status = 1;
                    $hostel->bed_status = 1;
                    $hostel->pay_status = 1;
                    $hostel->college = $request->college;
                    $hostel->Course = $request->Course;
                    $hostel->guardian_name = $request->guardian_name;
                    $hostel->guardian_mobile = $request->guardian_mobile;
                    $hostel->guardian_tel = $request->guardian_tel;
                    $hostel->guardian_address = $request->guardian_address;
                    $hostel->duration_Of_stay = $request->duration_Of_stay;
                    if ($request->file('Student_Signature_img')) {
                      
                        $image = $request->file('Student_Signature_img');
                        $Student_Signature_img = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/Student_Signature_img';
                        $image->move($destinationPath, $Student_Signature_img);
                          if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/Student_Signature_img/' . $hostel->Student_Signature_img)) {
                        File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/Student_Signature_img/' . $hostel->Student_Signature_img);
                        }
                        $hostel->Student_Signature_img = $Student_Signature_img;
                    }
                    if ($request->file('father_Signature')) {
                        $image = $request->file('father_Signature');
                        $father_Signature = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/father_Signature';
                        $image->move($destinationPath, $father_Signature);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/father_Signature/' . $hostel->father_Signature)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/father_Signature/' . $hostel->father_Signature);
                        }
                        $hostel->father_Signature = $father_Signature;
                    }
                    if ($request->file('mother_Signature')) {
                        $image = $request->file('mother_Signature');
                        $mother_Signature = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/mother_Signature';
                        $image->move($destinationPath, $mother_Signature);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/mother_Signature/' . $hostel->mother_Signature)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/mother_Signature/' . $hostel->mother_Signature);
                        }
                        $hostel->mother_Signature = $mother_Signature;
                    }
                    if ($request->file('guardian_Signature')) {
                        $image = $request->file('guardian_Signature');
                        $guardian_Signature = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_Signature';
                        $image->move($destinationPath, $guardian_Signature);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_Signature/' . $hostel->guardian_Signature)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_Signature/' . $hostel->guardian_Signature);
                        }
                        $hostel->guardian_Signature = $guardian_Signature;
                    }
                    if ($request->file('student_id_proof')) {
                        $image = $request->file('student_id_proof');
                        $student_id_proof = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/student_id_proof';
                        $image->move($destinationPath, $student_id_proof);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/student_id_proof/' . $hostel->student_id_proof)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/student_id_proof/' . $hostel->student_id_proof);
                        }
                        $hostel->student_id_proof = $student_id_proof;
                    }
                    if ($request->file('college_id')) {
                        $image = $request->file('college_id');
                        $college_id = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/college_id';
                        $image->move($destinationPath, $college_id);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/college_id/' . $hostel->college_id)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/college_id/' . $hostel->college_id);
                        }
                        $hostel->college_id = $college_id;
                    }
                    if ($request->file('police_verification')) {
                        $image = $request->file('police_verification');
                        $police_verification = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/police_verification';
                        $image->move($destinationPath, $police_verification);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/police_verification/' . $hostel->police_verification)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/police_verification/' . $hostel->police_verification);
                        }
                        $hostel->police_verification = $police_verification;
                    }
                    if ($request->file('covid_certificate')) {
                        $image = $request->file('covid_certificate');
                        $covid_certificate = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/covid_certificate';
                        $image->move($destinationPath, $covid_certificate);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/covid_certificate/' . $hostel->covid_certificate)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/covid_certificate/' . $hostel->covid_certificate);
                        }
                        $hostel->covid_certificate = $covid_certificate;
                    } 
                    if ($request->file('father_adhar')) {
                        $image = $request->file('father_adhar');
                        $father_adhar = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/father_adhar';
                        $image->move($destinationPath, $father_adhar);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/father_adhar/' . $hostel->father_adhar)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/father_adhar/' . $hostel->father_adhar);
                        }
                        $hostel->father_adhar = $father_adhar;
                    }
                    if ($request->file('other_document')) {
                        $image = $request->file('other_document');
                        $other_document = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/other_document';
                        $image->move($destinationPath, $other_document);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/other_document/' . $hostel->other_document)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/other_document/' . $hostel->other_document);
                        }
                        $hostel->other_document = $other_document;
                    }
                    if ($request->file('guardian_photo')) {
                        $image = $request->file('guardian_photo');
                        $guardian_photo = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_photo';
                        $image->move($destinationPath, $guardian_photo);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_photo/' . $hostel->guardian_photo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_photo/' . $hostel->guardian_photo);
                        }
                        $hostel->guardian_photo = $guardian_photo;
                    }
                    $hostel->save();
			        return redirect::to('assign_student_view')->with('message', 'Student Updated Successfully !');
		        }
		        return view('hostel.hostel_assign.edit', ['admission'=>$admission,'hostel'=>$hostel]);
	        }

	        public function hostelStudentDelete(Request $request){
        		$id = $request->delete_id;
                $admission=Admission::where('id',$request->admission_id)->update(['hostel'=>0]);
        		$bed = HostelAssign::find($id);
		        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/Student_Signature_img/' . $bed->Student_Signature_img)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/Student_Signature_img/' . $bed->Student_Signature_img);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/father_Signature/' . $bed->father_Signature)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/father_Signature/' . $bed->father_Signature);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/mother_Signature/' . $bed->mother_Signature)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/mother_Signature/' . $bed->mother_Signature);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_Signature/' . $bed->guardian_Signature)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_Signature/' . $bed->guardian_Signature);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/student_id_proof/' . $bed->student_id_proof)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/student_id_proof/' . $bed->student_id_proof);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/college_id/' . $bed->college_id)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/college_id/' . $bed->college_id);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/police_verification/' . $bed->police_verification)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/police_verification/' . $bed->police_verification);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/covid_certificate/' . $bed->covid_certificate)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/covid_certificate/' . $bed->covid_certificate);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/father_adhar/' . $bed->father_adhar)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/father_adhar/' . $bed->father_adhar);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/other_document/' . $bed->other_document)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/other_document/' . $bed->other_document);
                }
        		if (File::exists(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_photo/' . $bed->guardian_photo)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'hostel/guardian_photo/' . $bed->guardian_photo);
                }
                $bed->delete();
        		return redirect::to('assign_student_view')->with('message', 'Student Deleted Successfully !');
        	}

            public function hostelAssignDashboard(Request $request){
                $data = HostelAssign::get();
                $admission = Admission::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->get();
                return view('hostel.hostel_assign.hostelAssignDashboard',['data'=>$data,'admission'=>$admission]);
            }
    
            public function hostelInformation(Request $request){
        		$hostel = Hostel::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('id', 'ASC')->get();
        		$building = HostelBuilding::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('id', 'ASC')->get();
        		$floor = HostelFloor::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('id', 'ASC')->get();
        		$room = HostelRoom::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                $bed = HostelBed::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('id', 'ASC')->get();
		        return view('hostel.hostel_assign.hostelInformation', ['hostel' => $hostel, 'building' => $building, 'floor' => $floor, 'room' => $room, 'bed' => $bed]);
	        }

            public function getHostelAssignDashboard(Request $request, $hostel_id){
                $data = HostelAssign::get();
                $admission = Admission::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->get();
                if($hostel_id == 1){
                    return view('hostel.hostel_assign.hostelAssignDashboardBoys',['data'=>$data,'admission'=>$admission]);
                }else{
                    return view('hostel.hostel_assign.hostelAssignDashboardGirls',['data'=>$data,'admission'=>$admission]);
                }
                
            }

}
