<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\Head;
use App\Models\hostel\Mess;
use App\Models\Setting;
use App\Models\hostel\HostelExpences;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Master\PaymentMode;
use Session;
use Hash;
use Helper;
use File;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeadController extends Controller

{

	public function dashboard()
	{

		return view('hostel.hostel_dashboard');
	}


	public function hostelExpensesHeadeAdd(Request $request)
	{

		if ($request->isMethod('post')) {
			$request->validate([


				'name' => 'required',
				'description' => 'required',

			]);

			$hostel = new Head; //model name
			$hostel->session_id = Session::get('session_id');
            $hostel->branch_id = Session::get('branch_id');
            $hostel->user_id = Session::get('id');
			$hostel->name = $request->name;
			$hostel->description = $request->description;
			$hostel->type = 'expance';
			$hostel->save();

			return redirect::to('hostelExpensesHeadeAdd')->with('message', 'Head Added Successfully !');
		}
		$data = Head::where('type', 'expance')->where('session_id',Session::get('session_id'));
		
		if(Session::get('role_id') > 1){
		    $data = $data->where('branch_id',Session::get('branch_id'));
		}
		$data = $data->orderBy('id','desc')->get();

		return view('hostel.head.add', ['data' => $data]);
	}

	public function hostelExpensesHeadeEdit(Request $request, $id)
	{

		$data = Head::find($id);
		if ($request->isMethod('post')) {
			$request->validate([


				'name' => 'required',
				'description' => 'required',

			]);

            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->user_id = Session::get('id');
			$data->name = $request->name;
			$data->description = $request->description;
			$data->type = 'expance ';

			$data->save();

			return redirect::to('hostelExpensesHeadeAdd')->with('message', 'Head Updated Successfully !');
		}

		return view('hostel.head.edit', ['data' => $data]);
	}

	public function hostelExpensesHeadeDelete(Request $request)
	{

		$id = $request->delete_id;

		$building = Head::find($id)->delete();

		return redirect::to('hostelExpensesHeadeAdd')->with('message', 'Head Deleted Successfully !');
	}
	public function hostelExpensesAdd(Request $request)
	{

		if ($request->isMethod('post')) {
			$request->validate([

				'expense_head' => 'required',
				'expense_name' => 'required',
				'expense_date' => 'required',
				'expense_amount' => 'required',
				'expense_by' => 'required',
				'payment_mode' => 'required',

			]);
		

			$expense_bill = '';
			if ($request->file('expense_bill')) {
				$image = $request->file('expense_bill');
				$path = $image->getRealPath();
				$expense_bill =  time() . uniqid() . $image->getClientOriginalName();
				$destinationPath = env('IMAGE_UPLOAD_PATH') . 'expense_bill';
				$image->move($destinationPath, $expense_bill);
			}

			$hostel = new HostelExpences; //model name
			$hostel->session_id = Session::get('session_id');
            $hostel->branch_id = Session::get('branch_id');
            $hostel->user_id = Session::get('id');
			$hostel->expense_head = $request->expense_head;
			$hostel->expense_name = $request->expense_name;
			$hostel->expense_date = $request->expense_date;
			$hostel->expense_amount = $request->expense_amount;
			$hostel->expense_by = $request->expense_by;
			$hostel->payment_mode = $request->payment_mode;
			$hostel->expense_bill = $expense_bill;

			$hostel->save();
			
			$template =  MessageTemplate::Select('message_templates.*', 'message_types.slug')
				->leftjoin('message_types', 'message_types.id', 'message_templates.message_type_id')
				->where('message_types.status', 1)->where('message_types.slug', 'HostelExpences')->first();
				
			$branch = Branch::find(Session::get('branch_id'));
            $setting = Setting::where('branch_id',Session::get('branch_id'))->first();
            $expense_by = User::where('id', $request->expense_by)->get()->first();
            $PaymentMode = PaymentMode::where('id',$request->payment_mode)->first();
            
            
            $arrey1 =   array(
                            '{#name#}',
                            '{#expenses_name#}',
                            '{#amount#}',
                            '{#method#}',
                            '{#date_time#}',
                            '{#support_no#}',
                            '{#school_name#}');
                           
            $arrey2 = array(
                            $expense_by->first_name." ".$expense_by->last_name,
                            $request->expense_name,
                            $request->expense_amount,
                            $PaymentMode->name,
                            date('d-m-Y h:i A'),
                            $setting->mobile,
                            $setting->name);	

			        if($template->status != 1){
                            if($expense_by->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $expense_by->email, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($expense_by->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($expense_by->mobile, $whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($expense_by->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($expense_by->mobile, $sms);
                                    }
                                }
                            }    
                    }
                     $url = '/hostelExpensesPrint/'.$hostel->id;
                      ?>
                    <script type="text/javascript">
                        window.open("<?=$url?>", "_blank");
                    </script>
                <?php

			return redirect::to('hostelExpensesView')->with('message', 'Head Added Successfully !');
		}


		return view('hostel.ExpanceAdd.add');
	}

	public function hostelExpensesEdit(Request $request, $id)
	{

		$data = HostelExpences::find($id);

		if ($request->isMethod('post')) {
			$request->validate([

				'expense_head' => 'required',
				'expense_name' => 'required',
				'expense_date' => 'required',
				'expense_amount' => 'required',
				'expense_by' => 'required',
				'payment_mode' => 'required',

			]);
			$expense_bill = '';
			if ($request->file('expense_bill')) {
				$image = $request->file('expense_bill');
				$path = $image->getRealPath();
				$expense_bill =  time() . uniqid() . $image->getClientOriginalName();
				$destinationPath = env('IMAGE_UPLOAD_PATH') . 'expense_bill';
				$image->move($destinationPath, $expense_bill);
				 if (File::exists(env('IMAGE_UPLOAD_PATH') . 'expense_bill/' . $data->expense_bill)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'expense_bill/' . $data->expense_bill);
                    }
				$data->expense_head = $expense_bill;
			}
            $data = new HostelExpences; //model name
			$data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->user_id = Session::get('id');
			$data->expense_head = $request->expense_head;
			$data->expense_name = $request->expense_name;
			$data->expense_date = $request->expense_date;
			$data->expense_amount = $request->expense_amount;
			$data->expense_by = $request->expense_by;
			$data->payment_mode = $request->payment_mode;
			$data->save();

			return redirect::to('hostelExpensesView')->with('message', 'Hostel Expences Updated Successfully !');
		}

		return view('hostel.ExpanceAdd.edit', ['data' => $data]);
	}

	public function hostelExpensesDelete(Request $request)
	{

		$id = $request->delete_id;

		$building = HostelExpences::find($id);

		 if (File::exists(env('IMAGE_UPLOAD_PATH') . 'expense_bill/' . $building->expense_bill)) {
            File::delete(env('IMAGE_UPLOAD_PATH') . 'expense_bill/' . $building->expense_bill);
                 }
         $building->delete();

		return redirect::to('hostelExpensesView')->with('message', 'Hostel Expences Deleted Successfully !');
	}
	public function hostelExpensesView(Request $request)
	{
        $data = HostelExpences::select('hostel_expences.*', 'heads.name as expense_head', 'users.first_name as first_name', 'users.last_name as last_name')
			->leftjoin('heads', 'heads.id', 'hostel_expences.expense_head')
			->leftjoin('users', 'users.id', 'hostel_expences.expense_by')->where('hostel_expences.session_id',Session::get('session_id'));
			if(Session::get('role_id') > 1){
		    $data = $data->where('hostel_expences.branch_id',Session::get('branch_id'));
		}
		$data = $data->orderBy('id','desc')->get();
			

		return view('hostel.ExpanceAdd.view', ['data' => $data]);
	}


	public function messFoodAdd(Request $request)
	{

		if ($request->isMethod('post')) {
			$request->validate([


				'name' => 'required',

			]);

			$messHead = new Head; //model name
			$messHead->name = $request->name;
			$messHead->description = $request->description;
			$messHead->type = 'mess';
			$messHead->save();

			return redirect::to('messFoodAdd')->with('message', 'Mess Head Added Successfully !');
		}
		$data = Head::where('type', 'mess')->get();

		return view('hostel.mess_head.add', ['data' => $data]);
	}

	public function hostelMessEdit(Request $request, $id)
	{

		$data = Head::find($id);
		if ($request->isMethod('post')) {
			$request->validate([


				'head_name' => 'required',

			]);

			$data->head_name = $request->head_name;
			$data->time_frome = $request->time_frome;
			$data->to_frome = $request->to_frome;
			$data->type = 'mess';
			$data->save();

			return redirect::to('messFoodAdd')->with('message', 'Mess Head Updated Successfully !');
		}

		return view('hostel.mess_head.edit', ['data' => $data]);
	}

	public function messFoodDelete(Request $request)
	{

		$id = $request->delete_id;

		$building = Head::find($id)->delete();

		return redirect::to('messFoodAdd')->with('message', 'Mess Head Deleted Successfully !');
	}

	public function hostelExpensesPrint(Request $request, $id)
	{
		$data = HostelExpences::select('hostel_expences.*', 'users.first_name', 'users.last_name')
			->leftJoin('users', 'users.id', 'hostel_expences.expense_by')
			->where('hostel_expences.id', $id)->get()->first();
			
         $printPreview =    Helper::printPreview('Hostel Expese Slip');
        
        return view($printPreview, ['data' => $data]);
		//return view('print_file.hostel.hostel_expance', ['data' => $data]);
	}
}
