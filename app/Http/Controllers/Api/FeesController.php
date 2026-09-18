<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\fees\FeesCounter;
use App\Models\Master\PaymentMode;
use App\Models\FeesDetail;
use App\Models\BillCounter;
use App\Models\Admission;
use App\Models\Invoice;
use App\Models\FeesMaster;
use App\Models\FeesCollect;
use App\Models\fees\FeesAssign;
use Validator;
use Hash;
use File;
use Session;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helper;
use Mail;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;


class FeesController extends BaseController
{

public function counterAdd(Request $request)
{
    try{
           if ($request->isMethod('post')) {
    $counter = new FeesCounter(); //model name
    $counter->name = $request->name;
    $counter->password = $request->password;
    $counter->save();

    return $this->sendResponseData($counter, 'success');
    
           }
    } catch (Exception $e) {
    return $this->sendError('Validation Error.', 'Error');
    }
}

public function counterList(Request $request)
	{
	     try{
	         
	            if ($request->isMethod('post')) {
	    
	    $list = FeesCounter::where('session_id',1)->get();
	    
	    $data = array();
            foreach ($list as $key => $item) {
                $data[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->name,
                    'password' => $item->password,
                );
            }
	   
           
	     return $this->sendResponseData($data, 'success');
	     
	            }
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
		public function counterAuth(Request $request)
	{

	$counter = FeesCounter::where("id", $request->id)->where('password', $request->password)->first();



		if ($counter) {
	return response()->json(['status' => true, 'message' => 'Login Successfull','data'=>$counter], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Counter Found'], 200);
		}

	}


public function counterDelete(Request $request)
	{
	    try{
	   
	   $delete_id = $request->delete_id;
	   
	   $delete_data = FeesCounter::where('id',$delete_id)->delete();
           
	   return response()->json(['status' => true, 'message' => 'Counter Deleted Successfully'], 200);
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
	
		public function counterEdit(Request $request ,$id)
	{
	     try{
	
           $data = FeesCounter::where('id',$id)->first();
           
            if ($request->isMethod('post')) {
               
                
               $data->name = $request->name;
               $data->password = $request->password;
          
            $data->save();
                
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
public function paymentModes(){
     
       try{
	
        $getPaymentMode = PaymentMode::orderBy('id', 'ASC')->get();
        
         $data = array();
            foreach ($getPaymentMode as $item) {
                $data[] = array(
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
        
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
   } 
public function feesDetails(Request $request){
     
       try{
	
	        $BillCounter = BillCounter::where('type','FeesSlip')->get()->first();
        if (!empty($BillCounter)) {
            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounterNo = $counter + 1;
        }
        
        $admission_id = $request->admissionNo;
       $admission =  Admission::find($admission_id);
       $billCounter =  $BillCounterNo;
       $feesAssign =  FeesAssign::where('admission_id',$admission_id)->first();
       $feesCollect=  FeesCollect::where('admission_id',$admission_id)->first();
       $feesMaster=  FeesMaster::where('class_type_id',$admission['class_type_id'])->get();
        // $data['BillCounter'] = BillCounter::where('type','FeesSlip')->get()->first();

        // if (!empty($data['FeesAssign']->total_amount)) {
        //     return view('fees.fees_collect.student_bill', ['data' => $data]);
        // } else {
        //      $data = 0;
        //      echo $data;

        // }
	
$feesDetails=	FeesDetail::Select('fees_detail.*','payment_modes.name as payment_modes')
                            ->leftjoin('payment_modes','payment_modes.id','fees_detail.payment_mode_id')
                     ->where('fees_detail.admission_id', $admission_id)->get();
                           
 $data = array();
            foreach ($feesDetails as $key => $item) {
                $data[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'payment_modes' => $item->payment_modes,
                    'admission_id' => $item->admission_id,
                    'date' => date('d-M-y', strtotime($item->date)),
                    'total_amount' => $item->total_amount,
                    'discount' => $item->discount,
                    'receipt_no' => $item->receipt_no,
                    
                  
                );
            }
        
    return response()->json(['status' => true, 'message' => 'Detail Fetched Successfully','data'=>$data,
    'admissionId'=>$admission_id,'billCounter'=>$billCounter,'feesAssign'=>$feesAssign,
    'feesCollect'=>$feesCollect,
    'feesMaster'=>$feesMaster,
    'admissionData'=>$admission
    ], 200);
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
   } 
   
   public function studentFeesDetails(Request $request){
        $id = $request->admission_id ;
        $data = Admission::where('id',$id)->first();
        
        $fees['school_fees'] = [];
        $fees['library_fees'] = [];
        $fees['hostel_fees'] = [];
        $fees['total_school_fees'] = [];
        $subscription['school'] = 0 ;
        $subscription['library'] = 0 ;
        $subscription['hostel'] =  0;
         if($request->isMethod('post')){
        
        
        if(!empty($data))
        {
                    $subscription['school'] = $data->school ?? 0;
                    $subscription['library'] = $data->library ?? 0;
                    $subscription['hostel'] =  $data->hostel ?? 0;
        }
        if($data->school != 0){
           $fees['school_fees'] = FeesDetail::select('fees_detail.*','admissions.first_name','admissions.mobile')
                                    ->leftjoin('admissions','admissions.id','fees_detail.admission_id')
                                    ->where('fees_detail.fees_type',0)->where('fees_detail.admission_id',$id)->get();
                                    
                    $total_school_fees =  FeesAssign::where('admission_id',$id)->first();
                    if(!empty($total_school_fees))
                    {
                        $collected_school_fees = FeesCollect::where('admission_id',$id)->first();
                        $fees['total_school_fees']  = $total_school_fees->net_amount ?? 0;
                         $fees['collected_school_fees'] =  $collected_school_fees->amount ?? 0;
                    }else
                    {
                         $fees['total_school_fees'] =0;
                         $fees['collected_school_fees'] =0;
                    }
        }
        
        if($data->library != 0){
          $fees['library_fees'] =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                                        ->leftjoin('library_assign as library_assign','library_assign.admission_id','invoices.admission_id')
                                        ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                                        ->where('library_assign.session_id',3)
                                        ->where('library_assign.branch_id',1)
                                        ->GroupBy('invoices.id')->where('invoices.invoice_type',0)
                                        ->where('invoices.admission_id',$id)
                                        ->where('admissions.library',1)->get();
         

                    if(count($fees['library_fees']) != 0)
                    {
                         $fees['total_library_fees']  = $fees['library_fees']->sum('total_amount') ?? 0;
                         $fees['collected_library_fees']  = $fees['library_fees']->sum('paid_amount') ?? 0;
                    }else
                    {
                         $fees['total_library_fees'] = 0;
                         $fees['collected_library_fees'] = 0;
                    }
        }
        
        if($data->hostel != 0){
          $fees['hostel_fees'] =  Invoice::select('invoices.*','admissions.first_name as first_name','admissions.mobile','admissions.admissionNo') 
                                    ->leftjoin('hostel_assign as hostel_assign','hostel_assign.admission_id','invoices.admission_id')
                                        ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                                        ->where('hostel_assign.session_id',3)
                                        ->where('hostel_assign.branch_id',1)
                                        ->GroupBy('invoices.id')->where('invoices.invoice_type',1)
                                        ->where('invoices.admission_id',$id)
                                        ->where('admissions.hostel',1)->get();
    
                    if(count($fees['hostel_fees']) != 0)
                    {
                         $fees['total_hostel_fees']  = $fees['hostel_fees']->sum('total_amount') ?? 0;
                         $fees['collected_hostel_fees']  = $fees['hostel_fees']->sum('paid_amount') ?? 0;
                    }else
                    {
                         $fees['total_hostel_fees'] = 0;
                         $fees['collected_hostel_fees'] = 0;
                    }
        }

              return response()->json(['status' => true, 'message' => 'Fees Details Fetched Successfully', 'data' => $fees, 'subscription' => $subscription], 200);
        }
        
    }
    
    public function printPayementGenerate(Request $request)
    {
        
        $id = $request->admisson_id;
        $fess_print = FeesDetail::with('Admission')->with('PaymentMode')->with('FeesCollect')->with('ClassTypes')->find($id);
        //dd($fess_print);
        $printPreview =    Helper::printPreview('Fees Collect');
        
        $randomString = Str::random(10);
        $pdf = PDF::loadView($printPreview, ['data' => $fess_print]);

        file_put_contents(env('IMAGE_UPLOAD_PATH'). 'feesPaymentPdf' . '/' .$randomString.$fess_print->receipt_no . '.pdf', $pdf->output());
        $file_url = env('IMAGE_SHOW_PATH') . 'feesPaymentPdf' . '/' .$randomString.$fess_print->receipt_no . '.pdf';  
        
        FeesDetail::where('id',$id)->update(['fees_pdf_name' => $file_url]);
        return redirect::to('fees/index')->with('message', 'PDF Generated Successfully !');
        
        // return view($printPreview, ['data' => $fess_print]);
        
        // return view('print_file.student_print.print_fees', ['data' => $fess_print]);
}


    public function schoolFeesPrint(Request $request, $id){
        $fess_print = FeesDetail::with('Admission')->with('PaymentMode')->with('FeesCollect')->with('ClassTypes')->find($id);
        
        $printPreview = Helper::printPreview('Fees Collect');

        $html = View::make($printPreview, ['data' => $fess_print])->render();
        
        $dompdf = new Dompdf();
        
        $dompdf->loadHtml($html);
        
        $dompdf->render();
    
        return $dompdf->stream("schoolFees.pdf");
    }
    
    
        
    public function libraryFeesPrint(Request $request,$invoice_no,$admission_id){
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
                        
      
        $printPreview = Helper::printPreview('Student Fees Collect');

        $html = View::make($printPreview, ['data' => $data])->render();
        
        $dompdf = new Dompdf();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        
        $dompdf->render();
    
        return $dompdf->stream("LibraryFees.pdf");
    }
}