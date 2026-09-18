<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\hostel\StudentExpense;
use App\Models\hostel\StudentExpenseDetail;
use App\Models\hostel\HostelAssign;
use Session;
use Hash;
use Helper;
use Str;
use File;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentExpenseController extends Controller

{

	        public function view(Request $request){
        		$search['hostel_assign_id'] = $request->hostel_assign_id;
        		$search['payment_status'] = $request->payment_status;
        		$search['expense_date'] = $request->expense_date;
		        $data = HostelAssign::Select('hostel_assign.*','pay_mode.name as payMode','student_expenses.expense_date','student_expenses.expense_bill','student_expenses.id as expense_id', 'student_expenses.left_amount', 'student_expenses.expense_name', 'student_expenses.expense_amount', 'student_expenses.status as paid_status', 'hostel.name as hostel_name', 'hostel_building.name as building_name', 'hostel_floor.name as floor_name', 'hostel_room.name as room_name', 'hostel_bed.name as bad_name','admissions.first_name','admissions.father_name','admissions.mobile')
    			->leftjoin('hostel as hostel', 'hostel.id', 'hostel_assign.hostel_id')
    			->leftjoin('hostel_building as hostel_building', 'hostel_building.id', 'hostel_assign.building_id')
    			->leftjoin('hostel_floor as hostel_floor', 'hostel_floor.id', 'hostel_assign.floor_id')
    			->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
    			->leftjoin('hostel_bed as hostel_bed', 'hostel_bed.id', 'hostel_assign.bed_id')
    			->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
    			->leftjoin('student_expenses as student_expenses', 'student_expenses.hostel_assign_id', 'hostel_assign.id')
    			->leftjoin('payment_modes as pay_mode', 'pay_mode.id', 'student_expenses.payment_mode')
    			->where('student_expenses.deleted_at', '=', null)
    		    ->where('hostel_assign.session_id',Session::get('session_id'));
    			if(Session::get('role_id') > 1){
		            $data = $data->where('hostel_assign.branch_id',Session::get('branch_id'));
		        }
        		if ($request->isMethod('post')) {
        		    if(!empty($request->hostel_assign_id)){
        		        $data = $data->where('student_expenses.hostel_assign_id',$request->hostel_assign_id);
        		    }
        		    if($request->payment_status != ""){
        		        $data = $data->where('student_expenses.status',$request->payment_status);
        		    }
        		    if(!empty($request->expense_date)){
        		        $data = $data->where('student_expenses.expense_date',$request->expense_date);
        		    }
        		}	
        		$expense_data  = $data->orderBy('id','desc')->get();		
        		return view('hostel.student_expneses.view', ['data' => $expense_data, 'search' => $search]);
        	}

	        public function add(Request $request){
	            $dataAll =  HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.father_name')
                ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')->where('hostel_assign.branch_id', Session::get('branch_id'))->where('hostel_assign.bed_status', 1)->orderBy('hostel_assign.id', 'DESC')->get();
		        if ($request->isMethod('post')) {
			        $expense = new StudentExpense; //model name
                    $expense_bill = '';
                    if ($request->file('expense_bill')) {
                        $image = $request->file('expense_bill');
                        $path = $image->getRealPath();
                        $expense_bill = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'student_expence_bill';
                        $image->move($destinationPath, $expense_bill);
                    }
        			$expense->user_id = Session::get('id');
        			$expense->session_id = Session::get('session_id');
        			$expense->branch_id = Session::get('branch_id');
        			$expense->hostel_assign_id = $request->hostel_assign_id;
        			$expense->expense_amount = $request->amount;
        			$expense->expense_date = $request->expense_date;
        			$expense->payment_mode = $request->payment_mode;
        			$expense->expense_bill = $expense_bill;
        			$expense->left_amount = $request->amount;
        			$expense->expense_name = $request->expense_name;
        			$expense->save();
			        return redirect::to('student_expenses')->with('message', 'Expense Added Successfully !');
		        }
		        return view('hostel.student_expneses.add',['allstudents' => $dataAll]);
	        }

	        public function edit(Request $request,$id){
        	    $expense = StudentExpense::find($id);
        		if ($request->isMethod('post')) {
                    $expense_bill = '';
                    if ($request->file('expense_bill')) {
                        $image = $request->file('expense_bill');
                        $path = $image->getRealPath();
                        $expense_bill = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'student_expence_bill';
                        $image->move($destinationPath, $expense_bill);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'student_expence_bill/' . $expense->expense_bill)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'student_expence_bill/' . $expense->expense_bill);
                        }
                        $expense->expense_bill = $expense_bill;
                    }
        			$expense->user_id = Session::get('id');
        			$expense->session_id = Session::get('session_id');
        			$expense->branch_id = Session::get('branch_id');
        			$expense->hostel_assign_id = $request->hostel_assign_id;
        			$expense->expense_amount = $request->amount;
        			$expense->expense_date = $request->expense_date;
        			$expense->payment_mode = $request->payment_mode;
        			$expense->left_amount = $request->amount;
        			$expense->expense_name = $request->expense_name;
        			$expense->save();
        			return redirect::to('student_expenses')->with('message', 'Expense Edit Successfully !');
        		}
		        return view('hostel.student_expneses.edit',['expense' => $expense]);
	        }

	        public function change_expense_status(Request $request){
        		if ($request->paid_amount != 0) {
        			$data =  StudentExpense::where('id', $request->paid_id)->update(['left_amount' => $request->paid_amount]);
        		} else {
        			$data =  StudentExpense::where('id', $request->paid_id)->update(['left_amount' => $request->paid_amount, 'status' => 1]);
        		}
        		$deatil = new StudentExpenseDetail;
        		$deatil->student_expense_id = $request->paid_id;
        		$deatil->paid_amount = $request->amount_to_pay;
        		$deatil->save();
		        return redirect::to('student_expenses')->with('message', 'Expense Updated Successfully !');
	        }

	        public function expenses_all_pay(Request $request){
	            $count = count($request->details);
	            for($i = 0; $i <$count; $i++){
        	        $expence_id = ($request->details[$i])['expence_id'];
        	        $left_amt = ($request->details[$i])['left_amt'];
        	        $data = StudentExpense::where('id', $expence_id)->update(['left_amount' =>0, 'status' => 1]);
        	        $deatil = new StudentExpenseDetail;
            	    $deatil->student_expense_id = $expence_id;
            	    $deatil->paid_amount = $left_amt;
            	    $deatil->save();
	            }
            }

	        public function delete_expence(Request $request){
        		$data = StudentExpense::where('id', $request->delete_id)->delete();
        		$details = StudentExpenseDetail::where('student_expense_id', $request->delete_id)->delete();
        		return redirect::to('student_expenses')->with('message', 'Expense Delete Successfully !');
        	}
}
