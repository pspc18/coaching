<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\Student;
use App\Models\BillCounter;
use App\Models\library\Library;
use App\Models\library\LibraryCabin;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryAssign;
use App\Models\Invoice;
use App\Models\library\LibraryPlan;
use App\Models\FeesCollect;
use App\Models\FeesDetail;
use App\Models\library\LibraryTimeSlot;
use App\Models\Setting;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller

{
     
    
       public function dashboard(){

       return view('library.library_dashboard');
    }

    public function libraryAdd(Request $request){

        if($request->isMethod('post')){
            $request->validate([
                
            'name' => 'required', 
            
            ]);
            
            $library = new Library;//model name
            $library->user_id = Session::get('id');
            $library->session_id = Session::get('session_id');
            $library->branch_id = Session::get('branch_id');
            $library->name = $request->name;
            $library->save();
            

            
            return redirect::to('library_add')->with('message', 'Library Added Successfully !');
        }
        
        $library_list = Library::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $library_list->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $library_list->orderBy('id',"DESC")->get();  
        
        return view('library.library.add',['data'=>$data]);
    }

    public function libraryEdit(Request $request,$id){
    
        $data = Library::find($id);
        if($request->isMethod('post')){
            $request->validate([
            
            'name' => 'required', 
            
            ]);
            
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->name = $request->name;
            $data->save();
            
        return redirect::to('library_add')->with('message', 'Library Updated Successfully !');
        }
        $library_list = Library::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id',"DESC")->get();
        
        return view('library.library.edit',['data'=>$data,'dataview'=>$library_list]);
    }
    
    public function library_student_plans_manage(Request $request, $id){
        $student = Admission::where('id',$id)->first();
        $data = LibraryPlan::select('library_plans.*','invoices.invoice_no','library_time_slots.study_time','library_time_slots.study_hour','library_cabins.name as library_cabin')
                                ->leftjoin('library_time_slots','library_time_slots.id','library_plans.library_time_slot_id')
                                ->leftjoin('library_cabins','library_cabins.id','library_plans.library_cabin_id')
                                ->leftjoin('invoices','invoices.id','library_plans.invoice_id')
                                ->where('library_plans.admission_id',$id)->where('library_plans.status',0)->get();
                                
        return view('library.library_assign.manage_user_plans',compact('data','student'));
    }
    
    public function change_user_plan(Request $request, $plan_id,$admission_id){
        $data = LibraryPlan::select('library_plans.*','library_cabins.name as library_cabin')
                                ->leftjoin('library_cabins','library_cabins.id','library_plans.library_cabin_id')
                                ->where('library_plans.id',$plan_id)->first();
                                
        $slots = LibraryPlan::where('admission_id',$admission_id)->where('status',0)->get(); 
        
        $blocked_slot = [];
        foreach($slots as $item){
            if($item->library_time_slot_id != $data->library_time_slot_id){
                $blocked_slot[] = $item->library_time_slot_id;
            }
        }
                                
        $time_slot = LibraryTimeSlot::orderBy('id','ASC')->get(); 
        
        
        if($request->isMethod('post')){
            LibraryPlan::where('id',$plan_id)->update(['library_cabin_id' => $request->submit_cabin_id,'renew_date' => $request->submit_renew_date,'library_time_slot_id' => $request->submit_time_slot_id,]);
            
            return redirect::to('library_student_plans_manage/'.$admission_id)->with('message', 'Plan Changed Successfully !');
        }
                                
        return view('library.library_assign.change_user_plan',compact('time_slot','data','blocked_slot'));
    }
    
    
    
    public function student_plan_delete(Request $request){
        
        LibraryPlan::where('id',$request->delete_id)->delete();
        
        return redirect::to('library_student_plans_manage/'.$request->admission_id)->with('message', 'User Plan Deleted Successfully !');
    }
    
    

    public function libraryDelete(Request $request){
       
        $id = $request->delete_id;
       
        $hostel = Library::find($id)->delete();
        $cabin = LibraryCabin::where('library_id', $id)->delete();
        
        return redirect::to('library_add')->with('message', 'Library Deleted Successfully !');
    }

    public function cabinAdd(Request $request){

        if($request->isMethod('post')){
            $request->validate([
            
            'library_id' => 'required',    
            'name' => 'required', 

            ]);
           
         $lastCabin = LibraryCabin::where('library_id', $request->library_id)->orderBy('name','DESC')->first();
              
                $int = (int)$request->name;
              
              if(!empty($lastCabin))
              {
                 $int1 = (int)$lastCabin->name;
              }else{
                  $int1 = 0;
              }
                $library = new LibraryCabin;//model name
                for($i = $int1+1;$i<=$int1+$int;$i++)
                {
                // $hostel->user_id = Session::get('id');
                // $hostel->library_id = $request->library_id;
                // $hostel->name = "";
                // $hostel->save();   
                   
                       $data = LibraryCabin::insert([
    'user_id' => Session::get('id'),
    'library_id' => $request->library_id,
    'session_id' => Session::get('session_id'),
    'branch_id' => Session::get('branch_id'),
    'name' => $i]);
                    
                }
         
        return redirect::to('cabin_add')->with('Library')->with('message', 'Cabin Added Successfully !');
        }
        $cabin_list = LibraryCabin::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $cabin_list->where('branch_id',Session::get('branch_id'));
        }
		 $data = $cabin_list->groupBy('library_id')->orderBy('id',"DESC")->get();  
   
        return view('library.cabin.add',['data'=>$data]);
    }
    
    
 public function manageSeatMap(Request $request){
        
        $time_slot = LibraryTimeSlot::orderBy('id','ASC')->get();
        $search['time_slot_id'] = $request->time_slot_id ?? '1';
        
        $cabins = LibraryCabin::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
        $libraryCabin ="";
        $time = LibraryTimeSlot::where('id',$search['time_slot_id'])->first();

            foreach($cabins as $key => $type){
                
                $alredyAssign = LibraryPlan::select('library_plans.*','admissions.first_name','admissions.admissionNo')
                                            ->leftJoin('admissions','admissions.id','library_plans.admission_id')
                                            ->where('admissions.status',1)->where('library_plans.session_id',Session::get('session_id'))
                                            ->where('library_plans.branch_id',Session::get('branch_id'))
                                            ->where('library_plans.library_cabin_id',$type->id)
                                            ->where('library_plans.library_time_slot_id',$search['time_slot_id'])
                                            ->where('library_plans.status',0)
                                            ->get()->first();
                
                if(!empty($alredyAssign)){
                    if($alredyAssign->library_cabin_id == $type->id){ 
                    $libraryCabin .= '<div class="seat assigned booked_student"><div class="action_seat booked_seat_data" data-plan_id="'. $alredyAssign->id .'" data-name="' . $alredyAssign->first_name . '" data-seat_id="' . $type->id . '" data-seat_name="' . $type->name . '"  data-time_slot_name="'.$time->study_time.'"><i class="fa fa-trash"></i></div> S-' . $type->name . '</div>';
                    }                   
                }
                    else{
                       $not_assign_time_slot = LibraryPlan::select('library_plans.*','admissions.first_name','admissions.admissionNo')
                                            ->leftJoin('admissions','admissions.id','library_plans.admission_id')
                                            ->where('admissions.status',1)->where('library_plans.session_id',Session::get('session_id'))
                                            ->where('library_plans.branch_id',Session::get('branch_id'))
                                            ->whereIn('library_plans.library_time_slot_id',explode(',', $time->not_assign_time_slot_id ?? ''))
                                            ->where('library_plans.library_cabin_id',$type->id)
                                            ->where('library_plans.status',0)
                                            ->get()
                                            ->first();
                    

                    if(!empty($not_assign_time_slot)){
                   if($not_assign_time_slot->library_cabin_id == $type->id){ 
                     
                $time_slot_name = LibraryTimeSlot::where('id',$not_assign_time_slot->library_time_slot_id)->first(); 
                 
                    $libraryCabin .= '<div class="seat assigned booked_student"><div class="action_seat booked_seat_data" data-plan_id="'. $not_assign_time_slot->id .'"  data-name="' . $not_assign_time_slot->first_name . '" data-seat_id="' . $type->id . '" data-seat_name="' . $type->name . '"  data-time_slot_name="'.$time_slot_name->study_time.'"><i class="fa fa-trash"></i></div> S-' . $type->name . '</div>';
                   }else{
                     $libraryCabin .= '<div class="seat seat_not_assigned"><div class="action_seat" onClick="assignSeat('.$type->id.','.$type->name .')"><i class="fa fa-plus"></i></div>  S-' . $type->name . '</div>';
                   }               
                }else{
                    
                     $libraryCabin .= '<div class="seat seat_not_assigned"><div class="action_seat" onClick="assignSeat('.$type->id.','.$type->name .')"><i class="fa fa-plus"></i></div>  S-' . $type->name . '</div>';

                }
                }
                    
                    
                }
           

        
        return view('library.cabin.manage_seat_map',['time_slot' => $time_slot, 'search' => $search,'cabins' => $libraryCabin]);
    }

    public function cabinEdit(Request $request,$id){
    
        $data = LibraryCabin::find($id);
        if($request->isMethod('post')){
            $request->validate([
            
            'library_id' => 'required',
            'name' => 'required', 
            
            ]);
            $oldData = LibraryCabin::where('library_id', $request->library_id)->where('name', $request->name)->get()->first();
            if(!empty($oldData)){
                return redirect::to('cabin_edit/$request->library_id')->with('error', 'This Cabin Already Exists !');
            }else{            
                $data->library_id = $request->library_id;
                $data->name = $request->name;
                $data->save();
            }   
        return redirect::to('cabin_add')->with('message', 'Cabin Updated Successfully !');
        }
        $cabin_list = LibraryCabin::groupBy('library_id')->orderBy('id',"DESC")->get();
        
        return view('library.cabin.edit',['data'=>$data,'dataview'=>$cabin_list]);
    }

    public function cabinDelete(Request $request){
       
        $id = $request->delete_id;
       
        $building = LibraryCabin::find($id)->delete();
        
        return redirect::to('cabin_add')->with('message', 'Cabin Deleted Successfully !');
    }

    public function libraryAssign(Request $request){
       
        $admissionBillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type', 'StudentAdmission')->get()->first();

             if($request->isMethod('post')){
                   $student_image ='';
               if ($request->file('profile')) {
                $image = $request->file('profile');
                $path = $image->getRealPath();
                $student_image = time() . uniqid() . $image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                $image->move($destinationPath, $student_image);
            }
            
                $addadmission = new Admission(); //model name
                $addadmission->user_id = Session::get('id');
                $addadmission->session_id = Session::get('session_id');
                $addadmission->branch_id = Session::get('branch_id');
                $addadmission->branch_id = Session::get('branch_id');
                $addadmission->school = 0;
                $addadmission->library = 1;
                $addadmission->hostel = 0;
                $addadmission->admissionNo = $request->admission_no;
                $addadmission->first_name = $request->first_name;
                $addadmission->gender_id = $request->gender_id;
                $addadmission->mobile = $request->mobile;
                $addadmission->email = $request->email;
                $addadmission->father_name = $request->father_name;
                $addadmission->admission_date = $request->registration_date;
                $addadmission->address = $request->address;
                $addadmission->country_id = $request->country;
                $addadmission->state_id = $request->state;
                $addadmission->city_id = $request->city;
                $addadmission->pincode = $request->pincode;
                $addadmission->id_proof = $request->id_proof;
                $addadmission->id_number = $request->id_number;
                $addadmission->dob = $request->dob;
                $addadmission->userName = $request->mobile;
                $addadmission->password = Hash::make('12345678');
                $addadmission->confirm_password = '12345678';
                $addadmission->status = 1;
                $addadmission->image = $student_image;
                $addadmission->remark_1 = $request->remark;
                $addadmission->save();  
                
                
                $admission_id = $addadmission->id; 
                
                $counter = !empty($admissionBillCounter->counter) ? $admissionBillCounter->counter : 0 ;
                $admissionBillCounter->counter = $counter + 1 ;
                $admissionBillCounter->save();
                
                if($request->locker_fees_check != null){
                    $locker_fees = $request->locker_fees;
                }else{
                    $locker_fees = 0;
                }
                
                $library = new LibraryAssign;//model name
                $library->user_id = Session::get('id');
                $library->session_id = Session::get('session_id');
                $library->branch_id = Session::get('branch_id');
                $library->admission_id = $admission_id;
                $library->library_fees = $request->library_amount;
                $library->library_id = 1;
                $library->date = $request->registration_date;
                $library->locker_renewal_date = $request->lockerRenewalDate;
                $library->locker_amount = $locker_fees;
                $library->library_locker_id = $request->library_locker_id;
                $library->status = 1;
                $library->save();
                
                $library_id = $library->id;
                
                
                $pay = new Invoice;
                $pay->user_id = Session::get('id');
                $pay->session_id = Session::get('session_id');
                $pay->branch_id = Session::get('branch_id');
                $pay->invoice_no = date('d').random_int(1000, 9999);
                if(!empty($request->time_slot_id)){ 
                    $pay->library_time_slot_id = implode(',', $request->time_slot_id);
                }
                $pay->library_assign_id = $library_id;
                $pay->admission_id = $admission_id;
                $pay->paid_amount = $request->amount;
                $pay->total_amount = $request->library_amount + $locker_fees;
                $pay->due_amount = $request->duesAmount;
                $pay->discount = $request->discountAmount;
                if($request->locker_fees_check != null){
                    $pay->locker_renewal_date = $request->lockerRenewalDate;
                    $pay->library_locker_amount = $locker_fees;
                    $pay->library_locker_id = $request->library_locker_id;
                }
                $pay->invoice_type = 0;
                $pay->save();
                
                $library_invoice_id = $pay->id;
                
                $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type', 'FeesSlip')->get()->first();
                
                $FeesDetail = new FeesDetail; //model name
                $FeesDetail->user_id = Session::get('id');
                $FeesDetail->session_id = Session::get('session_id');
                $FeesDetail->branch_id = Session::get('branch_id');
                $FeesDetail->fees_counter_id = 1;
                $FeesDetail->receipt_no = $BillCounter->counter + 1;
                $FeesDetail->admission_id = $admission_id;
                $FeesDetail->invoice_id = $library_invoice_id;
                $FeesDetail->discount = $request->discountAmount;
                $FeesDetail->discount_type = $request->discountType;
                $FeesDetail->discount_value = $request->discountValue;
                $FeesDetail->paid_amount = $request->amount;
                $FeesDetail->total_amount = $request->library_amount + $locker_fees;
                $FeesDetail->payment_mode_id = $request->payment_mode_id;
                $FeesDetail->library_due_amount = $request->duesAmount;
                $FeesDetail->fees_type = 2;
                $FeesDetail->status = 0;
                $FeesDetail->date = date('Y-m-d');
                $FeesDetail->save(); 
                
                $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                            $BillCounter->counter = $counter + 1;
                            $BillCounter->save();
                
                $time_slot = [];       
                $cabins = [];       
                if(!empty($request->time_slot_id)){            
                    foreach($request->time_slot_id as $key => $slot_id){
                        $library_slot = LibraryTimeSlot::where('id',$slot_id)->first();
                        if(!empty($library_slot)){
                            $time_slot[] = $library_slot->study_time;
                        }
                        $feesSlot = new LibraryPlan;
                        $feesSlot->user_id = Session::get('id');
                        $feesSlot->session_id = Session::get('session_id');
                        $feesSlot->branch_id = Session::get('branch_id');
                        $feesSlot->admission_id = $admission_id;
                        $feesSlot->invoice_id = $library_invoice_id;
                        $feesSlot->library_assign_id = $library_id;
                        $feesSlot->library_time_slot_id = $slot_id;
                        if(!empty($request->cabin_seat_id[$key])){ 
                            $feesSlot->library_cabin_id = $request->cabin_seat_id[$key];
                            $library_cabin = LibraryCabin::where('id',$request->cabin_seat_id[$key])->first(); 
                            if(!empty($library_cabin)){
                                $cabins[] = "S-".$library_cabin->name;
                            }
                        }else{
                          $cabins[] = "No Seat";
                        }
                        $feesSlot->renew_date  = $request->renew_date [$key];
                        $feesSlot->status  = 0;
                        $feesSlot->save();
                    }         
                }
           
        
            
    	$template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','libraryassign')->first();
        
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first(); 
        $library_name = Library::where('id',1)->first();
        $locker = LibraryLocker::find($request->library_locker_id);
        if(!empty($locker)){
         $locker_name =  $locker->name;
 
        }else{
          $locker_name = null;  
        }

        $times = [];
        foreach($time_slot as $key => $item){
            $times[] = $item."(". $cabins[$key] .")";
        }

        $seat_slot = implode(',', $times);
        
                     $arrey1 =   array(
                                    '{#name#}',
                                    '{#library_name#}',
                                    '{#cabin_name#}',
                                    '{#library_fees#}',
                                    '{#locker#}',
                                    '{#locker_amount#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $request->first_name." ".$request->last_name,
                                    $library_name->name,
                                    $seat_slot,
                                    $request->library_amount + $locker_fees,
                                    $locker_name,
                                    $locker_fees,
                                    $setting->name);
                    
                     if($template->status != 1){
                            if($request->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $request->email, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($request->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($request->mobile, $whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($request->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($request->mobile, $sms);
                                    }
                                }
                            }    
                    } 
                    
                $template1 =  MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','fees-collect')->first();
        
       
    
                    $arrey1 =   array(
                                    '{#name#}',
                                    '{#fees#}',
                                    '{#discount#}',
                                     '{#duesAmount#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $request->first_name,
                                    $request->amount,
                                    $request->discountAmount,
                                    $request->duesAmount,
                                    $setting->name);
                    
                     if($template1->status != 1){
                            if($request->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template1->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template1->email_content);
                                        $emailData = ['email' => $request->email, 'data' => $message, 'subject' => $template1->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($request->mobile != ""){
                                    if($template1->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template1->whatsapp_content);
                                        Helper::sendWhatsappMessage($request->mobile, $whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($request->mobile != ""){
                                    if($template1->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template1->sms_content);
                                        Helper::SendMessage($request->mobile, $sms);
                                    }
                                }
                            }    
                    } 

            return redirect::to('library_student_view')->with('message', 'Library Assign Successfully !');
            }
            
            $time_slot =LibraryTimeSlot::orderBy('id','ASC')->get();           

        return view('library.library_assign.add',['admissionBillCounter'=>$admissionBillCounter,'time_slot'=>$time_slot]);
    } 


   public function libraryStudentRenew(Request $request){
        //dd($request);
        $data = LibraryAssign::select('library_assign.*','admissions.first_name','admissions.admissionNo')
                                ->leftJoin('admissions','admissions.id','library_assign.admission_id')
                                ->where('admissions.status',1)
                                ->where('library_assign.status',1)
                                ->get();
                                
        $time_slot =LibraryTimeSlot::orderBy('id','ASC')->get(); 
        if($request->isMethod('post')){
           
                if($request->locker_fees_check != null){
                    $locker_fees = $request->locker_fees;
                }else{
                    $locker_fees = 0;
                }
                $library = LibraryAssign::find($request->library_assign_id);
                 $admi = Admission::find($library->admission_id);
                 if($request->locker_fees_check != null){
                    $library->locker_renewal_date = $request->lockerRenewalDate;
                    $library->locker_amount = $request->locker_fees;
                    $library->library_locker_id = $request->library_locker_id;
                  }else{
                      $library->locker_renewal_date = null;
                    $library->locker_amount = null;
                    $library->library_locker_id =  null;
                  }
                $library->save();
                
                $pay = new Invoice;
                $pay->user_id = Session::get('id');
                $pay->session_id = Session::get('session_id');
                $pay->branch_id = Session::get('branch_id');
                $pay->invoice_no = date('d').random_int(1000, 9999);
                $pay->library_time_slot_id = implode(',', $request->time_slot_id);
                if($request->locker_fees_check != null){
                    $pay->locker_renewal_date = $request->lockerRenewalDate;
                    $pay->library_locker_amount = $locker_fees;
                    $pay->library_locker_id = $request->library_locker_id;
                }else{
                    $pay->locker_renewal_date = null;
                    $pay->library_locker_amount = null;
                    $pay->library_locker_id = null;
                }
                
                $pay->library_assign_id = $library->id;
                $pay->admission_id = $library->admission_id;
                $pay->paid_amount = $request->amount;
                $pay->total_amount = $request->library_amount + $locker_fees;
                $pay->due_amount = $request->duesAmount;
                $pay->discount = $request->discountAmount;
                $pay->invoice_type = 0;
                $pay->save();
                $library_invoice_id = $pay->id;
                
                $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type', 'FeesSlip')->get()->first();
                
                $FeesDetail = new FeesDetail;
                $FeesDetail->user_id = Session::get('id');
                $FeesDetail->session_id = Session::get('session_id');
                $FeesDetail->branch_id = Session::get('branch_id');
                $FeesDetail->fees_counter_id = 1;
                $FeesDetail->receipt_no = $BillCounter->counter + 1;
                $FeesDetail->admission_id = $library->admission_id;
                $FeesDetail->invoice_id = $pay->id;
                //$FeesDetail->library_plan_id = implode(',', $request->time_slot_id);
                $FeesDetail->discount = $request->discountAmount;
                $FeesDetail->discount_type = $request->discountType;
                $FeesDetail->discount_value = $request->discountValue;
                $FeesDetail->paid_amount = $request->amount;
                $FeesDetail->total_amount = $request->library_amount + $locker_fees;
                $FeesDetail->payment_mode_id = $request->payment_mode_id;
                $FeesDetail->library_due_amount = $request->duesAmount;
                $FeesDetail->fees_type = 2;
                $FeesDetail->status = 0;
                $FeesDetail->date = date('Y-m-d');
                $FeesDetail->save(); 
                
                $cabins = [];
                $time_slot = [];
                $renewDate = [];
                  
                  
                if(!empty($request->time_slot_id)){            
                    foreach($request->time_slot_id as $key => $slot_id){
                        $oldData = LibraryPlan::where('admission_id',$library->admission_id)->where('library_time_slot_id',$slot_id)->first();
                        if(!empty($oldData)){
                            $old = LibraryPlan::where('admission_id',$library->admission_id)->where('library_time_slot_id',$slot_id)->update(['status'=> 1]);
                        }
                    $library_slot = LibraryTimeSlot::where('id',$slot_id)->first();
                     if(!empty($library_slot)){
                            $time_slot[] = $library_slot->study_time;
                        }
                        $feesSlot = new LibraryPlan;
                        $feesSlot->user_id = Session::get('id');
                        $feesSlot->session_id = Session::get('session_id');
                        $feesSlot->branch_id = Session::get('branch_id');
                        $feesSlot->admission_id = $library->admission_id;
                        $feesSlot->invoice_id = $library_invoice_id;
                        $feesSlot->library_assign_id = $library->id;
                        $feesSlot->library_time_slot_id = $slot_id;
                        if(!empty($request->cabin_seat_id[$key])){ 
                            $feesSlot->library_cabin_id = $request->cabin_seat_id[$key];
                      
                            $library_cabin = LibraryCabin::where('id',$request->cabin_seat_id[$key])->first(); 
                            if(!empty($library_cabin)){
                                $cabins[] = "S-".$library_cabin->name;
                            }
                        }else{
                          $cabins[] = "No Seat";
                        }
                        
                        if(!empty($request->renew_date[$key])){ 
                            $feesSlot->renew_date  = $request->renew_date [$key];
                      
                            $renewDate[] = date('d-M-Y',strtotime($request->renew_date[$key]));
                        }else{
                          $renewDate[] = "";
                        }
                        
                        $feesSlot->status  = 0;
                        $feesSlot->save();
                    }         
                }
                
                	$template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','library-renew')->first();
        
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first(); 
        $library_name = Library::where('id',1)->first();
        $locker = LibraryLocker::find($request->library_locker_id);
 if(!empty($locker)){
         $locker_name =  $locker->name;
 
        }else{
          $locker_name = null;  
        }
        $times = [];
        foreach($time_slot as $key => $item){
            $times[] = $item."(". $cabins[$key] ." , ".$renewDate[$key]." )";
        }

        $seat_slot = implode(',', $times);
        // dd($seat_slot);
                    $arrey1 =   array(
                                    '{#name#}',
                                    '{#library_name#}',
                                    '{#cabin_name#}',
                                    '{#library_fees#}',
                                    '{#locker#}',
                                    '{#locker_amount#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $admi->first_name." ".$admi->last_name,
                                    $library_name->name,
                                    $seat_slot,
                                    $request->library_amount + $locker_fees,
                                    $locker_name,
                                    $locker_fees,
                                    $setting->name);
                    
                    
                     if($template->status != 1){
                            
                            if($branch->whatsapp_srvc != 0){
                                if ($admi->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($admi->mobile, $whatsapp);
                                    }
                                }
                            }
                            
                               
                    } 
                    
                    
                    $template1 =  MessageTemplate::Select('message_templates.*','message_types.slug')
                                ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                                ->where('message_types.status',1)->where('message_types.slug','fees-collect')->first();
        
       
    
                    $arrey1 =   array(
                                    '{#name#}',
                                    '{#fees#}',
                                    '{#discount#}',
                                    '{#duesAmount#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $admi->first_name." ".$admi->last_name,
                                    $request->amount,
                                    $request->discountAmount,
                                    $request->duesAmount,
                                    $setting->name);
                    
                     if($template1->status != 1){
                            if($branch->whatsapp_srvc != 0){
                                if ($admi->mobile != ""){
                                    if($template1->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template1->whatsapp_content);
                                        Helper::sendWhatsappMessage($admi->mobile, $whatsapp);
                                    }
                                }
                            }
                    } 
                    
                    
                    
                    
            return redirect::to('library_student_view')->with('message', 'Library Renew Successfully !');
            }                               
                                
        return view('library.library_assign.renew',['data'=>$data,'time_slot'=>$time_slot]);                 
    }
    
    public function getLastPlans(Request $request){
        $data = LibraryPlan::select('library_plans.*','library_cabins.name as cabin_name','fees_detail.discount_type','fees_detail.discount','fees_detail.discount_value')
                            ->leftJoin('library_cabins','library_cabins.id','library_plans.library_cabin_id')
                            ->leftJoin('fees_detail','fees_detail.admission_id','library_plans.admission_id')
                            ->where('library_plans.library_assign_id',$request->library_assign_id)
                            ->where('library_plans.status',0)
                            ->where('fees_detail.fees_type',2)->get();
        return $data;
    }
    
    public function getLockerDetails(Request $request){
        $data = LibraryAssign::select('library_assign.*','library_lockers.name as locker_name')
                            ->leftJoin('library_lockers','library_lockers.id','library_assign.library_locker_id')
                            ->where('library_assign.id',$request->library_assign_id)->whereNotNull('library_assign.library_locker_id')->first();
        return $data;
    }
    
    
    public function libraryStudentEdit(Request $request,$id){
         $admission = Admission::find($id);
         $library = LibraryAssign::where('admission_id',$admission->id)->get()->first();
		if ($request->isMethod('post')) {
			$request->validate([
				'first_name' => 'required',
				'mobile' => 'required',
				'father_name' => 'required',
				'gender_id' => 'required',
				'address' => 'required',
			]);
                $admission->first_name = $request->first_name;
                $admission->last_name = $request->last_name;
                $admission->email = $request->email;
                $admission->mobile = $request->mobile;
                $admission->father_name = $request->father_name;
                $admission->gender_id = $request->gender_id;
                $admission->address = $request->address;
                $admission->save(); 
                
                $library->user_id = Session::get('id');
                $library->session_id = Session::get('session_id');
                $library->branch_id = Session::get('branch_id');
                $library->admission_id = $id;
                $library->status = $request->status;
                $library->save();
			return redirect::to('library_student_view')->with('message', 'Library Student Updated Successfully !');
		}

		return view('library.library_assign.edit', ['admission'=>$admission,'library'=>$library]);
    } 
 
    public function libraryStudentView(Request $request){
        $search['filter'] = $request->filter;
        $data = LibraryAssign::select('library_assign.*','admissions.first_name','admissions.admissionNo','admissions.mobile','admissions.email','admissions.admission_date')
                               ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                               ->leftjoin('library_plans','library_plans.admission_id','library_assign.admission_id')->where('library_plans.status',0);
        if($request->isMethod('post')){
		      if($request->filter == 'active') {
                    $data = $data->where('library_assign.status',1);
                }
                if($request->filter == 'expired') {
                    $data = $data->where('library_plans.renew_date','<=',date('Y-m-d'));
                } 
                if($request->filter == 'expired_last_15_days') {
                    $date = date('Y-m-d', strtotime("-15 day"));
                    $data = $data->where('library_plans.renew_date','>=',$date)->where('library_plans.renew_date','<=',date('Y-m-d'));
                }
                if($request->filter == 'expired_yesterday') {
                    $date = date('Y-m-d', strtotime("-1 day"));
                    $data = $data->where('library_plans.renew_date','>=',$date)->where('library_plans.renew_date','<=',date('Y-m-d'));
                }
                if($request->filter == 'expiring_today') {
                    $data = $data->where('library_plans.renew_date',date('Y-m-d'));
                }
                if($request->filter == 'expiring_3_days') {
                   $date = date('Y-m-d', strtotime("+3 day"));
                    $data = $data->where('library_plans.renew_date','<=',$date)->where('library_plans.renew_date','>=',date('Y-m-d'));
                }
                if($request->filter == 'expiring_7_days') {
                    $date = date('Y-m-d', strtotime("+7 day"));
                    $data = $data->where('library_plans.renew_date','<=',$date)->where('library_plans.renew_date','>=',date('Y-m-d'));
                }
                if($request->filter == 'expiring_15_days') {
                   $date = date('Y-m-d', strtotime("+15 day"));
                    $data = $data->where('library_plans.renew_date','<=',$date)->where('library_plans.renew_date','>=',date('Y-m-d'));
                }
                if($request->filter == 'new_student_today') {
                    $data = $data->whereDate('library_assign.created_at',date('Y-m-d'));
                }
                if($request->filter == 'new_student_yesterday') {
                    $data = $data->whereDate('library_assign.created_at','=',date('Y-m-d', strtotime("-1 day")));
                }
                if($request->filter == 'new_student_this_month') {
                    $data = $data->whereYear('library_assign.created_at', '=', date('Y-m-d'))->whereMonth('library_assign.created_at', '=', date('Y-m-d'));
                }
                if($request->filter == 'new_student_last_month') {
                    $data = $data->whereYear('library_assign.created_at', '=', date('Y-m-d'))->whereMonth('library_assign.created_at', '=', date('Y-m-d', strtotime("-1 month")));
                }
            }
            if(Session::get('role_id') > 1){
                $data = $data->where('library_assign.branch_id',Session::get('branch_id'));
             }
            if (!empty(Session::get('admin_branch_id'))) {
               $data = $data->where('library_assign.branch_id', Session::get('admin_branch_id'));
            }
            $alllibrary = $data->where('library_assign.session_id',Session::get('session_id'))
		 ->orderBy('library_assign.id','DESC')->groupBy('library_assign.id')->get();  
        return view('library.library_assign.view',['data'=>$alllibrary, 'search'=>$search]);
    } 

 public function libraryStudentStatus(Request $request)
{

    if($request->status_id == 1){
       
           $library= LibraryAssign::where('id',$request->student_id)->update(['status' =>0]);

    }else{
        
           $library=  LibraryAssign::where('id',$request->student_id)->update(['status' =>1]);
    }
 
    return redirect('library_student_view')->with('message', 'Student Status Changed Successfully!');
}

 
    public function libraryStudentDelete(Request $request){

        $id = $request->delete_id;
      
        $bed = LibraryAssign::find($id)->delete();
        $add  = Admission::where('admissionNo', $request->admission_id)->get()->first();
        if($add->school > 0 || $add->hostel > 0){
         $admission = Admission::where('id', $request->admission_id)->update(['library' => 0]);
        }else{
         $admission = Admission::find($request->admission_id)->delete();

        }
        return redirect::to('library_student_view')->with('message', 'Student Deleted Successfully !');
    } 

    public function stuCabinDetail(Request $request){
       
        $data = LibraryAssign::select('library_assign.*','library.name as library_name','cabin.name as cabin_name','admission.first_name','admission.last_name','admission.mobile','admission.email','admission.address')
        ->leftjoin('librarys as library','library.id','library_assign.library_id')
        ->leftjoin('admissions as admission','admission.id','library_assign.admission_id')
        ->leftjoin('library_cabins as cabin','cabin.id','library_assign.cabin_id')
        ->where('cabin_id',$request->cabin_id)->get()->first();
   
        echo json_encode($data);
    }      

    public function searchSchoolStudent(Request $request){
       
        $search['name'] = $request->name;
        
        if($request->isMethod('post')){
            $request->validate([]);
            $data =  Admission::with('ClassTypes')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'));
                if(!empty($request->name)){
                    $data = $data->where('first_name', 'like', '%' .$request->name. '%')
                        ->orWhere('mobile', 'like', '%' .$request->name. '%')
                        ->orWhere('aadhaar', 'like', '%' .$request->name. '%')
                        ->orWhere('email', 'like', '%' .$request->name. '%')
                        ->orWhere('father_name', 'like', '%' .$request->name. '%')
                        ->orWhere('mother_name', 'like', '%' .$request->name. '%')
                        ->orWhere('address', 'like', '%' .$request->name. '%');
                }
                if(!empty($request->admissionNo)){
                   $data = $data ->where("admissionNo", $request->admissionNo);
                }                 
                if(!empty($request->class_search_id)){
                   $data = $data ->where("class_type_id", $request->class_search_id);
                }  
      
        $allstudents = $data->orderBy('id','DESC')->get();
        }
      return  view('library.library_assign.search_view',['data'=>$allstudents]);
    }     
    
    
     public function libraryPrint(Request $request,$id){
        $student_id  =  LibraryAssign::select('library_assign.*','admissions.image','admissions.first_name','admissions.father_name','admissions.mobile','admissions.address')
                        ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                        ->where('library_assign.id',$id)->first();
               //dd($student_id);         
        return view('print_file.student_print.library_student_id',['data'=>$student_id]);
    }
    
    public function library_student_detail(Request $request,$id){
        $student_id  =  LibraryAssign::select('library_assign.*','admissions.image','admissions.first_name','admissions.father_name',
                        'admissions.mobile','admissions.address','admissions.email','gender.name as gender_name',
                        'admissions.dob','admissions.id_number','admissions.id_proof')
                        ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                        ->leftjoin('gender','gender.id','admissions.gender_id')
                        ->where('library_assign.id',$id)->first();
                
                    $image = !empty($student_id['image']) ? env('IMAGE_SHOW_PATH').'profile/'.$student_id['image'] :  env('IMAGE_SHOW_PATH').'default/user_image.jpg';  
                    $arrey['image'] = $image;
                    $arrey['name'] =   $student_id->first_name." ".$student_id->last_name;
                    $arrey['email'] =   $student_id->email;
                    $arrey['mobile'] =  $student_id->mobile;
                    $arrey['gender_name'] =   $student_id->gender_name;
                    $arrey['id_proof'] =  $student_id->id_proof;
                    $arrey['id_number'] =  $student_id->id_number;
                    $arrey['father_name'] =  $student_id->father_name;
                    $arrey['father_mobile'] =  $student_id->father_mobile;
                    $arrey['dob'] =  $student_id->dob;
                    $arrey['address'] =  $student_id->address;           
        return $arrey;
    }
    
        
    public function blankSeatAssign(Request $request){
        LibraryPlan::where('id',$request->library_plan_id)->update(['library_cabin_id' => $request->library_cabin_id]);
        
        return redirect::to('manage_seat_map')->with('message', 'User Assigned Successfully For this Seat !');

    }
    
    public function seat_unassign(Request $request){
        LibraryPlan::where('id',$request->library_plan_id)->update(['library_cabin_id' => null]);
        
        return redirect::to('manage_seat_map')->with('message', 'User Unassigned Successfully From this Seat !');

    }

    

     
}