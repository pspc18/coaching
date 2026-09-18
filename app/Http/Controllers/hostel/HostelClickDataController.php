<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\hostel\Hostel;
use App\Models\hostel\HostelBuilding;
use App\Models\hostel\HostelFloor;
use App\Models\hostel\HostelRoom;
use App\Models\hostel\HostelBed;
use App\Models\hostel\HostelAssign;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelClickDataController extends Controller

{


	public function dataBuilding(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelBuilding::with('Hostel')->where('hostel_id', $id)->get();
			$hostelData = "";
			foreach ($data as $key => $type) {
				$hostelData .= '<div class="icheck-primary">
                    <input class="building" id="building' . $key . '" name="building_id" type="radio" value="' . $type->id . '" data-value="' . $type->id . '">
                    <label for="building' . $key . '" >' . $type->name . '</label>
                </div>';
			}
			echo $hostelData;
		}
	}

	public function dataFloor(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelFloor::with('HostelBuilding')->where('building_id', $id)->get();
			$hostelData = "";
			foreach ($data as $key => $type) {
				$hostelData .= '<div class="icheck-primary">
                    <input class="floor" id="floor' . $key . '" name="floor_id" type="radio" value="' . $type->id . '" data-value="' . $type->id . '">
                    <label for="floor' . $key . '" >' . $type->name . '</label>
                </div>';
			}
			echo $hostelData;
		}
	}


	public function dataRoom(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelRoom::with('HostelFloor')->where('floor_id', $id)->get();
			$hostelData = '';
			foreach ($data as $key => $type) {
				$hostelData .= '<div class="icheck-primary">
                    <input class="room" id="room' . $key . '" name="room_id" type="radio" value="' . $type->id . '" data-value="' . $type->id . '">
                    <label for="room' . $key . '" >' . $type->name . '</label>
                </div>';
			}
			echo $hostelData;
		}
	}


	public function dataBed(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelBed::with('HostelRoom')->where('room_id', $id)->get();


			$hostelData = '';
			foreach ($data as $key => $type) {
				$alredyAssign = HostelAssign::where('bed_id', $type->id)->where('bed_status', 1)->get('bed_id')->first();

				if (!empty($alredyAssign)) {
					$hostelData .= '<div class="col-2 col-md-1 btn btn-danger btn-xs m-1 studentDetail formHide"  data-id="' . $type->id . '">
                    <input class="bed" id="bed' . $key . '" disabled data-value="' . $type->id . '">
                    <i class="fa fa-bed"></i><br><label class="" for="bed' . $key . '" >' . $type->name . '</label>
                </div>';
				} else {

					$hostelData .= '<div class="col-2 col-md-1 btn btn-success btn-xs m-1 formShow" data-id="' . $type->id . '">
                    <input class="bed" id="bed' . $key . '" value="' . $type->id . '"   data-value="' . $type->id . '">
                    <i class="fa fa-bed"></i><br><label for="bed' . $key . '" class="">' . $type->name . '</label>
                </div>';
				}
			}
			echo $hostelData;
		}
	}

	public function hostelData(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelBuilding::with('Hostel')->where('hostel_id', $id)->get();
			$hostelData = '<option value="">Select</option>';
			foreach ($data as $type) {
				$hostelData .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $hostelData;
		}
	}

	public function hostelDataSearch(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelBuilding::with('Hostel')->where('hostel_id', $id)->get();
			$hostelDataSearch = '';
			foreach ($data as $type) {
				$hostelDataSearch .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $hostelDataSearch;
		}
	}

	public function buildingData(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelFloor::with('HostelBuilding')->where('building_id', $id)->get();
			$buildingData = '<option value="">Select</option>';
			foreach ($data as $type) {
				$buildingData .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $buildingData;
		}
	}

	public function floorData(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelRoom::with('HostelFloor')->where('floor_id', $id)->get();
			$floorData = '<option value="">Select</option>';
			foreach ($data as $type) {
				$floorData .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $floorData;
		}
	}

	public function roomData(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = HostelBed::with('HostelRoom')->where('room_id', $id)->get();
			$roomData = '<option value="">Select</option>';
			foreach ($data as $type) {
				$roomData .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $roomData;
		}
	}
	public function studentsData(Request $request, $id)
	{
		$data = array();
		if (!empty($id)) {
			$data = Admission::where('class_type_id', $id)->get();
			$studentData = '<option value="">Select</option>';
			foreach ($data as $type) {
				$studentData .= '
               <option value="' . $type['id'] . '">' . $type['name'] . '</option>';
			}
			echo $studentData;
		}
	}
	
	
	
	
	
	
	
	
	
	public function getBuilding(Request $request, $id){
	    $building = HostelBuilding::where('hostel_id', $id)->get();
	    return view('hostel.hostel_assign.appendBuilding', ['building' => $building]);
	}
	
	public function getFloor(Request $request, $id){
	    $floor = HostelFloor::where('building_id', $id)->get();
	    return view('hostel.hostel_assign.appendFloor', ['floor' => $floor]);
	}
	public function getbed(Request $request, $id){
	    $bed = HostelBed::where('room_id', $id)->get();
	    return view('hostel.hostel_assign.appendbed', ['bed' => $bed]);
	}
	
	
	
}