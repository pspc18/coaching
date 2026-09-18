<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Setting;
use App\Models\StaffSalary;
use App\Models\StaffSalaryDetail;
use App\Models\SalaryDocument;
use App\Models\Teacher;
use App\Models\TeacherCategory;
use App\Models\TeacherAttendance;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Carbon\Carbon;
use Hash;
use Str;
use Helper;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalaryController extends Controller

{
            public function staff_salary_view(Request $request){
                $search['month_id'] = $request->month_id;
                $data = StaffSalaryDetail::select('staff_salary_details.*')
                ->leftjoin('users as User','User.id','staff_salary_details.staff_id')
                ->where('staff_salary_details.session_id',Session::get('session_id'))->where('staff_salary_details.branch_id',Session::get('branch_id'))
                ->where('staff_salary_details.staff_id',Session::get('id'));
                if($request->isMethod('post')){
                    if(!empty($request->month_id)){
                        $data = $data->where("staff_salary_details.month_id",$request->month_id);
                    }             
                    $allStaff = $data->get();
                }
                else{
                    $allStaff = $data->orderBy('staff_salary_details.month_id','DESC')->get();
                }
                return view('user.salary.staff_salary_view',['data'=>$allStaff,'search'=>$search]);
            }
            
            public function salaryDetails(Request $request){
                $search['role_id'] = $request->role_id;
                $search['month_id'] = $request->month_id;
                $search['name'] = $request->name;
                $data = StaffSalaryDetail::select('staff_salary_details.*')
                ->leftjoin('users as User','User.id','staff_salary_details.staff_id')
                ->where('staff_salary_details.session_id',Session::get('session_id'))->where('staff_salary_details.branch_id',Session::get('branch_id'));
                /* $data = StaffSalary::select('staff_salarys.*')->with('Month')
                ->leftjoin('users as User','User.id','staff_salarys.staff_id')
                ->where('staff_salarys.session_id',Session::get('session_id'))->where('staff_salarys.branch_id',Session::get('branch_id'));*/
                if (!empty(Session::get('admin_branch_id'))) {
                   $data = $data->where('staff_salary_details.branch_id', Session::get('admin_branch_id'));
                }
                if($request->isMethod('post')){
                    if(!empty($request->role_id)){
                       $data = $data->where("User.role_id",$request->role_id);
                    }              
                    if(!empty($request->month_id)){
                       $data = $data->where("staff_salary_details.month_id",$request->month_id);
                    }             
                    if(!empty($request->name)){
                        $data = $data->where('User.first_name', 'like', '%' .$request->name. '%')
                        ->orWhere('User.last_name', 'like', '%' .$request->name. '%')
                        ->orWhere('User.mobile', 'like', '%' .$request->name. '%')
                        ->orWhere('User.email', 'like', '%' .$request->name. '%')
                        ->orWhere('User.father_name', 'like', '%' .$request->name. '%');
                    }
                    $allStaff = $data->orderBy('id','DESC')->get();
                }
                else{
                    $allStaff = $data->where("staff_salary_details.month_id",date('m'))->orderBy('staff_salary_details.name','DESC')->get();
                }
                return view('user.salary.view',['data'=>$allStaff,'search'=>$search]);
            } 
            
            public function assignSalaryDetail(Request $request){
                if($request->isMethod('post')){
                if(!empty($request)){
                    foreach($request->staff_id as $key=> $id){
                        if($request->salary[$key] >= 0){
                            $check_exist = StaffSalary::where('staff_id',$id)->first();
                            if(empty($check_exist)){
                                $add = new StaffSalary;//model name 
                            }
                            else{
                                $add = $check_exist;
                            }
                            $add->user_id = Session::get('id');
                            $add->session_id = Session::get('session_id');
                            $add->branch_id = Session::get('branch_id');   
                            $add->staff_id = $id;   
                            $add->first_name = $request->first_name[$key];              
                            $add->last_name = $request->last_name[$key];
                            if($request->basic[$key] > 0 ){
                                $add->basic_amt = ($request->basic[$key]/$request->salary[$key])*100;   
                            }
                            else{
                                $add->basic_amt = 0;
                            }
                            if($request->da[$key] > 0 ){
                                $add->da = ($request->da[$key]/$request->salary[$key])*100;   
                            }
                            else{
                                $add->da = 0;
                            }
                            if($request->pf[$key] > 0 ){
                                $add->pf = $request->pf[$key];   
                            }
                            else{
                                $add->pf = 0;
                            }
                            if($request->tds[$key] > 0 ){
                                $add->tds = $request->tds[$key];
                            }
                            else{
                                $add->tds = 0;
                            }
                            $add->total_amount = $request->salary[$key];   
                            $add->save();
                        }
                    }
                }
                return response()->json(['status' => 'success','message' => 'Admission Updated Successfully.',]);
              }
            }
            
            public function assignSalary(Request $request){
                $search['role_id'] = $request->role_id;
                $search['name'] = $request->name;
                $data = User::select('users.*')
                ->leftjoin('staff_salarys as salary','salary.staff_id','users.id')
                ->where('users.status',1)->where('users.branch_id',Session::get('branch_id'))
                ->whereNotIn('users.role_id',[1,3]);
                if (!empty(Session::get('admin_branch_id'))) {
                   $data = $data->where('users.branch_id', Session::get('admin_branch_id'));
                }
                if($request->isMethod('post')){
                    if(!empty($request->role_id)){
                       $data = $data->where("User.role_id",$request->role_id);
                    }              
                    if(!empty($request->name)){
                        $data = $data->where('User.first_name', 'like', '%' .$request->name. '%')
                        ->orWhere('User.last_name', 'like', '%' .$request->name. '%')
                        ->orWhere('User.mobile', 'like', '%' .$request->name. '%')
                        ->orWhere('User.email', 'like', '%' .$request->name. '%')
                        ->orWhere('User.father_name', 'like', '%' .$request->name. '%');
                    }
                }
                $allStaff = $data->orderBy('first_name','ASC')->get();
                return view('user.salary.assign',['data'=>$allStaff,'search'=>$search]);
            } 
    
            public function generateSalarySlip(Request $request){
                $this->current_attendance_status_id();
                $users_id = !empty($request->user_id) ? $request->user_id : '1' ;
                $data = User::select('users.*','staff.joining_date as staffJoining_date','salary.total_amount as salary','salary.basic_amt as basicPer','salary.da as daPer','salary.pf as pfPer','salary.tds as tdsPer')
                ->leftjoin('teachers as staff','staff.id','users.teacher_id')
                ->leftjoin('staff_salarys as salary','salary.staff_id','users.id')
                ->where('users.id',$users_id)->first();
                $load = "first_load";
                if($request->user_id != ''){
                    $load = 'second_load';
                }
                $dataUsers = User::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))
                ->where('role_id',$request->role_id)->orderBy('id','DESC')->get();
                $monthId = $request->monthId;
                return view('user.salary.add',['first_load'=>$load,'data'=>$data,'monthId'=>$monthId,'dataUsers'=>$dataUsers]);
            }     
    
            public function findStaff(Request $request){
                $data = array();
                if($request->isMethod('post')){
                    $data = User::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                    ->where('role_id',$request->role_id)->orderBy('id','DESC')->get();
                    $userData ='<option value="">Select</option>';
                    foreach($data as $user){
                        $userData.='
                        <option value="'.$user['id'].'">'.$user['first_name'].'&nbsp;'.$user['last_name'].'</option>';
                    }
                    echo $userData;
                }
            }     

            public function generateSalary(Request $request){
                if($request->isMethod('post')){
                    // dd($request);
                    $request->validate([
                        'first_name' => 'required',
                      'basic_amt' => 'required',
                      'date' => 'required',
                    ]);
                    $old_data = StaffSalaryDetail::where('staff_id',$request->staff_id)->where('month_id',$request->month_id)->first();
                    if(empty($old_data)){
         $detail = new StaffSalaryDetail;//model name
   }else{
       $detail = $old_data;

  }

           
          /*  $add->user_id = Session::get('id');
            $add->session_id = Session::get('session_id');
            $add->branch_id = Session::get('branch_id');   
            $add->staff_id = $request->staff_id;   
            $add->first_name = $request->first_name;              
            $add->last_name = $request->last_name;                
            $add->month_id = $request->month_id;  
            $add->basic_amt = $request->basic_amt;
            $add->total_amount = $request->total_amount;   
            $add->date = $request->date;   
            $add->save();
            $salaryId = $add->id;*/
             
          
            $detail->user_id = Session::get('id');
            $detail->session_id = Session::get('session_id');
            $detail->branch_id = Session::get('branch_id'); 
           // $detail->salary_id = $salaryId;
            $detail->staff_id = $request->staff_id;
            $detail->name = $request->first_name;              
            $detail->last_name = $request->last_name;              
            $detail->month_id = $request->month_id;   
             $detail->basic_amt = $request->basic_amt;
            $detail->gross_salary = $request->total_amount;              
            $detail->salary = $request->total_salary;              
            $detail->per_day_amt = $request->per_day_amt;              
            $detail->salary_day = $request->salaryDays;              
          //  $detail->hra = $request->hra;              
            $detail->da = $request->da;              
            $detail->incentive = $request->incentive;              
           // $detail->allowance = $request->allowance;              
           // $detail->advance = $request->advance;              
            $detail->pf = $request->pf;              
            $detail->tds = $request->tds;              
            $detail->present= $request->salaryDays;              
            $detail->absent= $request->absent;              
            $detail->total_days= $request->totalDays;              
          //  $detail->esic = $request->esic;              
          //  $detail->tax = $request->tax;              
            $detail->other_deduction = $request->other_deduction;              
            $detail->deduction_remark = $request->deduction_remark;              
            $detail->holiday = $request->holiday;              
            $detail->half_day = $request->halfDay;              
            $detail->double_shift = $request->doubleShift;              
            $detail->date = $request->date;              
            $detail->lateCome = $request->lateCome;              
            $detail->lateComeFrequency = $request->lateComeFrequency;   
            


            $detail->save(); 
              $RoleId = $request->roleId;
               $MonthId = $request->month_id;
            /*
           

            $data = array();
                $data = User::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->where('role_id',$RoleId)->orderBy('id','DESC')->get();
              
                $userData ='<option value="">Select</option>';
                foreach($data as $user){
                $userData.='
                <option value="'.$user['id'].'">'.$user['first_name'].'&nbsp;'.$user['last_name'].'</option>';
                }
                
                
                
                $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                            ->where('message_types.status',1)->where('message_types.slug','salarygenerate')->first();
                
                $branch = Branch::find(Session::get('branch_id'));
                $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();           
                $studentdetail = User::where('id',$request->staff_id)->get()->first();
                $monthName = date('F',strtotime($request->month_id));
                $currentYear = Carbon::now()->year;
                
                    $arrey1 =   array(
                                   '{#employee_name#}',
                                   '{#this_month#}',
                                   '{#this_year#}',
                                   '{#this_month_salery#}',
                                   '{#support_no#}',
                                   '{#school_name#}',
                                   '{#net_selery#}');
                       
                    $arrey2 = array(
                                    $request->first_name.' '.$request->last_name,
                                    $monthName,
                                    $currentYear,
                                    $request->total_amount,
                                    $setting->mobile,
                                    $setting->name,
                                    $request->basic_amt);
                    
                    if($template->status != 1){
                            if($studentdetail->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $studentdetail->email, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($studentdetail->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($studentdetail->mobile,$whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($studentdetail->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($studentdetail->mobile, $sms);
                                    }
                                }
                            }    
                    }
                    
                  
                  
                    
                    $url = '/salary_print/'.$request->staff_id.'/'.$request->month_id;
                   
                    ?>
                        <script type="text/javascript">
                            window.open("<?=$url?>", "_blank");
                        </script>
                    <?php
                    */

                // return redirect::to('generate/salary/slip')->with( ['RoleId' => $RoleId,'MonthId' => $MonthId,'userData' => $userData] )->with('message','Salary Generated Successfully.');
                return response()->json(['status' => 'success','message' => ' Generate Successfully.',]);
           
            
        }
}
    
    
    public function salaryPrint($id,$month_id){
        
     
    
 
        $salarydata = StaffSalaryDetail::select('staff_salary_details.*','staff.joining_date as staffJoining_date','staff.medical_leave as staffMedical_leave','staff.designation as staffDesignation','staff.address as staffAddress','staff.pan_no as staffpan_no','Role.name as roleName','User.id as employeeId','User.mobile','staff.gender_id','staff.bank_name as b_name','staff.account_no as acc_no','User.image as photo')
                                    // ->leftjoin('salary_documents as salaryDocument','salaryDocument.teacher_id','staff_salary_details.staff_id')
                                    ->leftjoin('users as User','User.id','staff_salary_details.staff_id')
                                    ->leftjoin('role as Role','Role.id','User.role_id')
                                    ->leftjoin('teachers as staff','staff.id','User.teacher_id');
                      
        
        $data = $salarydata->where('staff_salary_details.staff_id',$id)->where('staff_salary_details.month_id',$month_id)->get()->first();
        
                   $url =    Helper::printPreview('Salary Slip');
                   //dd($url);
                  
    return view($url,['data'=>$data]);
    }      

    public function downloadSalarySlip($id,$month_id){
        
       $salarydata = StaffSalaryDetail::select('staff_salary_details.*','staff.joining_date as staffJoining_date','staff.medical_leave as staffMedical_leave','staff.designation as staffDesignation','staff.address as staffAddress','staff.pan_no as staffpan_no','Role.name as roleName','User.id as employeeId','User.mobile','staff.gender_id','staff.bank_name as b_name','staff.account_no as acc_no','User.image as photo')
                                    // ->leftjoin('salary_documents as salaryDocument','salaryDocument.teacher_id','staff_salary_details.staff_id')
                                    ->leftjoin('users as User','User.id','staff_salary_details.staff_id')
                                    ->leftjoin('role as Role','Role.id','User.role_id')
                                    ->leftjoin('teachers as staff','staff.id','User.teacher_id');
        
        $data = $salarydata->where('staff_salary_details.staff_id',$id)->where('staff_salary_details.month_id',$month_id)->get()->first();
        
        return view('master.printFilePanel.UserManagement.template01',['data'=>$data]);
    }  
    public function current_attendance_status_id(){
           $attendance = TeacherAttendance::whereMonth('date',date('m'))->where('attendance_status_id',1)->whereNull('current_attendance_status_id')->orderBY('attendance_status_id','asc')->get();
           
           foreach($attendance as $item){
               
            $oldData = TeacherAttendance::whereDate('date',$item['date'])->where('attendance_status_id',2)->where('staff_id',$item['teacher_id'])->first();
          if(!empty($oldData)){
            $start = strtotime($item['time']);
            $end = strtotime($oldData['time']);
            $mins = ($end - $start) / 60;
            $hours = $mins/60;
           if($oldData['id'] == 11){
               
           }
           
            if($hours >= 3 && $hours <= 7){
            TeacherAttendance::where('date',date("Y-m-d", strtotime($item['date'])))->where('teacher_id',$item['teacher_id'])->update(['current_attendance_status_id'=>4]);
           }elseif($hours <= 2.59){
              
            TeacherAttendance::where('date',date("Y-m-d", strtotime($item['date'])))->where('teacher_id',$item['teacher_id'])->update(['current_attendance_status_id'=>3]);
           }elseif($hours >= 7.01){
                 
            TeacherAttendance::where('date',date("Y-m-d", strtotime($item['date'])))->where('teacher_id',$item['teacher_id'])->update(['current_attendance_status_id'=>6]);
           }
          }else{
             
          }
           
    }  
 

    }
    
}
