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
class HostelElectricityBillController extends Controller{
    
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
    
            public function hostelFeesOnclick (Request $request){
                //dd($request);
                $hostel_assign_id = $request->get('hostel_assign_id');
                $data['stuData'] =  HostelAssign::find($hostel_assign_id);
                $lastDayOfMonth = Carbon::createFromDate(date("Y"), $request->month_id)->endOfMonth();
                $data['consumption_unit'] =30 * 4;
                $startDate = Carbon::parse($data['stuData']['date']);
                $endDate = Carbon::parse('2023-10-31');
                $months = $startDate->diffInMonths($lastDayOfMonth);
                $pending_bills= [];
                for($v = 0; $v<=$months; $v++){
                    if($v>0){
                        $newDate = $startDate->addMonth(1);
                    }
                    else{
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
    
            public function hostelElectricityPaySubmit(Request $request){
                if ($request->isMethod('post')) {
                    //dd($request);
                    $electricitybil_img = '';
                    if ($request->file('electricitybil_img')) {
                        $image = $request->file('electricitybil_img');
                        $path = $image->getRealPath();
                        $electricitybil_img = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'electricitybil_img';
                        //dd($destinationPath);
                        $image->move($destinationPath, $electricitybil_img);
                    }
                    $hostelpayDetail = new ElectricityBillPayment; //model name
                    $hostelpayDetail->user_id = Session::get('id');
                    $hostelpayDetail->session_id = Session::get('session_id');
                    $hostelpayDetail->branch_id = Session::get('branch_id');
                    $hostelpayDetail->admission_id = $request->admission_id;
                    $hostelpayDetail->hostel_assign_id = $request->hostel_assign_id;
                    $hostelpayDetail->total_days = $request->total_days;
                    $hostelpayDetail->status = 0;
                    $hostelpayDetail->meter_unit = $request->meter_unit;
                    $hostelpayDetail->last_meter_unit = $request->last_meter_unit;
                    $hostelpayDetail->this_meter_unit = $request->this_meter_unit;
                    $hostelpayDetail->last_month_date = $request->last_month_date;
                    $hostelpayDetail->this_month_date = $request->this_month_date;
                    $hostelpayDetail->per_unit_rate = $request->per_unit_rate;
                    $hostelpayDetail->total_monthly_unit = $request->total_monthly_unit;
                    $hostelpayDetail->month_id = $request->month_id;
                    $hostelpayDetail->monthly_consumption_uni = $request->monthly_consumption_uni;
                    $hostelpayDetail->payment_mode_id = $request->payment_mode_id;
                    $hostelpayDetail->pay_amount = $request->pay_amount;
                    $hostelpayDetail->total_amount = $request->total_amount;
                    $hostelpayDetail->electricitybil_img = $electricitybil_img;
                    $hostelpayDetail->save();
                }
                return redirect::to('hostel_fees_electricity_view' )->with('message', 'Collected For This Month.');   
            }

            public function unitData(Request $request, $month_id, $admission_id) {
                $previousMonthId = $month_id - 1;
                $electricityBill = ElectricityBillPayment::where('admission_id', $admission_id)
                ->where('month_id', $previousMonthId)
                ->select('this_meter_unit')
                ->first();
                if ($electricityBill) {
                    // Output the 'this_meter_unit' value
                    return response()->json($electricityBill->this_meter_unit);
                } 
                else {
                    return response()->json(['message' => 'No data found'], 404);
                }
            }

            public function hostelElectricityPayView(Request $request){
        		$search['hostel_id'] = $request->hostel_id;
        		$search['building_id'] = $request->building_id;
        		$search['floor_id'] = $request->floor_id;
        		$search['room_id'] = $request->room_id;
        		$search['bed_id'] = $request->bed_id;
        		$search['month_id'] = $request->month_id;
		        $data = ElectricityBillPayment::Select('electricity_bill_payments.*', 'hostel_assign.status as hostel_status', 'months.name as month_name', 'hostel.name as hostel_name', 'hostel_building.name as building_name', 'hostel_floor.name as floor_name',
    		    'hostel_room.name as room_name', 'hostel_bed.name as bad_name','admission.first_name','admission.last_name',
    		    'admission.dob as student_dob','admission.father_name','admission.father_mobile','admission.address')
    			->leftjoin('hostel_assign', 'hostel_assign.id', 'electricity_bill_payments.hostel_assign_id')
    			->leftjoin('months', 'months.id', 'electricity_bill_payments.month_id')
    			->leftjoin('hostel as hostel', 'hostel.id', 'hostel_assign.hostel_id')
    			->leftjoin('hostel_building as hostel_building', 'hostel_building.id', 'hostel_assign.building_id')
    			->leftjoin('hostel_floor as hostel_floor', 'hostel_floor.id', 'hostel_assign.floor_id')
    			->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
    			->leftjoin('admissions as admission', 'admission.id', 'hostel_assign.admission_id')
    			->leftjoin('hostel_bed as hostel_bed', 'hostel_bed.id', 'hostel_assign.bed_id');
	            //dd($data);		
        		if ($request->isMethod('post')) {
        			if (!empty($request->hostel_id)) {
        				$data = $data->where("hostel.id", $request->hostel_id);
        			}
        			if (!empty($request->building_id)) {
        				$data = $data->where("hostel_building.id", $request->building_id);
        			}
        			if (!empty($request->floor_id)) {
        				$data = $data->where("hostel_floor.id", $request->floor_id);
        			}
        			if (!empty($request->room_id)) {
        				$data = $data->where("hostel_room.id", $request->room_id);
        			}
        			if (!empty($request->bed_id)) {
        				$data = $data->where("hostel_bed.id", $request->bed_id);
        			}
        			if (!empty($request->month_id)) {
        				$data = $data->where("months.id", $request->month_id);
        			}
        		}
		        $data = $data->where('electricity_bill_payments.session_id', Session::get('session_id'))->where('electricity_bill_payments.branch_id', Session::get('branch_id'))->orderBy('id', 'ASC')->get();
		        return view('hostel.fees.electricityBillPayment.view', ['data' => $data, 'search' => $search]);
	        }
        
    
    
}
