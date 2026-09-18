<?php

namespace App\Http\Controllers\library;

use Illuminate\Validation\Validator;
use App\Models\StudentFees;
use App\Models\Admission;
use App\Models\library\LibraryCabin;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryAssign;
use App\Models\Account;
use App\Models\Setting;
use App\Models\Master\Branch;
use App\Models\FeesDetail;
use App\Models\BillCounter;
use App\Models\Invoice;
use App\Models\library\Library;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryFeesController extends Controller{

    public function library_due_amount(Request $request){
        $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                            ->leftjoin('library_assign as library_assign','library_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                            ->where('library_assign.session_id',Session::get('session_id'))->where('library_assign.branch_id',Session::get('branch_id'))
                            ->where('invoices.due_amount', '>' ,0)
                            ->GroupBy('invoices.id')->where('invoices.invoice_type',0)->get();
        return  view('library/fees/library_due_amount', ['data' => $data]);
    }
    
    public function library_due_amount_pay(Request $request,$id){
        $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                            ->leftjoin('library_assign as library_assign','library_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                            ->where('invoices.id',$id)->where('invoices.invoice_type',0)->first();
        if ($request->isMethod('post')) {
            
                        $pay = Invoice::find($id);
                        $pay->paid_amount = $pay->paid_amount+$request->amount;
                        $pay->due_amount = $request->duesAmount;
                        $pay->discount = $pay->discount+$request->discountAmount;
                        $pay->save();
                        $library_invoice_id = $pay->id;

                        $FeesDetail = new FeesDetail; //model name
                        $FeesDetail->user_id = Session::get('id');
                        $FeesDetail->session_id = Session::get('session_id');
                        $FeesDetail->branch_id = Session::get('branch_id');
                        $FeesDetail->fees_counter_id = 1;
                        $FeesDetail->receipt_no = $data->receipt_no;
                        $FeesDetail->admission_id = $data->admission_id;
                        $FeesDetail->fees_collect_id = $data->fees_collect_id;
                        $FeesDetail->invoice_id = $library_invoice_id;
                        $FeesDetail->discount = $request->discountAmount;
                        $FeesDetail->discount_type = $request->discountType;
                        $FeesDetail->discount_value = $request->discountValue;
                        $FeesDetail->paid_amount = $request->amount;
                        $FeesDetail->total_amount = $request->library_amount;
                        $FeesDetail->payment_mode_id = $request->payment_mode_id;
                        $FeesDetail->fees_type = 2;
                        $FeesDetail->status = 0;
                        $FeesDetail->date = date('Y-m-d');
                        $FeesDetail->save();
                        
    	$template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','fees-collect')->first();
        
       $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first(); 
    
                    $arrey1 =   array(
                                    '{#name#}',
                                    '{#fees#}',
                                    '{#discount#}',
                                     '{#duesAmount#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $data->first_name." ".$data->last_name,
                                    $request->amount,
                                    $request->discountAmount,
                                    $request->duesAmount,
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
                                if ($data->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($data->mobile, $whatsapp);
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
            
                         return redirect::to('library_due_amount')->with('message', 'Payment Collected Successfully.');

        }
        
        return  view('library/fees/due_amount_pay', ['data' => $data]);
    }
    
    public function invoice(Request $request,$invoice_no,$admission_id){
        $data =  Invoice::select('invoices.*','admissions.first_name as first_name','library_lockers.name as locker_name','admissions.mobile as mobile','admissions.admissionNo','payment_modes.name as payment_mode_name'
                            ,'fees_detail.discount as per_discount','fees_detail.paid_amount as per_paid_amount','fees_detail.date as bill_date') 
                                ->leftjoin('library_assign as library_assign','library_assign.admission_id','invoices.admission_id')
                                ->leftjoin('library_lockers','library_lockers.id','invoices.library_locker_id')
                                ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                                 ->leftjoin('fees_detail','fees_detail.invoice_id','invoices.id')
                                ->leftjoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                                ->where('invoices.invoice_no',$invoice_no)
                                ->where('invoices.admission_id',$admission_id)
                                ->where('invoices.invoice_type',0)->get();
      
        return view('library/fees/invoice',['data' => $data]);
    }
    
    
    public function due_amount_reminder(Request $request){
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first();
        $student = Admission::where('id',$request->admission_id)->where('status',1)->first();
        

            if($student->email != ""){
                if($branch->email_srvc != 0){
                    $message = $request->reminder_message;
                    $emailData = ['email' => $student->email, 'data' => $message, 'subject' => "Due Fees Reminder"];
                    Helper::sendMail('email_print.template_print', $emailData);
                } 
            }
        
            if($branch->whatsapp_srvc != 0){
                if ($student->mobile != ""){
                        $whatsapp = $request->reminder_message;
                        Helper::sendWhatsappMessage($student->mobile,$whatsapp);
                }
            }
            
            if($branch->sms_srvc != 0){
                if($student->mobile != ""){
                    $sms = $request->reminder_message;
                    Helper::SendMessage($student->mobile, $sms);
                }
            }    
            
            return redirect('library_due_amount')->with('message','Reminder Send Successfully');
    }
    
    
    public function viewFees(Request $request)
    {
        
        
        
        $serach['name'] = $request->name;
        $serach['starting'] = $request->starting;
        $serach['ending'] =$request->ending ;

       $data =  FeesDetail::select('fees_detail.*','admissions.first_name as first_name','admissions.father_name as f_name')
      ->leftjoin('library_assign as library_assign','library_assign.admission_id','fees_detail.admission_id')
      ->leftjoin('admissions','admissions.id','library_assign.admission_id')
      ->where('library_assign.session_id',Session::get('session_id'))->where('library_assign.branch_id',Session::get('branch_id'));
       if ($request->isMethod('post')) {
       if (!empty($request->name)) {

                    $data = $data->where('admissions.first_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.father_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.mobile', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.email', 'LIKE', '%' . $request->name . '%');
                }
                
                if(!empty($request->starting)) {
                    $data = $data->whereBetween('fees_detail.date',[$request->starting,$request->ending]);
                }
        }else{
               $data = $data->whereMonth('fees_detail.date', '=', date('n'))
                             ->whereYear('fees_detail.date', '=', date('Y'));
        
        }
        $fees = $data->where('fees_detail.fees_type',2)->orderBy('id', 'DESC')->get();
        
        return view('library.fees.index', ['fees' => $fees,'serach' => $serach]);
    }
    
   public function feesStudentReport(Request $request){
    $search['library_assign_id'] = $request->library_assign_id;
    $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                        ->leftjoin('library_assign as library_assign','library_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                            ->where('library_assign.session_id',Session::get('session_id'))->where('library_assign.branch_id',Session::get('branch_id'))
                            ->GroupBy('invoices.id')->where('invoices.invoice_type',0);
    

        if ($request->isMethod('post')) {
           
            if (!empty($request->library_assign_id)) {
                $data = $data->where('library_assign.id',$request->library_assign_id);
            } 
        }else{
            $data = $data->whereMonth('invoices.created_at', '=', date('n'))
                             ->whereYear('invoices.created_at', '=', date('Y'));
        }
           $data = $data->orderBy('invoices.id')->get();
           
        $all_stu = LibraryAssign::select('library_assign.*','admissions.first_name','admissions.admissionNo')
                                ->leftJoin('admissions','admissions.id','library_assign.admission_id')
                                ->where('admissions.status',1)
                                ->where('library_assign.status',1)
                                ->get();
                                
    return view('library.fees.student_report', ['data' => $data,'search'=>$search,'all_student' => $all_stu,]);
}





    



	

    public function libraryFeesPrint($id){
        
        $data = FeesDetail::select('fees_detail.*','admissions.first_name','admissions.father_name','admissions.mobile','admissions.address','librarys.name as library_name','library_cabins.name as cabin_name','library_assign.library_time')
        ->leftjoin('library_assign','library_assign.admission_id','fees_detail.admission_id')
        ->leftjoin('librarys','librarys.id','library_assign.library_id')
        ->leftjoin('library_cabins','library_cabins.id','library_assign.cabin_id')
        ->leftjoin('admissions','admissions.id','library_assign.admission_id')
        ->with('PaymentMode')->with('Month')->find($id);
        
        return view('print_file.library.library_print_fees', ['data' => $data]);
    }

}
