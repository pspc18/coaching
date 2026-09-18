<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\BillCounter;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryTimeSlot;
use App\Models\Invoice;
use App\Models\FeesDetail;
use App\Models\library\LibraryAssign;
use App\Models\Setting;
use App\Models\Master\Branch;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LockerController extends Controller

{

    public function locker(Request $request){

        if($request->isMethod('post')){
            $request->validate([
                
            'name' => 'required',
            
            ]);
            
            $library = new LibraryLocker;//model name
            $library->user_id = Session::get('id');
            $library->session_id = Session::get('session_id');
            $library->branch_id = Session::get('branch_id');
            $library->name = $request->name;
            $library->save();
            

            
        return redirect::to('library/locker')->with('message', 'Locker Added Successfully!');
        }
        $locker_list = LibraryLocker::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $locker_list->where('branch_id',Session::get('branch_id'));
        }
        
            $data = $locker_list->get();  
        
        return view('library.locker.add',['data'=>$data]);
    }

    public function bookLocker(Request $request){
        $data = LibraryAssign::select('library_assign.*','admissions.first_name','admissions.admissionNo')
                                ->leftJoin('admissions','admissions.id','library_assign.admission_id')
                                ->where('admissions.status',1)
                                ->where('library_assign.status',1)
                                ->get();
                                
        $time_slot = LibraryTimeSlot::orderBy('id','ASC')->get(); 
        
        
        if($request->isMethod('post')){
            
            $library_assign = LibraryAssign::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                              ->where('id',$request->library_assign_id)->first();
            
            $library_assign->locker_renewal_date = $request->lockerRenewalDate;
            $library_assign->locker_amount = $request->amount;
            $library_assign->library_locker_id = $request->library_locker_id;
            $library_assign->save();
            
            $invoice = new Invoice;
            $invoice->user_id = Session::get('id');
            $invoice->session_id = Session::get('session_id');
            $invoice->branch_id = Session::get('branch_id');
            $invoice->invoice_no = date('d').random_int(1000, 9999);
            $invoice->library_assign_id = $request->library_assign_id;
            $invoice->admission_id = $library_assign->admission_id;
            $invoice->paid_amount = $request->amount;
            $invoice->total_amount = $request->locker_fees;
            $invoice->due_amount = $request->duesAmount;
            $invoice->locker_renewal_date = $request->lockerRenewalDate;
            $invoice->library_locker_amount = $request->locker_fees;
            $invoice->library_locker_id = $request->library_locker_id;
            $invoice->save();
            
            $FeesDetail = new FeesDetail; //model name
            $FeesDetail->user_id = Session::get('id');
            $FeesDetail->session_id = Session::get('session_id');
            $FeesDetail->branch_id = Session::get('branch_id');
            $FeesDetail->fees_counter_id = 1;
            $FeesDetail->receipt_no = 1;
            $FeesDetail->admission_id = $library_assign->admission_id;
            $FeesDetail->fees_collect_id = $library_assign->fees_collect_id;
            $FeesDetail->invoice_id = $invoice->id;
            $FeesDetail->paid_amount = $request->amount;
            $FeesDetail->total_amount = $request->locker_fees;
            $FeesDetail->payment_mode_id = $request->payment_mode_id;
            $FeesDetail->fees_type = 2;
            $FeesDetail->status = 0;
            $FeesDetail->date = $request->payment_date;
            $FeesDetail->save();
            
            return redirect::to('book_locker')->with('message', 'Locker Booked Successfully!');
        }
        
        return view('library.locker.book_locker',['data'=>$data,'time_slot'=>$time_slot]);  
    }

    public function lockerEdit(Request $request,$id){
    
        $data = LibraryLocker::find($id);
        if($request->isMethod('post')){
            $request->validate([
            
            'name' => 'required|unique:lockers,name,' . $id,
            
            ]);
            
            
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->name = $request->name;
            $data->save();
            
        return redirect::to('library/locker')->with('message', 'Locker Updated Successfully!');
        }
        $locker_list = LibraryLocker::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
        
        return view('library.locker.edit',['data'=>$data,'dataview'=>$locker_list]);
    }

    public function lockerDelete(Request $request){
       
        $id = $request->delete_id;
       
        $hostel = LibraryLocker::find($id)->delete();

        return redirect::to('library/locker')->with('message', 'Locker Deleted Successfully!');
    }


    public function lockerDetails(Request $request){
        $locker = LibraryLocker::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
        $libraryLocker ="";

            foreach($locker as $key => $type){
                
                $alredyAssign = LibraryAssign::select('library_assign.*','admissions.first_name','admissions.admissionNo')
                                            ->leftJoin('admissions','admissions.id','library_assign.admission_id')
                                            ->where('admissions.status',1)
                                            ->where('library_assign.session_id',Session::get('session_id'))
                                            ->where('library_assign.branch_id',Session::get('branch_id'))
                                            ->where('library_assign.library_locker_id',$type->id)
                                            ->where('library_assign.status',1)
                                            ->get()->first();
                
                if(!empty($alredyAssign)){
                    if($alredyAssign->library_locker_id == $type->id){ 
                        $libraryLocker.='<div class="locker assigned booked_student" data-name="'.$alredyAssign->first_name.'" data-admission_no="'.$alredyAssign->admissionNo.'">'.$type->name.'</div>';
                    }                   
                }else{
                     $libraryLocker .= '<div class="locker">'.$type->name.'</div>';

                }
           }

        
        return view('library.locker.locker_details',['lockers' => $libraryLocker]);
        
    }
     
}