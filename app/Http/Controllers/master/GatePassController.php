<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Master\GatePass;
use App\Models\Admission;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GatePassController extends Controller

{

            public function gate_pass_otp(Request $request){
                if ($request->num != ""){
                    Helper::MessageQueue($request->num,$request->OTP);
                }
            }
            
            public function add(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'student_name'  => 'required',
                        'father_name'  => 'required',
                        'father_mobile'  => 'required',
                        'reciver_name'  => 'required',
                        'reciver_mobile'  => 'required',
                        'relation'  => 'required',
                    ]);
                    $GatePass = new GatePass; //model name
                    $GatePass->user_id = Session::get('id');
                    $GatePass->session_id = Session::get('session_id');
                    $GatePass->branch_id = Session::get('branch_id');
                    $GatePass->admissionNo = $request->admissionID;
                    $GatePass->student_name = $request->student_name;
                    $GatePass->father_name = $request->father_name;
                    $GatePass->father_mobile = $request->father_mobile;
                    $GatePass->otp = $request->otp;
                    $GatePass->iessu_date = $request->iessu_date;
                    $GatePass->reciver_name = $request->reciver_name;
                    $GatePass->reciver_mobile = $request->reciver_mobile;
                    $GatePass->relation = $request->relation;
                    $GatePass->save();
                    return redirect::to('gate_pass_view')->with('message', 'GatePass add Successfully.');
                }
                return view('master.GatePass.add');
            }

            public function view(Request $request){
                $search['admissionNo'] = $request->admissionNo;
                $search['class_type_id'] = $request->class_type_id;
                $data = GatePass::select('gate_passes.*','admissions.first_name','admissions.last_name','admissions.father_name as stu_father_name','admissions.father_mobile as f_mobile','class_types.name as class_name')
                ->leftJoin('admissions','admissions.id','gate_passes.admissionNo')
                ->leftJoin('class_types','class_types.id','admissions.class_type_id')->whereNull('gate_passes.deleted_at');
                if ($request->isMethod('post')) {
                    if (!empty($request->admissionNo)) {
                        $data = $data->where("admissions.admissionNo", $request->admissionNo);
                    }
                    if (!empty($request->class_type_id)) {
                        $data = $data->where("admissions.class_type_id", $request->class_type_id);
                    }
                }
                $data = $data->where('gate_passes.session_id',Session::get('session_id'))
		        ->where('gate_passes.branch_id',Session::get('branch_id'))->orderBy('gate_passes.id','DESC')->get();
                return view('master.GatePass.view', ['data' => $data,'search'=>$search]);
            }

            public function edit(Request $request, $id){
                $data = GatePass::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'student_name'  => 'required',
                        'father_name'  => 'required',
                        'father_mobile'  => 'required',
                        'reciver_name'  => 'required',
                        'reciver_mobile'  => 'required',
                        'relation'  => 'required',
                    ]);
                    $data->student_name = $request->student_name;
                    $data->father_name = $request->father_name;
                    $data->father_mobile = $request->father_mobile;
                    $data->iessu_date = $request->iessu_date;
                    $data->reciver_name = $request->reciver_name;
                    $data->reciver_mobile = $request->reciver_mobile;
                    $data->relation = $request->relation;
                    $data->save();
                    return redirect::to('gate_pass_view')->with('message', 'GatePass Update  Successfully.');
                }
                return view('master.GatePass.edit', ['data' => $data]);
            }

            public function delete(Request $request){
                $id = $request->delete_id;
                $sss = GatePass::find($id)->delete();
                return redirect::to('gate_pass_view')->with('message', 'GatePass  Delete Successfully.');
            }

            public function searchGetpassStudent(Request $request){
                $class_type_id = $request->class_type_id;
                $admissionNo = $request->admissionNo;
                if ($request->isMethod('post')) {
                    //dd($request);
                    $data =  Admission::where('session_id',Session::get('session_id'))
    		        ->where('branch_id',Session::get('branch_id'));
                    if (!empty($admissionNo)) {
                        $data = $data->where("admissionNo", $admissionNo);
                    }
                    if (!empty($class_type_id)) {
                        $data = $data->where("class_type_id", $class_type_id);
                    }
                    if (!empty($country_id)) {
                        $data = $data->where("country_id", $country_id);
                    }
                    if (!empty($state_id)) {
                        $data = $data->where("state_id", $state_id);
                    }
                    if (!empty($city_id)) {
                        $data = $data->where("city_id", $city_id);
                    }
                    $allstudents = $data->orderBy('id', 'DESC')->get();
                }
                return  view('master.GatePass.search', ['data' => $allstudents]);
            }
            
            public function gatePassPrint(Request $request, $id){
                $data = GatePass::select('gate_passes.*', 'admissions.image as student_image','admissions.address as student_address','class_types.name as class_name')
                ->leftJoin('admissions', 'admissions.id', '=', 'gate_passes.admissionNo')
                ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                ->where('admissions.id', $id)->where('gate_passes.session_id',Session::get('session_id'))
                ->where('gate_passes.branch_id',Session::get('branch_id'))
                ->first();
                return view('master.GatePass.print', ['data' => $data]);
            }
    

}
