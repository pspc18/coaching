<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admin;
use App\Models\Account;
use App\Models\Admission;
use App\Models\Admit;
use App\Models\AdmitCardNote;
use App\Models\exam\AssignExam;
use App\Models\exam\Exam;
use App\Models\exam\AssignQuestion;
use App\Models\exam\ExamResult;
use App\Models\exam\ExamResultDetail;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\Question;
use App\Models\examoffline\AssignOfflineExam;
use App\Models\examoffline\ExamOffline;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesCounter;
use App\Models\fees\FeesAssignDetail;
use App\Models\hostel\ElectricityBillPayment;
use App\Models\hostel\FoodMenuList;
use App\Models\hostel\Head;
use App\Models\hostel\Hostel;
use App\Models\hostel\HostelAssign;
use App\Models\hostel\HostelBed;
use App\Models\hostel\HostelBuilding;
use App\Models\hostel\HostelExpences;
use App\Models\hostel\HostelFloor;
use App\Models\hostel\HostelMeterUnit;
use App\Models\hostel\HostelRoom;
use App\Models\hostel\MessFeesStrucher;
use App\Models\hostel\MessFoodCategory;
use App\Models\hostel\MessFoodRoutine;
use App\Models\hostel\SecurityDeposit;
use App\Models\hostel\StudentExpense;
use App\Models\hostel\StudentExpenseDetail;
use App\Models\library\AssignBook;
use App\Models\library\BookInvoice;
use App\Models\library\Library;
use App\Models\library\LibraryAssign;
use App\Models\library\LibraryBook;
use App\Models\library\LibraryCabin;
use App\Models\library\LibraryCategory;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryPlan;
use App\Models\library\LibraryTimeSlot;
use App\Models\library\RetrunBook;
use App\Models\Setting;
use App\Models\Master\Branch;
use App\Models\Master\Complaint;
use App\Models\Master\BooksUniformShop;
use App\Models\Master\Bus;
use App\Models\Master\BusAssign;
use App\Models\Master\BusRoute;
use App\Models\Master\BusRouteAssign;
use App\Models\Master\EmailRecords;
use App\Models\Master\EmailTamplate;
use App\Models\Master\EnquiryStatus;
use App\Models\Master\EventManagement;
use App\Models\Master\Gallery;
use App\Models\Master\GatePass;
use App\Models\Master\Holidays;
use App\Models\Master\Homework;
use App\Models\Master\HomeworkDocuments;
use App\Models\Master\HomeworkReview;
use App\Models\Master\HourlyHomework;
use App\Models\Master\InvantoryBooking;
use App\Models\Master\InvantoryDresh;
use App\Models\Master\LeaveManagement;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\NoticeBoard;
use App\Models\Master\Penalty;
use App\Models\Master\Prayer;
use App\Models\Master\RecycleBin;
use App\Models\Master\RegistrationTerms;
use App\Models\Master\Sport;
use App\Models\Master\Sports;
use App\Models\Master\Stork;
use App\Models\Master\TeacherSubject;
use App\Models\Master\Time_Table;
use App\Models\Master\TimePeriods;
use App\Models\Master\Uniform;
use App\Models\Master\UploadHomework;
use App\Models\Master\Sessions;
use App\Models\BillCounter;
use App\Models\PermissionManagement;
use App\Models\ClassType;
use App\Models\DownloadCenter;
use App\Models\EventeCertificate;
use App\Models\ExaminationAdmitCard;
use App\Models\ExaminationSchedule;
use App\Models\ExaminationScheduleDetail;
use App\Models\Invantory;
use App\Models\InvantoryItem;
use App\Models\getExamType;
use App\Models\FeesCollect;
use App\Models\FeesDetail;
use App\Models\SchoolCalender;
use App\Models\SportCertificate;
use App\Models\StudentMarksDetails;
use App\Models\StudentsMarks;
use App\Models\StaffSalary;
use App\Models\StaffSalaryDetail;
use App\Models\StudentAttendance;
use App\Models\StudentAction;
use App\Models\TcCertificate;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\TeacherDocuments;
use App\Models\TeachersAccounts;
use App\Models\ToDoList;
use App\Models\TotalDays;
use App\Models\Subject;
use App\Models\Remark;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Enquiry;
use App\Models\CcForm;
use App\Models\Chat;
use App\Models\City;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class BranchController extends Controller

{
    public function deleteBranch(Request $request){
        
        AssignBook::where('branch_id',$request->delete_id)->delete();
        Account::where('branch_id',$request->delete_id)->delete();
        Admission::where('branch_id',$request->delete_id)->delete();
        Admit::where('branch_id',$request->delete_id)->delete();
        AssignExam::where('branch_id',$request->delete_id)->delete();
        PermissionManagement::where('branch_id',$request->delete_id)->delete();
        BillCounter::where('branch_id',$request->delete_id)->delete();
        User::where('branch_id',$request->delete_id)->delete();
        Sessions::where('branch_id',$request->delete_id)->delete();
        Setting::where('branch_id',$request->delete_id)->delete();
        Complaint::where('branch_id',$request->delete_id)->delete();
        CcForm::where('branch_id',$request->delete_id)->delete();
        Chat::where('branch_id',$request->delete_id)->delete();
        DownloadCenter::where('branch_id',$request->delete_id)->delete();
        Enquiry::where('branch_id',$request->delete_id)->delete();
        EventeCertificate::where('branch_id',$request->delete_id)->delete();
        // ExaminationAdmitCard::where('branch_id',$request->delete_id)->delete();
        // ExaminationSchedule::where('branch_id',$request->delete_id)->delete();
        // ExaminationScheduleDetail::where('branch_id',$request->delete_id)->delete();
        Expense::where('branch_id',$request->delete_id)->delete();
        FeesCollect::where('branch_id',$request->delete_id)->delete();
        FeesDetail::where('branch_id',$request->delete_id)->delete();
        // getExamType::where('branch_id',$request->delete_id)->delete();
       // Invantory::where('branch_id',$request->delete_id)->delete();
       // InvantoryItem::where('branch_id',$request->delete_id)->delete();
        Invoice::where('branch_id',$request->delete_id)->delete();
        Remark::where('branch_id',$request->delete_id)->delete();
        SchoolCalender::where('branch_id',$request->delete_id)->delete();
        SportCertificate::where('branch_id',$request->delete_id)->delete();
        StaffSalary::where('branch_id',$request->delete_id)->delete();
        StaffSalaryDetail::where('branch_id',$request->delete_id)->delete();
        StudentAction::where('branch_id',$request->delete_id)->delete();
        StudentAttendance::where('branch_id',$request->delete_id)->delete();
        // StudentMarksDetails::where('branch_id',$request->delete_id)->delete();
        StudentsMarks::where('branch_id',$request->delete_id)->delete();
        Subject::where('branch_id',$request->delete_id)->delete();
        TcCertificate::where('branch_id',$request->delete_id)->delete();
        Teacher::where('branch_id',$request->delete_id)->delete();
        TeacherAttendance::where('branch_id',$request->delete_id)->delete();
        TeacherDocuments::where('branch_id',$request->delete_id)->delete();
        TeachersAccounts::where('branch_id',$request->delete_id)->delete();
        ToDoList::where('branch_id',$request->delete_id)->delete();
        // TotalDays::where('branch_id',$request->delete_id)->delete();
        AssignQuestion::where('branch_id',$request->delete_id)->delete();
        Exam::where('branch_id',$request->delete_id)->delete();
        ExamResult::where('branch_id',$request->delete_id)->delete();
        ExamResultDetail::where('branch_id',$request->delete_id)->delete();
        FillMarks::where('branch_id',$request->delete_id)->delete();
        FillMinMaxMarks::where('branch_id',$request->delete_id)->delete();
        Question::where('branch_id',$request->delete_id)->delete();
       // AssignOfflineExam::where('branch_id',$request->delete_id)->delete();
       // ExamOffline::where('branch_id',$request->delete_id)->delete();
        FeesAssign::where('branch_id',$request->delete_id)->delete();
        FeesAssignDetail::where('branch_id',$request->delete_id)->delete();
        ElectricityBillPayment::where('branch_id',$request->delete_id)->delete();
        FoodMenuList::where('branch_id',$request->delete_id)->delete();
        Head::where('branch_id',$request->delete_id)->delete();
        Hostel::where('branch_id',$request->delete_id)->delete();
        HostelAssign::where('branch_id',$request->delete_id)->delete();
        HostelBed::where('branch_id',$request->delete_id)->delete();
        HostelBuilding::where('branch_id',$request->delete_id)->delete();
        HostelExpences::where('branch_id',$request->delete_id)->delete();
        HostelFloor::where('branch_id',$request->delete_id)->delete();
        HostelMeterUnit::where('branch_id',$request->delete_id)->delete();
        HostelRoom::where('branch_id',$request->delete_id)->delete();
        MessFeesStrucher::where('branch_id',$request->delete_id)->delete();
        MessFoodCategory::where('branch_id',$request->delete_id)->delete();
        MessFoodRoutine::where('branch_id',$request->delete_id)->delete();
        SecurityDeposit::where('branch_id',$request->delete_id)->delete();
        StudentExpense::where('branch_id',$request->delete_id)->delete();
        StudentExpenseDetail::where('branch_id',$request->delete_id)->delete();
        BookInvoice::where('branch_id',$request->delete_id)->delete();
        Library::where('branch_id',$request->delete_id)->delete();
        LibraryAssign::where('branch_id',$request->delete_id)->delete();
        LibraryBook::where('branch_id',$request->delete_id)->delete();
        LibraryCabin::where('branch_id',$request->delete_id)->delete();
        LibraryCategory::where('branch_id',$request->delete_id)->delete();
        LibraryLocker::where('branch_id',$request->delete_id)->delete();
        LibraryPlan::where('branch_id',$request->delete_id)->delete();
        LibraryTimeSlot::where('branch_id',$request->delete_id)->delete();
        // RetrunBook::where('branch_id',$request->delete_id)->delete();
        BooksUniformShop::where('branch_id',$request->delete_id)->delete();
        Bus::where('branch_id',$request->delete_id)->delete();
        BusAssign::where('branch_id',$request->delete_id)->delete();
        BusRoute::where('branch_id',$request->delete_id)->delete();
        EventManagement::where('branch_id',$request->delete_id)->delete();
        Gallery::where('branch_id',$request->delete_id)->delete();
        GatePass::where('branch_id',$request->delete_id)->delete();
        Holidays::where('branch_id',$request->delete_id)->delete();
        Homework::where('branch_id',$request->delete_id)->delete();
        HomeworkDocuments::where('branch_id',$request->delete_id)->delete();
        HomeworkReview::where('branch_id',$request->delete_id)->delete();
        HourlyHomework::where('branch_id',$request->delete_id)->delete();
        // InvantoryBooking::where('branch_id',$request->delete_id)->delete();
        // InvantoryDresh::where('branch_id',$request->delete_id)->delete();
        LeaveManagement::where('branch_id',$request->delete_id)->delete();
        NoticeBoard::where('branch_id',$request->delete_id)->delete();
        Penalty::where('branch_id',$request->delete_id)->delete();
        Prayer::where('branch_id',$request->delete_id)->delete();
        RecycleBin::where('branch_id',$request->delete_id)->delete();
        RegistrationTerms::where('branch_id',$request->delete_id)->delete();
        Sport::where('branch_id',$request->delete_id)->delete();
        Sports::where('branch_id',$request->delete_id)->delete();
        Stork::where('branch_id',$request->delete_id)->delete();
        TeacherSubject::where('branch_id',$request->delete_id)->delete();
        Time_Table::where('branch_id',$request->delete_id)->delete();
        TimePeriods::where('branch_id',$request->delete_id)->delete();
        Uniform::where('branch_id',$request->delete_id)->delete();
        UploadHomework::where('branch_id',$request->delete_id)->delete();

        Branch::find($request->delete_id)->delete();
         return redirect::to('viewBranch')->with('message', 'Branch Delete Successfully.');
    }
    

    
    public function addBranch(Request $request){
        
        if($request->isMethod('post')){
           
            $request->validate([
                 'branch_code'  => 'required',
                 'branch_name'  => 'required',
                 'mobile'  => 'required',
                 'email'  => 'required',
         	]);
            $branch_sidebar_id = Branch::find(Session::get('branch_id'));

            $add_branch = new Branch;//model name
            $add_branch->user_id = Session::get('id');
            $add_branch->session_id = Session::get('session_id');
    		$add_branch->branch_code =$request->branch_code;
    		$add_branch->branch_name = $request->branch_name;
    		$add_branch->contact_person  = $request->contact_person;
    		$add_branch->mobile  = $request->mobile;
    		$add_branch->email = $request->email;
    		$add_branch->address = $request->address;
    		$add_branch->country_id = $request->country_id;
    		$add_branch->city_id = $request->city_id;
    		$add_branch->state_id = $request->state_id;
    		$add_branch->pin_code = $request->pin_code;
    		$add_branch->branch_sidebar_id = $branch_sidebar_id->branch_sidebar_id;
    		$add_branch->sidebar_sub_id = $branch_sidebar_id->sidebar_sub_id;
            $add_branch->save();
            
             $data = BillCounter::where('branch_id',Session::get('branch_id'))->get();

            foreach($data as $val){
            $bill = new BillCounter;
            $bill->user_id = Session::get('id');
            $bill->branch_id = $add_branch->id;
            $bill->session_id = Session::get('session_id');
            $bill->type = $val->type;
            $bill->counter = 0;
            $bill->save();
        }
                  $add_setting = new Setting;//model name
            		$add_setting->user_id = Session::get('id'); 
            		$add_setting->session_id = Session::get('session_id');
            		$add_setting->role_id = Session::get('role_id');
                    $add_setting->branch_id = $add_branch->id;
            		$add_setting->name = $request->branch_name;
            		$add_setting->mobile  = $request->mobile;
            		$add_setting->gmail = $request->email;
            		$add_setting->country_id = $request->country_id;
            		$add_setting->state_id = $request->state_id;
            		$add_setting->city_id = $request->city_id;
            		$add_setting->pincode = $request->pin_code;
            		$add_setting->address  = $request->address;
            	    $add_setting->save();
        return response()->json(['status' => 'success','message' => 'Branch Added Successfully.',]);
        }    
        
        return view('master.branch.addBranch');
    }
    
     public function viewBranch(Request $request){
         
        $branch =  Branch::select('branch.*','states.name as state','citys.name as city')
        	->leftjoin('states','states.id','branch.state_id')
        	->leftjoin('citys','citys.id','branch.city_id')->orderBy('id', 'DESC')->get();
        return view('master.branch.viewBranch',['data'=>$branch]);
    }
    
    
    
    public function editBranch(Request $request,$id){
       
        $data = Branch::find($id);
        
        if($request->isMethod('post')){
            $request->validate([
             'branch_code'  => 'required',
             'branch_name'  => 'required',
             'mobile'  => 'required',
             'email'  => 'required',
         	]);
      
        
        $data->user_id = Session::get('id');
        $data->session_id = Session::get('session_id');
		$data->branch_code =$request->branch_code;
		$data->branch_name = $request->branch_name;
		$data->contact_person  = $request->contact_person;
		$data->mobile  = $request->mobile;
		$data->email = $request->email;
		$data->address = $request->address;
		$data->country_id = $request->country_id;
		$data->city_id = $request->city_id;
		$data->state_id = $request->state_id;
		$data->pin_code = $request->pin_code;
		$data->expert_name = $request->expert_name;
		$data->trial_period = $request->trial_period;
		$data->access_token = $request->access_token;
		$data->instance_id = $request->instance_id;
		$data->api_link = $request->api_link;
        $data->save();
        
            return response()->json(['status' => 'success', 'message' => 'Branch Updated Successfully.','redirect' => url('viewBranch')]);
        }
               $getcitie = City::where('state_id',$data['state_id'])->get();

       return view('master.branch.editBranch',['data'=>$data,'getCity'=>$getcitie]);  
    }
    
     public function changeBranch(Request $request)
    {
        
    if(!empty($request->branch_id)){
        session()->put('admin_branch_id', $request->branch_id);
        session()->put('branch_id', $request->branch_id);
    }else{
        session()->put('admin_branch_id',null);
    }
        return redirect()->back();
    }
    
   
    
    
}



















































