<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\StudentFees;
use App\Models\Admission;
use App\Models\hostel\HostelAssign;
use App\Models\hostel\HostelFeesDetail;
use App\Models\hostel\SecurityDeposit;
use App\Models\hostel\ElectricityBillPayment;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\PaymentMode;
use App\Models\Master\Branch;
use App\Models\Account;
use App\Models\FeesStructure;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\FeesDetail;
use App\Models\hostel\HostelMeterUnit;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use Session;
use DateTime;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Paytm;
class HostelFeesController extends Controller{


    public function collectFees(Request $request){
        
        $serach['student_details'] = $request->student_details;

        $dataAll =  HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.last_name','admissions.admissionNo')
            ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
            ->leftjoin('class_types','class_types.id','admissions.class_type_id')
            ->leftjoin('hostel_room','hostel_room.id','hostel_assign.room_id')->where('hostel_assign.session_id',Session::get('session_id'))->where('hostel_assign.branch_id',Session::get('branch_id'))->where('hostel_assign.bed_status',1)->orderBy('hostel_assign.id','DESC')->get();
       
        $BillCounter = BillCounter::where('type','HostelFees')->first();
             $bill = $BillCounter->counter + 1;
        
        $data = '';
        $invoice = '';
        if ($request->isMethod('post')) {
            $data = HostelAssign::select('hostel_assign.*','hostel.name as hostel_name','admissions.admissionNo','class_types.name as class_name','hostel_room.name as room_name','hostel_room.room_category','admissions.first_name','admissions.last_name','admissions.father_name','admissions.email','admissions.mobile')
                    ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                    ->leftjoin('hostel','hostel.id','hostel_assign.hostel_id')
                    ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                    ->leftjoin('hostel_room','hostel_room.id','hostel_assign.room_id');
            
            if (!empty($request->student_details)) {
            
                $data = $data->where("hostel_assign.admission_id", $request->student_details)->get()->first();
            
                $invoice = Invoice::select('invoices.*','admissions.first_name','admissions.father_name','admissions.last_name','admissions.admissionNo')
                                    ->leftjoin('admissions','admissions.id','invoices.admission_id')
                                    ->where('invoices.admission_id',$request->student_details)
                                    ->where('invoices.invoice_type',1)->get();
            }
  
        }
        
        return  view('hostel/fees/collect', ['serach' => $serach,'allstudents'=>$dataAll,'data'=>$data,'feesDetail'=>$invoice,'BillCounter'=>$bill]);
    }

   public function hostelElectricityPaySubmit(Request $request){
       
          if ($request->isMethod('post')) {
        
        
           
                for($key =0; $key < count($request->month_id); $key++)
                {
                    
                    $old = ElectricityBillPayment::where("hostel_assign_id",$request->hostel_assign_id[$key])->where('month_id',$request->month_id[$key])->count();
                    
                          if($old == 0)
              {
                
           if(!empty($request->total_monthly_unit[$key]))
        {
                $hostelpayDetail = new ElectricityBillPayment; //model name
                $hostelpayDetail->user_id = Session::get('id');
                $hostelpayDetail->session_id = Session::get('session_id');
                $hostelpayDetail->branch_id = Session::get('branch_id');
                $hostelpayDetail->total_days_id = $request->total_days_id[$key];
                $hostelpayDetail->hostel_assign_id = $request->hostel_assign_id[$key];
                $hostelpayDetail->total_days = $request->total_days[$key];
                $hostelpayDetail->status = 0;
                
                $hostelpayDetail->per_unit_rate = $request->per_unit_rate[$key] ;
                $hostelpayDetail->total_monthly_unit = $request->total_monthly_unit[$key] ;
                $hostelpayDetail->month_id = $request->month_id[$key];
                $hostelpayDetail->monthly_consumption_uni = $request->monthly_consumption_uni[$key];
                $hostelpayDetail->payment_mode_id = $request->payment_mode_id;
                $hostelpayDetail->total_amount = $request->total_amount[$key];
        
                $hostelpayDetail->save();
             
        }              
          
        
                      }
            
        
                }
                   return redirect::to('electricity_bill_payment_add' )->with('message', 'Collected For This Month.');   
            }
            
              
            
              
        
          
          
     
   }
   
public function hostelElectricityPayView(Request $request,$id){
                     
                          $data =  ElectricityBillPayment::select('electricity_bill_payments.*',
                          'admissions.admissionNo',
                          'admissions.first_name',
                          'admissions.last_name',
                          'admissions.father_name',
                          'admissions.mother_name',
                          'admissions.mobile',
                          'admissions.image as student_img'
                          )
            ->leftjoin('admissions','admissions.id','electricity_bill_payments.admission_id')->
            where('electricity_bill_payments.id', $id)->first();
                      
                   return view('hostel.fees.electricityBillPayment.electricityPay',['data'=>$data]);
 
   }
   

        public function hostelPaySubmit(Request $request){

        if ($request->isMethod('post')) {
            
            
            //  $BillCounter = BillCounter::where('type','HostelFees')->where('branch_id',Session::get('branch_id'))->first();
             $BillCounter = BillCounter::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('type','HostelFees')->first();
            
            
              if (!empty($BillCounter)) {
            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounterNo = $counter;
              }
            
            $hostel_assign = HostelAssign::where('id',$request->hostel_assign_id)->update(['hostel_renewal_date' => $request->hostel_renewal_date]);
            
            $hostel_pay = new Invoice;
            $hostel_pay->user_id = Session::get('id');
            $hostel_pay->branch_id = Session::get('branch_id');
            $hostel_pay->session_id = Session::get('session_id');
            $hostel_pay->invoice_type = 1;
            $hostel_pay->invoice_no = date('d').random_int(1000, 9999);
            $hostel_pay->hostel_assign_id = $request->hostel_assign_id;
            $hostel_pay->hostel_renewal_date = $request->hostel_renewal_date;
            $hostel_pay->admission_id = $request->admission_id;
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
            // $FeesDetail->receipt_no = $request->editable_receipt_no;
             $FeesDetail->receipt_no = $BillCounterNo;
            $FeesDetail->fees_counter_id = Session::get('counter_id');
            $FeesDetail->admission_id = $request->admission_id;
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
            
            
        //      $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
        //     $BillCounter->counter = $counter + 1;
        //  $BillCounter->save();
           $BillCounter->counter = $BillCounterNo + 1;
         $BillCounter->save();

                $template = MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','hostelcollectfees')->first();
            
                $branch = Branch::find(Session::get('branch_id'));
                $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                $payment_method = PaymentMode::where('id',$request->payment_mode_id)->first();
                
                if (!empty($request->admission_id)) {
                    $feesDetail = HostelAssign::select('hostel_assign.*', 'admissions.first_name', 'admissions.last_name', 'admissions.mobile', 'admissions.email', 'admissions.father_name')
                        ->leftJoin('admissions', 'admissions.id', 'hostel_assign.admission_id')
                        ->where('hostel_assign.admission_id', $request->admission_id)->first(); 
                }
            
         
            
            $arrey1 = array(
                    '{#name#}',
                    '{#total_amount#}',
                    '{#due_amount#}',
                    '{#pay_amount#}',
                    '{#renew_date#}',
                    '{#method#}',
                    '{#support_no#}',
                    '{#school_name#}');
               
            $arrey2 = array(
                $feesDetail->first_name." ".$feesDetail->last_name,
                $request->totalPayableAmount,
                $request->amount,
                $request->duesAmount,
                date('d-M-Y',strtotime($request->hostel_renewal_date)),
                $payment_method->name,
                $setting->mobile,
                $setting->name);
                    
                    
            if($template->status != 1){
                if($feesDetail->email != ""){
                    if($branch->email_srvc != 0){
                        if($template->email_status != 0){
                            $message = str_replace($arrey1,$arrey2,$template->email_content);
                            $emailData = ['email' => $feesDetail->email, 'data' => $message, 'subject' => $template->title];
                            Helper::sendMail('email_print.template_print', $emailData);
                        } 
                    } 
                }
            
                if($branch->whatsapp_srvc != 0){
                    if ($feesDetail->mobile != ""){
                        if($template->whatsapp_status != 0){
                            $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                            Helper::sendWhatsappMessage($feesDetail->mobile,$whatsapp);
                        }
                    }
                }
                
                if($branch->sms_srvc != 0){
                    if($feesDetail->mobile != ""){
                        if($template->sms_status != 0){
                            $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                            Helper::SendMessage($feesDetail->mobile, $sms);
                        }
                    }
                }    
            }    
                    
                    
            }

        if ($request->print == 'print') {
            return redirect::to('hostel_fees_print/' . $payDetail->id)->with('message', 'Payment Collected Successfully.');
        } else {
            return redirect::to('hostel/fees/view')->with('message', 'Payment Collected Successfully.');
        }
    }

    
    public function viewSecurityDeposite(Request $request)
    {
        //dd($request);
         /*  $search['hostel_id'] = "";
        $search['student_details'] ="";*/
        
        $search['hostel_id'] = "";
		$search['student_details'] = "$request->student_details";
	$dataAll =  HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.last_name','admissions.father_name','hostel_room.name as room_name')
	    ->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
		->leftjoin('admissions','admissions.id','hostel_assign.admission_id')->where('hostel_assign.branch_id', Session::get('branch_id'))->where('hostel_assign.bed_status', 1)->orderBy('hostel_assign.id', 'DESC')->get();
      		
         $security_data = SecurityDeposit::Select('security_deposit.*','admissions.first_name','admissions.father_name','admissions.mobile')
                ->leftjoin('hostel_assign','hostel_assign.id','security_deposit.hostel_assign_id')
                ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                ->where('hostel_assign.bed_status',1);
                if ($request->isMethod('post')) { 
                  $security_data = $security_data->where('hostel_assign.admission_id',$request->student_details);  
                }
                
               $security_data = $security_data->get();
           
         
          return view('hostel.fees.security_deposit',['search'=>$search,'allstudents'=>$dataAll,'data'=>$security_data]);
        
    }
    public function addSecurityDeposite(Request $request)
    {
        
           $search['hostel_id'] = "";
        $search['student_details'] ="";
        
        

          $dataAll =  HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.father_name','admissions.mobile')
        ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')->where('hostel_assign.branch_id',Session::get('branch_id'))->where('hostel_assign.bed_status',1)->orderBy('hostel_assign.id','DESC')->get();
        
         $security_data = SecurityDeposit::Select('security_deposit.*','admissions.first_name','admissions.father_name','admissions.mobile')
                ->leftjoin('hostel_assign','hostel_assign.id','security_deposit.hostel_assign_id')
                ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                ->where('hostel_assign.bed_status',1)->get();
        
             if ($request->isMethod('post')) {
                 
                 $old_data = SecurityDeposit::where('hostel_assign_id',$request->hostel_assign_id)->count();
                 
                
                        $deposit = new SecurityDeposit; //model name
          $deposit->mess_security_deposite = $request->mess_security_deposite;
          $deposit->hostel_assign_id = $request->hostel_assign_id;
          $deposit->security_deposit = $request->security_deposit;
          $deposit->payment_mode_id = $request->payment_mode_id;
          $deposit->transaction_id = $request->transaction_id;
          $deposit->pay_remark = $request->pay_remark;
          $deposit->date = $request->date;
          $deposit->save();
          
          
        return redirect::to('hostel/fees/security_deposite' )->with('message', 'Security Deposited Successfully');   
                     
             }
             
          return view('hostel.fees.security_deposit_add',['search'=>$search,'allstudents'=>$dataAll,'security_data',$security_data]);
        
    }
    
    public function refundSecurityDeposite(Request $request)
    {
        
             if ($request->isMethod('post')) {
                 
             
                 
                 $old_data = SecurityDeposit::where('hostel_assign_id',$request->hostel_assign_id)->where('id',$request->security_id)->count();
                
               
                 if($old_data >0 )
                 {
                        $deposit =  SecurityDeposit::where('id',$request->security_id)->update(['status'=>$request->security_status==0 ? 1 : 0]);
       
            return redirect::to('hostel/fees/security_deposite' )->with('message', 'Security Refunded Successfully');   
                     
                 }
                 else
                 {
                       return redirect::to('hostel/fees/security_deposite' )->with('error', 'No Data Found');   
                 }
                 
             }

          return view('hostel.fees.security_deposit',['search'=>$search,'allstudents'=>$dataAll]);
        
    }
     public function deleteSecurityDeposite(Request $request){
        $id = $request->delete_id;
        $bed = SecurityDeposit::find($id);
         $bed->delete();

        return redirect::to('hostel/fees/security_deposite')->with('message', 'Security Deposite Deleted Successfully !');
    }
    
    public function securityDepositPrint(Request $request, $id){

         $security_data = SecurityDeposit::Select('security_deposit.*','admissions.first_name','admissions.father_name','admissions.mobile')
                ->leftjoin('hostel_assign','hostel_assign.id','security_deposit.hostel_assign_id')
                ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                ->where('hostel_assign.bed_status',1)->where('security_deposit.id', $id)->get();
            
        return view('hostel.fees.securityDepositPrint',['data'=>$security_data]);
    }
    
    
    
    public function viewFees(Request $request)
    {
        $serach['name'] = $request->name;
        $serach['starting'] = $request->starting;
        $serach['ending'] =$request->ending ;
        $serach['payment_mode_id'] =$request->payment_mode_id ;
      
        
       $data =  FeesDetail::select('fees_detail.*','admissions.first_name','admissions.last_name','admissions.father_name as f_name','months.name as month_name')->with('PaymentMode')
      ->leftjoin('hostel_assign as hostel_assign','hostel_assign.id','fees_detail.admission_id')
      ->leftjoin('admissions as admissions','admissions.id','fees_detail.admission_id')
      ->leftjoin('months','months.id','fees_detail.month_id');
       if (!empty($request->name)) {

                    $data = $data->where('admissions.first_name', 'LIKE', '%' . $request->name . '%')
                         ->orwhere('admissions.admissionNo', $request->name)
                        ->orwhere('admissions.mobile', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.aadhaar', $request->name)
                        ->orwhere('admissions.email', 'LIKE', '%' . $request->name . '%');
                }
                
                
             
             //   dd($request->admission_no);
                if (!empty($request->admission_no)) {

                    $data = $data->where('hostel_assign.admission_id', $request->admission_no);
                        
                }
                if (!empty($request->payment_mode_id)) {

                    $data = $data->where('fees_detail.payment_mode_id', $request->payment_mode_id);
                        
                }
                if (!empty($request->hostel_account_id)) {

                    $data = $data->where('fees_detail.hostel_account_id', $request->hostel_account_id);
                        
                }
                
                
                if(!empty($request->starting)) {
                    $data = $data->whereBetween('fees_detail.date',[$request->starting,$request->ending]);
                }

                $fees = $data->where('fees_detail.fees_type',1)->orderBy('id', 'DESC')->get();
                 $feeGroup =    $fees->unique('admission_id')->toArray();
      
                 
        return view('hostel.fees.index', ['fees' => $fees,'serach' => $serach , 'feesGroup'=>$feeGroup]);
    }

public function hostelFeesOnclick (Request $request)
    {

        $hostel_assign_id = $request->get('hostel_assign_id');
    $data['stuData'] =  HostelAssign::find($hostel_assign_id);
        
     $lastDayOfMonth = Carbon::createFromDate(date("Y"), $request->month_id)->endOfMonth();
 
        $data['consumption_unit'] =30 * 4;
        
        $startDate = Carbon::parse($data['stuData']['date']);
        $endDate = Carbon::parse('2023-10-31');

        
          $months = $startDate->diffInMonths($lastDayOfMonth);
           $pending_bills= [];
          for($v = 0; $v<=$months; $v++)
          {
              if($v>0)
              {
                    $newDate = $startDate->addMonth(1);
              }
              else
              {
                    $newDate = $startDate->addMonth(0);
              }
           
             $formattedNewDate = Carbon::parse($newDate);
              $pending_bills[] =  $formattedNewDate->format('m');  
           
          }
          
          
    
        
        
    $data['setting'] = Setting::where('session_id',Session::get('session_id'))->where('id',1)->first();    
    
        $data['BillCounter'] = BillCounter::where('type', 'FeesSlip')->get()->first();
   
            return view('hostel.fees.electricityBillPayment.student_bill', ['data' => $data,'pending_bills'=>$pending_bills ,
            'hostel_room_id'=>$request->room_id,
            'floor_id'=>$request->floor_id,
            'building_id'=>$request->building_id,
            'hostel_id'=>$request->hostel_id,
            'month_id'=>$request->month_id,
            ]);
        
    }

    public function hostelFeesPrint($id){
        $data = FeesDetail::select('fees_detail.*','admissions.first_name','admissions.last_name','months.name as month_name','admissions.father_name','admissions.father_mobile as f_mobile','payment_modes.name as payment_mode','admissions.image as student_image')
        ->leftjoin('months','months.id','fees_detail.month_id')
        ->leftjoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
        ->leftjoin('admissions','admissions.id','fees_detail.admission_id')->where('fees_detail.fees_type',1)->where('fees_detail.id',$id)->get()->first();
     
        return view('print_file.hostel.hostel_print_fees', ['data' => $data]);
    }

    public function viewFeesLedger(Request $request){
         $serach['starting'] = $request->starting;
        $serach['ending'] =$request->ending ;
        $serach['name'] =$request->name ;
        
        $data = Admission::select('admissions.*','hostel_assign.hostel_fees','fees_collect.amount as collect_amount','fees_collect.discount')
        ->leftjoin('fees_collect','fees_collect.admission_id','admissions.id')
        ->leftjoin('hostel_assign','hostel_assign.admission_id','admissions.id')->where('admissions.branch_id',Session::get('branch_id'))->where('admissions.hostel',1);
      
        if (!empty($request->name)) {
                    $data = $data->where('admissions.first_name', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.last_name', $request->name)
                        ->orwhere('admissions.mobile', 'LIKE', '%' . $request->name . '%')
                        ->orwhere('admissions.aadhaar', $request->name)
                        ->orwhere('admissions.email', 'LIKE', '%' . $request->name . '%');
                }
      
         if(!empty($request->starting)) {
                    $data = $data->whereBetween('hostel_assign.date',[$request->starting,$request->ending]);
                }

                $data = $data->orderBy('hostel_assign.id', 'DESC')->get();
        
        return view('hostel.fees.ledger_view', ['data' => $data,'serach'=>$serach]);
    }

    public function ledgerFeesPrint($id){
        $data['stuData'] = Admission::select('admissions.*','hostel_assign.hostel_fees')
        ->leftjoin('hostel_assign','hostel_assign.admission_id','admissions.id')->where('admissions.id',$id)->get()->first();
        

        $data['stuPaidDetail'] = FeesDetail::with('PaymentMode')->where('fees_detail.fees_type',1)->where('admission_id',$id)->get();
       
        return view('hostel.fees.ledger_print_fees', ['data' => $data]);
    }
    
    public function hostelLedgerPrint($id){
         $data['stuData'] = Admission::select('admissions.*','hostel_assign.hostel_fees')
        ->leftjoin('hostel_assign','hostel_assign.admission_id','admissions.id')->where('admissions.id',$id)->get()->first();
        

        $data['stuPaidDetail'] = FeesDetail::with('PaymentMode')->where('fees_detail.fees_type',1)->where('admission_id',$id)->get();
      
        return view('print_file.hostel.ledger_print', ['data' => $data]);
    }
    
    
    
    

    public function assignHostalFees(Request $request, $id){

        if($request->isMethod('post')){
            $request->validate([
            
            'date' => 'required', 
            'amount' => 'required', 
            
            ]);
            
            $hostel = new HostelFeesDetail;//model name
            $hostel->user_id = Session::get('id');
            $hostel->session_id = Session::get('session_id');
            $hostel->branch_id = Session::get('branch_id');
            $hostel->hostel_assign_id = $id;
            $hostel->date = $request->date;
            $hostel->amount = $request->amount;
            $hostel->pay_status = 1;
            $hostel->save();
            
            $increment =  HostelAssign::find($id)->where('pay_status',1)->increment('hostel_fees', $request->amount);
            
        return redirect::to('assign_student_view')->with('Hostel')->with('message', 'Fees Assign Successfully !');
        }
            $data = HostelAssign::find($id);
        return view('hostel.fees.assign_fees',['data'=>$data]);
    }




   public function electricityBillPayment(request $request){
          $search['hostel_id'] = $request->hostel_id;
        $search['building_id'] = $request->building_id;
        $search['floor_id'] = $request->floor_id;
        $search['room_id'] = $request->room_id;
        $search['bed_id'] = $request->bed_id;
        $search['month_id'] = $request->month_id;
        
         
        
        if ($request->isMethod('post')) {
            
             $data = HostelAssign::Select('hostel_assign.*','hostel.name as hostel_name','hostel_building.name as building_name','hostel_floor.name as floor_name','hostel_room.name as room_name','hostel_bed.name as bad_name','admissions.admissionNo','admissions.first_name','admissions.father_name','admissions.mobile')
       ->leftjoin('hostel as hostel','hostel.id','hostel_assign.hostel_id')
       ->leftjoin('hostel_building as hostel_building','hostel_building.id','hostel_assign.building_id')
       ->leftjoin('hostel_floor as hostel_floor','hostel_floor.id','hostel_assign.floor_id')
        ->leftjoin('hostel_room as hostel_room','hostel_room.id','hostel_assign.room_id')
        ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
        ->leftjoin('hostel_bed as hostel_bed','hostel_bed.id','hostel_assign.bed_id');   

            
            
            
           if(!empty($request->hostel_id)){
               $data = $data ->where("hostel.id", $request->hostel_id);
            }     
        if(!empty($request->building_id)){
               $data = $data ->where("hostel_building.id", $request->building_id);
            }     
        if(!empty($request->floor_id)){
               $data = $data ->where("hostel_floor.id", $request->floor_id);
            }     
        if(!empty($request->room_id)){
               $data = $data ->where("hostel_room.id", $request->room_id);
            }     

        if(!empty($request->month_id)){
            
              $data = $data  ->whereMonth('date', '<=',$request->month_id);
            
            } 
            
                $allstudents = $data->orderBy('id', 'DESC')->get();
            
       
            return  view('hostel.fees.electricityBillPayment.add', ['data' => $allstudents, 'search' => $search]);
        }

        return  view('hostel.fees.electricityBillPayment.add', ['search' => $search]);
    }



public function hostel_due_amount(Request $request){

 
        $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                            ->leftjoin('hostel_assign','hostel_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                            ->where('hostel_assign.session_id',Session::get('session_id'))->where('hostel_assign.branch_id',Session::get('branch_id'))
                            ->where('invoices.due_amount', '>' ,0)
                            ->GroupBy('invoices.id')->where('invoices.invoice_type',1)->get();

        return  view('hostel/fees/due_amount_list', ['data' => $data]);
    }
    
    public function hostel_due_amount_pay(Request $request,$id){

        $data = Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                            ->leftjoin('hostel_assign as hostel_assign','hostel_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                            ->where('invoices.id',$id)->where('invoices.invoice_type',1)->first();
                            
        if ($request->isMethod('post')) {
            
                        $pay = Invoice::find($id);
                        $pay->paid_amount = $pay->paid_amount + $request->amount;
                        $pay->due_amount = $request->duesAmount;
                        $pay->discount = $pay->discount + $request->discountAmount;
                        $pay->save();
                        
                        $invoice_id = $pay->id;

                        $FeesDetail = new FeesDetail; //model name
                        $FeesDetail->user_id = Session::get('id');
                        $FeesDetail->session_id = Session::get('session_id');
                        $FeesDetail->branch_id = Session::get('branch_id');
                        $FeesDetail->fees_counter_id = 1;
                        $FeesDetail->receipt_no = $data->receipt_no;
                        $FeesDetail->admission_id = $data->admission_id;
                        $FeesDetail->invoice_id = $invoice_id;
                        $FeesDetail->discount = $request->discountAmount;
                        $FeesDetail->discount_type = $request->discountType;
                        $FeesDetail->discount_value = $request->discountValue;
                        $FeesDetail->paid_amount = $request->amount;
                        $FeesDetail->total_amount = $request->hostel_amount;
                        $FeesDetail->payment_mode_id = $request->payment_mode_id;
                        $FeesDetail->fees_type = 1;
                        $FeesDetail->status = 0;
                        $FeesDetail->date = date('Y-m-d');
                        $FeesDetail->save();
                        
    	/*$template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','libraryassign')->first();
        
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first(); 
        $library_name = Library::where('id',$request->library_id)->first(); 
        $library_cabin = LibraryCabin::where('id',$request->cabin_id)->first(); 

                    $arrey1 =   array(
                                    '{#name#}',
                                    '{#library_name#}',
                                    '{#cabin_name#}',
                                    '{#library_fees#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $request->first_name." ".$request->last_name,
                                    $library_name->name,
                                    "Cabin"." ".$library_cabin->name,
                                    $request->library_fees,
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
                    } */
            
                         return redirect::to('hostel_due_amount')->with('message', 'Payment Collected Successfully.');

        }
        
        return  view('hostel/fees/due_amount_pay', ['data' => $data]);
    }
    
        public function hostel_due_amount_reminder(Request $request){
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
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
            
            return redirect('hostel_due_amount')->with('message','Reminder Send Successfully');
    }
    
    public function hostel_invoice(Request $request,$invoice_no,$admission_id){
        $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile as mobile','admissions.admissionNo','payment_modes.name as payment_mode_name'
        ,'fees_detail.discount as per_discount','fees_detail.paid_amount as per_paid_amount','fees_detail.date as bill_date') 
                            ->leftjoin('hostel_assign as hostel_assign','hostel_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                             ->leftjoin('fees_detail','fees_detail.invoice_id','invoices.id')
                            ->leftjoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                            ->where('invoices.invoice_no',$invoice_no)->where('invoices.admission_id',$admission_id)->where('invoices.invoice_type',1)->orderBy('id', 'DESC')->get();
      
        return view('hostel/fees/invoice',['data' => $data]);
    }
    
       public function hostelStudentReport(Request $request){
                $search['hostel_assign_id'] = $request->hostel_assign_id;
       
                $data =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                            ->leftjoin('hostel_assign as hostel_assign','hostel_assign.admission_id','invoices.admission_id')
                            ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                            ->where('hostel_assign.session_id',Session::get('session_id'))
                            ->where('hostel_assign.branch_id',Session::get('branch_id'))
                            ->GroupBy('invoices.id')->where('invoices.invoice_type',1);
    

        if ($request->isMethod('post')) {
           
            if (!empty($request->hostel_assign_id)) {
                $data = $data->where('hostel_assign.id',$request->hostel_assign_id);
            } 
        }else{
            $data = $data->whereMonth('invoices.created_at', '=', date('n'))
                             ->whereYear('invoices.created_at', '=', date('Y'));
        }
           $data = $data->orderBy('invoices.id')->get();
           
        $all_stu = HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.admissionNo')
                                ->leftJoin('admissions','admissions.id','hostel_assign.admission_id')
                                ->where('admissions.status',1)
                                ->where('hostel_assign.status',1)
                                ->get();
                                
    return view('hostel.fees.student_report', ['data' => $data,'search'=>$search,'all_student' => $all_stu,]);
}

    
    

}