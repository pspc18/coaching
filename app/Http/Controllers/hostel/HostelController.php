<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\hostel\Hostel;
use App\Models\hostel\HostelBuilding;
use App\Models\BillCounter;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Master\RegistrationTerms;
use App\Models\hostel\HostelFloor;
use App\Models\hostel\HostelRoom;
use App\Models\hostel\HostelBed;
use App\Models\Invoice;
use App\Models\hostel\StudentExpense;
use App\Models\hostel\HostelAssign;
use App\Models\hostel\HostelDetail;
use App\Models\FeesDetail;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\Head;
use App\Models\Master\MessageTemplate;
use App\Models\hostel\HostelMeterUnit;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelController extends Controller

{

	        public function dashboard(){
		        return view('hostel.hostel_dashboard');
	        }
	        public function hostelAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
				        'name' => 'required',
			        ]);
        			$hostel = new Hostel; //model name
        			$hostel->user_id = Session::get('id');
        			$hostel->session_id = Session::get('session_id');
        			$hostel->branch_id = Session::get('branch_id');
        			$hostel->name = $request->name;
        			$hostel->per_unit_rate = $request->per_unit_rate;
        			$hostel->save();
			        return redirect::to('hostel_add')->with('message', 'Hostel Added Successfully !');
		        }
		        $hostel_list = Hostel::orderBy('id', "DESC")->where('session_id', Session::get('session_id'))->get();;
        		if(Session::get('role_id') > 1){
        		    $hostel_list = $hostel_list->where('branch_id', Session::get('branch_id'));
        		}
        		$building_list = HostelBuilding::groupBy('hostel_id')->orderBy('id', "DESC")->get();
        		$floor_list = HostelFloor::groupBy('building_id')->orderBy('id', "DESC")->get();
        		$room_list = HostelRoom::groupBy('floor_id')->orderBy('id', "DESC")->get();
        		$bed_list = HostelBed::groupBy('room_id')->orderBy('id', "ASC")->get();
        		$student_list = HostelAssign::groupBy('bed_id')->get();
		        return view('hostel.hostel.add', ['data' => $hostel_list, 'data2' => $building_list, 'data3' => $floor_list, 'data4' => $room_list, 'data5' => $bed_list, 'data6' => $student_list]);
	        }

	        public function hostelEdit(Request $request, $id){
		        $data = Hostel::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
				        'name' => 'required',
			        ]);
        			$data->session_id = Session::get('session_id');
        			$data->branch_id = Session::get('branch_id');
        			$data->name = $request->name;
        			$data->per_unit_rate = $request->per_unit_rate;
        			$data->save();
			        return redirect::to('hostel_add')->with('message', 'Hostel Updated Successfully !');
		        }
		        $hostel_list = Hostel::orderBy('id', "DESC")->where('session_id', Session::get('session_id'))
			    ->where('branch_id', Session::get('branch_id'))->get();
		        return view('hostel.hostel.edit', ['data' => $data, 'dataview' => $hostel_list]);
	        }

	        public function hostelDelete(Request $request){
		        $id = $request->delete_id;
		        $hostel = Hostel::find($id)->delete();
		        return redirect::to('hostel_add')->with('message', 'Hostel Deleted Successfully !');
	        }
    
	        public function buildingAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'name' => 'required',
			        ]);
			        for($i=0; $i < count($request->name); $i++){
        			    $hostel = new HostelBuilding; //model name
            			$hostel->user_id = Session::get('id');
            			$hostel->session_id = Session::get('session_id');
            			$hostel->branch_id = Session::get('branch_id');
            			$hostel->hostel_id = $request->hostel_id;
            			$hostel->name = $request->name[$i];
            			$hostel->save();
			        }
			        return redirect::to('building_add')->with('Hostel')->with('message', 'Building Added Successfully !');
		        }
		        $building_list = HostelBuilding::groupBy('hostel_id')->orderBy('id', "DESC")->where('session_id', Session::get('session_id'))->get();;
        		if(Session::get('role_id') > 1){
        		    $building_list = $building_list->where('branch_id', Session::get('branch_id'));
        		}
		        return view('hostel.building.add', ['data' => $building_list]);
	        }

	        public function buildingEdit(Request $request, $id){
		        $data = HostelBuilding::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'name' => 'required',
        			]);
        			$data->session_id = Session::get('session_id');
        			$data->branch_id = Session::get('branch_id');
        			$data->hostel_id = $request->hostel_id;
        			$data->name = $request->name;
        			$data->save();
			        return redirect::to('building_add')->with('message', 'Building Updated Successfully !');
		        }
		        $building_list = HostelBuilding::groupBy('hostel_id')->orderBy('id', "DESC")->where('session_id', Session::get('session_id'))
			    ->where('branch_id', Session::get('branch_id'))->get();
		        return view('hostel.building.edit', ['data' => $data, 'building_list' => $building_list]);
	        }
	        public function buildingDelete(Request $request){
		        $id = $request->delete_id;
		        $building = HostelBuilding::find($id)->delete();
		        return redirect::to('building_add')->with('message', 'Building Deleted Successfully !');
	        }
	        public function floorAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'building_id' => 'required',
        				'name' => 'required',
        			]);
                    for($i=0; $i < count($request->name); $i++){
            			$hostel = new HostelFloor; //model name
            			$hostel->user_id = Session::get('id');
            			$hostel->session_id = Session::get('session_id');
            			$hostel->branch_id = Session::get('branch_id');
            			$hostel->hostel_id = $request->hostel_id;
            			$hostel->building_id = $request->building_id;
            			$hostel->name = $request->name[$i];
            			$hostel->save();
                    }
			        return redirect::to('floor_add')->with('Hostel')->with('HostelBuilding')->with('message', 'Floor Added Successfully !');
		        }
		        $floor_list = HostelFloor::groupBy('hostel_id')->groupBy('building_id')->orderBy('id', "DESC")->get();
		        return view('hostel.floor.add', ['data' => $floor_list]);
	        }

	        public function hostelData(Request $request, $id){
		        $data = array();
		        if (!empty($id)) {
        			$data = HostelBuilding::with('Hostel')->where('hostel_id', $id)->get();
        			$hostelData = "";
        			foreach ($data as $key => $type) {
        				$hostelData .= '<div class="form-check">
                       <input class="form-check-input hostel" id="building' . $key . '" type="checkbox" data-value="' . $type->id . '">
                       <label for="building' . $key . '"class="form-check-label">' . $type->name . '</label>
                    </div>';
        			}
        			echo $hostelData;
        		}
	        }
	        public function floorEdit(Request $request, $id){
		        $data = HostelFloor::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'building_id' => 'required',
        				'name' => 'required',
        			]);
        			$data->session_id = Session::get('session_id');
        			$data->branch_id = Session::get('branch_id');
        			$data->hostel_id = $request->hostel_id;
        			$data->building_id = $request->building_id;
        			$data->name = $request->name;
        			$data->save();
			        return redirect::to('floor_add')->with('message', 'Floor Updated Successfully !');
		        }
		        $floor_list = HostelFloor::orderBy('id', "DESC")->get();
		        return view('hostel.floor.edit', ['data' => $data, 'dataview' => $floor_list]);
	        }

	        public function floorDelete(Request $request){
		        $id = $request->delete_id;
		        $floor = HostelFloor::find($id)->delete();
		        return redirect::to('floor_add')->with('message', 'Floor Deleted Successfully !');
	        }

	        public function floorSearchData(Request $request){
        		$hostel_id_search = $request->get('hostel_id');
        		$building_id_search = $request->get('building_id');
        		$data =  HostelFloor::all();
        		if (!empty($hostel_id_search)) {
        			$data = $data->where("hostel_id", $hostel_id_search);
        		}
        		if (!empty($building_id_search)) {
        			$data = $data->where("building_id", $building_id_search);
        		}
		        $allfloor = $data->all();
		        return  view('hostel.floor.floor_search', ['data' => $allfloor]);
	        }

	        public function roomAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'room_category' => 'required',
        				'name' => 'required',
        			]);
                    for($i=0; $i < count($request->name); $i++){
            			$hostel = new HostelRoom; //model name
            			$hostel->user_id = Session::get('id');
            			$hostel->session_id = Session::get('session_id');
            			$hostel->branch_id = Session::get('branch_id');
            			$hostel->hostel_id = $request->hostel_id;
            			$hostel->building_id = $request->building_id;
            			$hostel->floor_id = $request->floor_id;
            			$hostel->room_category = $request->room_category;
            			$hostel->name = $request->name[$i];
            			$hostel->save();
            			$bed = new HostelBed; //model name
                        $bed->user_id = Session::get('id');
                        $bed->session_id = Session::get('session_id');
                        $bed->branch_id = Session::get('branch_id');
                        $bed->hostel_id = $request->hostel_id;
                        $bed->building_id = $request->building_id;
                        $bed->floor_id = $request->floor_id;
                        $bed->room_id = $hostel->id;
                        $bed->name = "A";
                        $bed->save();
                        $bed2 = new HostelBed; //model name
                        $bed2->user_id = Session::get('id');
                        $bed2->session_id = Session::get('session_id');
                        $bed2->branch_id = Session::get('branch_id');
                        $bed2->hostel_id = $request->hostel_id;
                        $bed2->building_id = $request->building_id;
                        $bed2->floor_id = $request->floor_id;
                        $bed2->room_id = $hostel->id;
                        $bed2->name = "B";
                        $bed2->save();
                    }
			        return redirect::to('room_view')->with('message', 'Room Added Successfully !');
		        }
		        return view('hostel.room.add');
	        }

	        public function roomView(){
		        $room_list = HostelRoom::select('hostel_room.*', 'hostel_building.name as bildng_name', 'hostel.name as hostel_name', 'hostel_floor.name as floor_name')
			    ->leftjoin('hostel_building', 'hostel_building.id', 'hostel_room.building_id')
			    ->leftjoin('hostel', 'hostel.id', 'hostel_room.hostel_id')
			    ->leftjoin('hostel_floor', 'hostel_floor.id', 'hostel_room.floor_id')->where('hostel_room.session_id', Session::get('session_id'))->orderBy('id', 'DESC')->get();
		        return view('hostel.room.view', ['data' => $room_list]);
	        }

	        public function roomEdit(Request $request, $id){
		        $data = HostelRoom::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'building_id' => 'required',
        				'floor_id' => 'required',
        				'name' => 'required',
			        ]);
        			$data->session_id = Session::get('session_id');
        			$data->branch_id = Session::get('branch_id');
        			$data->hostel_id = $request->hostel_id;
        			$data->building_id = $request->building_id;
        			$data->floor_id = $request->floor_id;
        			$data->name = $request->name;
        			$data->save();
			        return redirect::to('room_view')->with('message', 'Room Updated Successfully !');
	    	    }
		        return view('hostel.room.edit', ['data' => $data]);
	        }

	        public function roomDelete(Request $request){
		        $id = $request->delete_id;
		        $room = HostelRoom::find($id)->delete();
		        return redirect::to('room_view')->with('message', 'Room Deleted Successfully !');
	        }
	        public function roomSearchData(Request $request){
        		$hostel_id = $request->get('hostel_id');
        		$building_id = $request->get('building_id');
        		$floor_id = $request->get('floor_id');
        		$data =  HostelRoom::all();
        		if (!empty($hostel_id)) {
        			$data = $data->where("hostel_id", $hostel_id);
        		}
        		if (!empty($building_id)) {
        			$data = $data->where("building_id", $building_id);
        		}
        		if (!empty($floor_id)) {
        			$data = $data->where("floor_id", $floor_id);
        		}
		        $allroom = $data->all();
		        return  view('hostel.room.room_search', ['data' => $allroom]);
	        }

	        public function bedAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'building_id' => 'required',
        				'floor_id' => 'required',
        				'room_id' => 'required',
        				'name' => 'required',
    			    ]);
                    for($i=0; $i < count($request->name); $i++){
            			$hostel = new HostelBed; //model name
            			$hostel->user_id = Session::get('id');
            			$hostel->session_id = Session::get('session_id');
            			$hostel->branch_id = Session::get('branch_id');
            			$hostel->hostel_id = $request->hostel_id;
            			$hostel->building_id = $request->building_id;
            			$hostel->floor_id = $request->floor_id;
            			$hostel->room_id = $request->room_id;
            			$hostel->name = $request->name[$i];
            			$hostel->save();
                    }
			        return redirect::to('bed_view')->with('message', 'Bed Added Successfully !');
		        }
		        return view('hostel.bed.add');
	        }

	        public function bedView(){
		        $bed_list = HostelBed::orderBy('id', "DESC");
        		if(Session::get('role_id') > 1){
        		    $bed_list = $bed_list->where('branch_id',Session::get('branch_id'));
        		}
        		$bed_list = $bed_list->get();
        		return view('hostel.bed.view', ['data' => $bed_list]);
	        }

	        public function bedEdit(Request $request, $id){
		        $data = HostelBed::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
        				'hostel_id' => 'required',
        				'building_id' => 'required',
        				'floor_id' => 'required',
        				'room_id' => 'required',
        				'name' => 'required',
        			]);
        			$data->session_id = Session::get('session_id');
        			$data->branch_id = Session::get('branch_id');
        			$data->hostel_id = $request->hostel_id;
        			$data->building_id = $request->building_id;
        			$data->floor_id = $request->floor_id;
        			$data->room_id = $request->room_id;
        			$data->name = $request->name;
        			$data->save();
			        return redirect::to('bed_view')->with('message', 'Bed Updated Successfully !');
		        }
		        return view('hostel.bed.edit', ['data' => $data]);
	        }

	        public function bedDelete(Request $request){
		        $id = $request->delete_id;
        		$bed = HostelBed::find($id)->delete();
        		return redirect::to('bed_view')->with('message', 'Bed Deleted Successfully !');
        	}

	        public function bedSearchData(Request $request){
        		$hostel_id = $request->get('hostel_id');
        		$building_id = $request->get('building_id');
        		$floor_id = $request->get('floor_id');
        		$room_id = $request->get('room_id');
        		$data =  HostelBed::all();
        		if (!empty($hostel_id)) {
        			$data = $data->where("hostel_id", $hostel_id);
        		}
        		if (!empty($building_id)) {
        			$data = $data->where("building_id", $building_id);
        		}
        		if (!empty($floor_id)) {
        			$data = $data->where("floor_id", $floor_id);
        		}
        		if (!empty($room_id)) {
        			$data = $data->where("room_id", $room_id);
        		}
        		$allbed = $data->all();
        		return  view('hostel.bed.bed_search', ['data' => $allbed]);
        	}

	        public function stuBedDetail(Request $request){
		        $data = HostelAssign::select('hostel_assign.*','student.first_name','student.last_name','student.father_name','student.email','student.mobile','student.address','student.aadhaar','hostels.name as hostel_name', 'building.name as building_name', 'floor.name as floor_name', 'room.name as room_name', 'bed.name as bed_name')
			    ->leftjoin('admissions as student', 'student.id', 'hostel_assign.admission_id')
			    ->leftjoin('hostel as hostels', 'hostels.id', 'hostel_assign.hostel_id')
			    ->leftjoin('hostel_building as building', 'building.id', 'hostel_assign.building_id')
			    ->leftjoin('hostel_floor as floor', 'floor.id', 'hostel_assign.floor_id')
			    ->leftjoin('hostel_room as room', 'room.id', 'hostel_assign.room_id')
			    ->leftjoin('hostel_bed as bed', 'bed.id', 'hostel_assign.bed_id')
			    ->where('bed_id', $request->bed_id)->get()->first();
			    if ($data) {
                    $invoice1 = Invoice::where('admission_id', $data->admission_id)->get();
                    $invoice = ''; // Initialize as a string instead of an array
                    foreach ($invoice1 as $val) {
                        $invoice .= '<tr>'; // Start a new table row
                        $invoice .= '<td> <a href="' . url('hostel_invoice/' . $val->invoice_no . '/' . $val->admission_id) . '" target="_blank">' . $val->invoice_no . '</a></td>';
                        $invoice .= '<td>' . $val->total_amount . '</td>';
                        $invoice .= '<td>' . $val->paid_amount . '</td>';
                        $invoice .= '<td>' . $val->due_amount . '</td>';
                        $invoice .= '<td>' . $val->discount . '</td>';
                        $invoice .= '<td>' . $val->hostel_renewal_date . '</td>';
                        $invoice .= '</tr>'; // Close the table row
                    }   
                        $expense1 = StudentExpense::select('student_expenses.*','payment_modes.name as payment_name')
                        ->leftjoin('payment_modes', 'payment_modes.id', 'student_expenses.payment_mode')
                        ->where('hostel_assign_id', $data->id)->get();
                        $expense = ''; // Initialize as a string instead of an array
                        foreach ($expense1 as $val) {
                            $expense .= '<tr>'; // Start a new table row
                            $expense .= '<td>' . $val->expense_date . '</td>';
                            $expense .= '<td>' . $val->payment_name . '</td>';
                            $expense .= '<td>' . $val->expense_name . '</td>';
                            $expense .= '<td>' . $val->expense_amount . '</td>';
                            $expense .= '<td>' . $val->left_amount . '</td>';
                            $expense .= '<td>' . ($val->status == 1 ? 'Paid' : 'Unpaid') . '</td>';
                            $expense .= '</tr>'; // Close the table row
                        }  
                        $response = [
                            'data' => $data,
                            'invoice' => $invoice,
                            'expense' => $expense
                        ];
                        echo json_encode($response);
                        } 
                        else {
                        echo json_encode(['error' => 'No data found']);
                    }
	            }

	            public function schoolStudentSearch(Request $request){
            		$search['name'] = $request->name;
            		$search['admissionNo'] = $request->admissionNo;
    		        $value = $request->name;
    		        if ($request->isMethod('post')) {
			            $request->validate([]);
			            $data = Admission::with('ClassTypes')->where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'));
			            if (!empty($request->name)) {
				            $data = $data->where(function ($query) use ($value) {
        					$query->where('first_name', 'like', '%' . $value . '%');
        					$query->orWhere('mobile', 'like', '%' . $value . '%');
        					$query->orWhere('aadhaar', 'like', '%' . $value . '%');
        					$query->orWhere('email', 'like', '%' . $value . '%');
        					$query->orWhere('father_name', 'like', '%' . $value . '%');
        					$query->orWhere('mother_name', 'like', '%' . $value . '%');
        					$query->orWhere('address', 'like', '%' . $value . '%');
        				});
			        }
        			if (!empty($request->admissionNo)) {
        				$data = $data->where("admissionNo", $request->admissionNo);
        			}
        			if (!empty($request->class_search_id)) {
        				$data = $data->where("class_type_id", $request->class_search_id);
        			}
        			if (!empty($request->section_search_id)) {
        				$data = $data->where("section_id", $request->section_search_id);
        			}
        			$allstudents = $data->orderBy('id', 'DESC')->get();
        		}
        		return  view('hostel.hostel_assign.search_view', ['data' => $allstudents]);
        	}

	        public function hostelExpensesHeadeAdd(Request $request){
		        if ($request->isMethod('post')) {
			        $request->validate([
				        'name' => 'required',
			        ]);
        			$hostel = new Head; //model name
        			$hostel->name = $request->name;
        			$hostel->description = $request->description;
        			$hostel->save();
        			return redirect::to('hostelExpensesHeadeAdd')->with('Hostel')->with('message', 'Head Added Successfully !');
        		}
    	    	$data = Head::get();
    		    return view('hostel.head.add', ['data' => $data]);
    	   }

	        public function hostelExpensesHeadeEdit(Request $request, $id){
		        $data = Head::find($id);
		        if ($request->isMethod('post')) {
			        $request->validate([
				        'name' => 'required',
			        ]);
        			$data->name = $request->name;
        			$data->description = $request->description;
        			$data->save();
			        return redirect::to('hostelExpensesHeadeAdd')->with('message', 'Head Updated Successfully !');
		        }
		        return view('hostel.head.edit', ['data' => $data]);
	        }

	        public function hostelExpensesHeadeDelete(Request $request){
		        $id = $request->delete_id;
		        $building = Head::find($id)->delete();
		        return redirect::to('hostelExpensesHeadeAdd')->with('message', 'Head Deleted Successfully !');
	        }

	        public function meter_unit_update(Request $request){
        		$id = $request->hostel_assign_id;
        		$meter_unit = $request->meter_unit;
		        $building = HostelAssign::where('id', $id)->update(['meter_unit' => $meter_unit]);
	        }
	        
	        public function meter_unit_update_room(Request $request){
		        if ($request->isMethod('post')) {
			        foreach ($request->hostel_room_id as $key => $val) {
				        $old =  HostelMeterUnit::where('hostel_room_id', $val)->where('month_id', $request->month_id)->first();
        				if (!empty($old)) {
        					$hostel = $old;
        				} else {
        					$hostel = new HostelMeterUnit; //model name
        				}
        				$hostel->hostel_room_id = $val;
        				$hostel->meter_unit = $request->meter_unit[$key];
        				$hostel->from_unit = $request->from_unit[$key];
        				$hostel->building_id = $request->building_id[$key];
        				$hostel->hostel_id = $request->hostel_id[$key];
        				$hostel->floor_id = $request->floor_id[$key];
        				$hostel->to_unit = $request->to_unit[$key];
        				$hostel->month_id = $request->month_id;
        				$hostel->save();
			        }
			        return redirect::to('meter_unit')->with('message', 'Meter Unit Added Successfully !');
	    	    }
	        }

	        public function hostelMonthilyAmount(Request $request){
		        $data = HostelAssign::where('hostel_assign.branch_id', Session::get('branch_id'))
			    ->where('admission_id', $request->admission_id)->first();
		        $datade = FeesDetail::where('admission_id',$request->admission_id)->where('fees_type',1)->where('month_id',$request->month_id)->get();
		        $student_unit = 0;
        		foreach ($datade as $val) {
        			$student_unit += $val->total_amount + $val->discount;
        		}
        		if ($student_unit == 0) {
        			return  0;
        		} else {
        			if ($student_unit >= $data['hostel_fees']) {
        				return  $student_unit;
        			} else {
        				$student_unit2 = $data['hostel_fees'] - $student_unit;
        				return  $student_unit2;
        			}
        		}
	        }

	        public function changeAssignStatus(Request $request){
		        if ($request->isMethod('post')) {
			        $student_expense = StudentExpense::where('hostel_assign_id', $request->hostel_assign_id)->where('status', 0)->count();
			        $hostel_fees = HostelAssign::where('id', $request->hostel_assign_id)->first();
			        $carbonDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
    				$data = HostelAssign::where('id', $request->hostel_assign_id)->update([
    					'bed_status' => $request->status == 1 ? 0 : 1,
    					'end_date' => date('Y-m-d')
    				]);
    				$search['hostel_id'] = "";
    				$search['student_details'] = $request->first_name;
    				$dataAll =  HostelAssign::where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
				    $data = HostelAssign::Select('hostel_assign.*', 'hostel.name as hostel_name', 'hostel_building.name as building_name', 'hostel_floor.name as floor_name', 'hostel_room.name as room_name', 'hostel_bed.name as bad_name')
					->leftjoin('hostel as hostel', 'hostel.id', 'hostel_assign.hostel_id')
					->leftjoin('hostel_building as hostel_building', 'hostel_building.id', 'hostel_assign.building_id')
					->leftjoin('hostel_floor as hostel_floor', 'hostel_floor.id', 'hostel_assign.floor_id')
					->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
					->leftjoin('hostel_bed as hostel_bed', 'hostel_bed.id', 'hostel_assign.bed_id')
					->where('hostel_assign.session_id', Session::get('session_id'))->where('hostel_assign.branch_id', Session::get('branch_id'))->where('bed_status', 1)->where('hostel_assign.branch_id', Session::get('branch_id'))->orderBy('hostel_assign.id', 'DESC')->get();
				    //   return view('hostel.hostel_assign.remove',['search'=>$search,'allstudents'=>$dataAll,'data'=>$data]);
				    return redirect::to('hostel_unassign')->with('message', 'Student Unassigned Successfully !');
		        }
	        }
	
	        public function hostelStudentPrint(Request $request,$id){
                $data = HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.dob','admissions.aadhaar','admissions.admissionNo','hostel_bed.name as bed_name','admissions.last_name','admissions.father_mobile as f_mobile','admissions.address as student_address','admissions.father_name','admissions.mother_name','admissions.mobile','admissions.email','sessions.from_year','sessions.to_year','admissions.image as student_image','admissions.father_img','admissions.mother_img')
                ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                ->leftjoin('hostel_bed','hostel_bed.id','hostel_assign.bed_id')
                ->leftjoin('sessions','sessions.id','hostel_assign.session_id')
                ->where('hostel_assign.id',$id)->first();
                $terms = RegistrationTerms::find(1);
                $printPreview = Helper::printPreview('Hostel Student print');
               // dd($printPreview);
                return view($printPreview, ['data'=>$data,'terms'=>$terms]);
                // return view('print_file.hostel.student_addmission_print',['data'=>$data,'terms'=>$terms]);
            }

	        public function hostelUnAssign(Request $request){
        		$search['hostel_id'] = "";
        		$search['student_details'] = $request->student_details;
        		$dataAll =  HostelAssign::select('hostel_assign.*','admissions.first_name','admissions.last_name')
        		->leftjoin('admissions','admissions.id','hostel_assign.admission_id')->where('hostel_assign.branch_id', Session::get('branch_id'))->where('hostel_assign.bed_status', 1)->orderBy('hostel_assign.id', 'DESC')->get();
        		$data = '';
		        if ($request->isMethod('post')) {
			        $data = HostelAssign::Select('hostel_assign.*', 'hostel.name as hostel_name', 'hostel_building.name as building_name', 'hostel_floor.name as floor_name', 'hostel_room.name as room_name', 'hostel_bed.name as bad_name','admissions.first_name','admissions.last_name','admissions.mobile','admissions.father_name')
    				->leftjoin('hostel as hostel', 'hostel.id', 'hostel_assign.hostel_id')
    				->leftjoin('hostel_building as hostel_building', 'hostel_building.id', 'hostel_assign.building_id')
    				->leftjoin('hostel_floor as hostel_floor', 'hostel_floor.id', 'hostel_assign.floor_id')
    				->leftjoin('hostel_room as hostel_room', 'hostel_room.id', 'hostel_assign.room_id')
    				->leftjoin('hostel_bed as hostel_bed', 'hostel_bed.id', 'hostel_assign.bed_id')
    				->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
    				->where('hostel_assign.branch_id', Session::get('branch_id'))->where('hostel_assign.bed_status', 1)->where('hostel_assign.id', $request->student_details)
    				->orderBy('hostel_assign.id', 'DESC')->get();
		            // return view('hostel.hostel_assign.remove',['search'=>$search,'allstudents'=>$dataAll,'data'=>$data]);
		        }
		        return view('hostel.hostel_assign.remove', ['search' => $search, 'allstudents' => $dataAll, 'data' => $data]);
	        }
}
