<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Setting;
use App\Models\Admission;
use App\Models\Master\Bus;
use App\Models\Master\BusRoute;
use App\Models\Master\BusRouteAssign;
use App\Models\Master\BusAssign;
use App\Http\Requests;
use App\Models\ClassType;
use App\Models\Master\Branch;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use Session;
use Hash;
use Str;
use Redirect;
use File;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusController extends Controller
{

            public function busDashboard(){
                return view('master.bus.dashboard');
            }
        
            public function busAdd(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'bus_no' => 'required',
                        'owner_no' => 'required',
                        'bus_owmer_name' => 'required',
                        'bus_rigistration_no' => 'required',
                        'bus_photo' => 'required',
                        'bus_company' => 'required',
                        'bus_model_no' => 'required',
                    ]);
                    $bus_photo = '';
                    if ($request->file('bus_photo')) {
                        $image = $request->file('bus_photo');
                        $path = $image->getRealPath();
                        $bus_photo = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_photo);
                    }
                    $bus_rigistration_card = '';
                    if ($request->file('bus_rigistration_card')) {
                        $image = $request->file('bus_rigistration_card');
                        $path = $image->getRealPath();
                        $bus_rigistration_card = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_rigistration_card);
                    }
                    $bus_insurance = '';
                    if ($request->file('bus_insurance')) {
                        $image = $request->file('bus_insurance');
                        $path = $image->getRealPath();
                        $bus_insurance = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_insurance);
                    }
                    $bus_document = '';
                    if ($request->file('bus_document')) {
                        $image = $request->file('bus_document');
                        $path = $image->getRealPath();
                        $bus_document = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_document);
                    }
                    $bus_pollution = '';
                    if ($request->file('bus_pollution')) {
                        $image = $request->file('bus_pollution');
                        $path = $image->getRealPath();
                        $bus_pollution = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_pollution);
                    }
                    $bus_fitness = '';
                    if ($request->file('bus_fitness')) {
                        $image = $request->file('bus_fitness');
                        $path = $image->getRealPath();
                        $bus_fitness = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_fitness);
                    }
                    $bus_speed = '';
                    if ($request->file('bus_speed')) {
                        $image = $request->file('bus_speed');
                        $path = $image->getRealPath();
                        $bus_speed = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_speed);
                    }
                    $bus_permit = '';
                    if ($request->file('bus_permit')) {
                        $image = $request->file('bus_permit');
                        $path = $image->getRealPath();
                        $bus_permit = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_permit);
                    }
                    $bus_gps = '';
                    if ($request->file('bus_gps')) {
                        $image = $request->file('bus_gps');
                        $path = $image->getRealPath();
                        $bus_gps = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_gps);
                    }
                    $bus_camera = '';
                    if ($request->file('bus_camera')) {
                        $image = $request->file('bus_camera');
                        $path = $image->getRealPath();
                        $bus_camera = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_camera);
                    }
                    $addbus = new Bus; //model name
                    $addbus->user_id = Session::get('id');
                    $addbus->session_id = Session::get('session_id');
                    $addbus->branch_id = Session::get('branch_id');
                    $addbus->name = $request->name;
                    $addbus->bus_company = $request->bus_company;
                    $addbus->bus_model_no = $request->bus_model_no;
                    $addbus->bus_no = $request->bus_no;
                    $addbus->bus_owmer_name = $request->bus_owmer_name;
                    $addbus->owner_no = $request->owner_no;
                    $addbus->bus_rigistration_no = $request->bus_rigistration_no;
                    $addbus->capacity_bus = $request->capacity_bus;
                    $addbus->bus_photo = $bus_photo;
                    $addbus->bus_rigistration_card = $bus_rigistration_card;
                    $addbus->bus_insurance = $bus_insurance;
                    $addbus->bus_document = $bus_document;
                    $addbus->bus_pollution = $bus_pollution;
                    $addbus->bus_fitness = $bus_fitness;
                    $addbus->bus_speed = $bus_speed;
                    $addbus->bus_permit = $bus_permit;
                    $addbus->bus_gps = $bus_gps;
                    $addbus->bus_camera = $bus_camera;
                    $addbus->save();
                    return response()->json(['status' => 'success','message' => 'Bus Added Successfully !',]);  
                }
                return view('master.bus.bus.add');
            }
            public function busView(Request $request){
                $allbus = Bus::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'));
                if (Session::get('role_id') > 1) {
                    $data = $allbus->where('branch_id', Session::get('branch_id'));
                }
                $data = $allbus->orderBy('id', 'DESC')->get();
                return view('master.bus.bus.view', ['data' => $data]);
            }
        
            public function busEdit(Request $request, $id){
                $data = Bus::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'bus_company' => 'required',
                        'bus_model_no' => 'required',
                        'bus_no' => 'required',
                        'bus_owmer_name' => 'required',
                        'owner_no' => 'required',
                        'bus_rigistration_no' => 'required',
        
                    ]);
                    if ($request->file('bus_photo')) {
                        $image = $request->file('bus_photo');
                        $path = $image->getRealPath();
                        $bus_photo = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_photo);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_photo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_photo);
                        }
                        $data->bus_photo = $bus_photo;
                    }
                    if ($request->file('bus_rigistration_card')) {
                        $image = $request->file('bus_rigistration_card');
                        $path = $image->getRealPath();
                        $bus_rigistration_card = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_rigistration_card);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_rigistration_card)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_rigistration_card);
                        }
                        $data->bus_rigistration_card = $bus_rigistration_card;
                    }
                    if ($request->file('bus_insurance')) {
                        $image = $request->file('bus_insurance');
                        $path = $image->getRealPath();
                        $bus_insurance = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_insurance);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_insurance)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_insurance);
                        }
                        $data->bus_insurance = $bus_insurance;
                    }
                    if ($request->file('bus_document')) {
                        $image = $request->file('bus_document');
                        $path = $image->getRealPath();
                        $bus_document = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_document);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_document)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_document);
                        }
                        $data->bus_document = $bus_document;
                    }
                    if ($request->file('bus_pollution')) {
                        $image = $request->file('bus_pollution');
                        $path = $image->getRealPath();
                        $bus_pollution = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_pollution);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_pollution)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_pollution);
                        }
                        $data->bus_pollution = $bus_pollution;
                    }
                    if ($request->file('bus_fitness')) {
                        $image = $request->file('bus_fitness');
                        $path = $image->getRealPath();
                        $bus_fitness = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_fitness);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_fitness)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_fitness);
                        }
                        $data->bus_fitness = $bus_fitness;
                    }
                    if ($request->file('bus_speed')) {
                        $image = $request->file('bus_speed');
                        $path = $image->getRealPath();
                        $bus_speed = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_speed);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_speed)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_speed);
                        }
                        $data->bus_speed = $bus_speed;
                    }
                    if ($request->file('bus_permit')) {
                        $image = $request->file('bus_permit');
                        $path = $image->getRealPath();
                        $bus_permit = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_permit);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_permit)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_permit);
                        }
                        $data->bus_permit = $bus_permit;
                    }
                    if ($request->file('bus_gps')) {
                        $image = $request->file('bus_gps');
                        $path = $image->getRealPath();
                        $bus_gps = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_gps);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_gps)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_gps);
                        }
                        $data->bus_gps = $bus_gps;
                    }
                    if ($request->file('bus_camera')) {
                        $image = $request->file('bus_camera');
                        $path = $image->getRealPath();
                        $bus_camera = time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'bus_photo';
                        $image->move($destinationPath, $bus_camera);
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_camera)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $data->bus_camera);
                        }
                        $data->bus_camera = $bus_camera;
                    }
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->name = $request->name;
                    $data->bus_company = $request->bus_company;
                    $data->bus_model_no = $request->bus_model_no;
                    $data->bus_no = $request->bus_no;
                    $data->bus_owmer_name = $request->bus_owmer_name;
                    $data->owner_no = $request->owner_no;
                    $data->bus_rigistration_no = $request->bus_rigistration_no;
                    $data->capacity_bus = $request->capacity_bus;
                    $data->save();
                    return response()->json(['status' => 'success', 'message' => 'Bus Updated Successfully !','redirect' => url('busView')]);
                }
        
                return view('master.bus.bus.edit', ['data' => $data]);
            }
        
            public function busDelete(Request $request){
                $id = $request->delete_id;
                $addbus = Bus::find($id);
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_photo)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_photo);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_rigistration_card)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_rigistration_card);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_insurance)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_insurance);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_document)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_document);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_pollution)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_pollution);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_fitness)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_fitness);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_speed)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_speed);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_permit)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_permit);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_gps)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_gps);
                }
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_camera)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'bus_photo/' . $addbus->bus_camera);
                }
                $addbus->delete();
                return redirect::to('busView')->with('message', 'Bus Delete Successfully.');
            }
            
            public function busRouteAdd(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                    ]);
                    $bus_route = new BusRoute; //model name
                    $bus_route->user_id = Session::get('id');
                    $bus_route->session_id = Session::get('session_id');
                    $bus_route->branch_id = Session::get('branch_id');
                    $bus_route->name = $request->name;
                    $bus_route->description = $request->description;
                    $bus_route->save();
                    return response()->json(['status' => 'success','message' => 'Bus Route Added Successfully !',]);   
                }
                $route_list = BusRoute::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'));
                if (Session::get('role_id') > 1) {
                    $data = $route_list->where('branch_id', Session::get('branch_id'));
                }
                $data = $route_list->orderBy('id', 'DESC')->get();
                return view('master.bus.route.route_add', ['dataview' => $data]);
            }
            
            public function busRouteEdit(Request $request, $id){
                $data = BusRoute::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                    ]);
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->name = $request->name;
                    $data->description = $request->description;
                    $data->save();
                    return response()->json(['status' => 'success', 'message' => 'Bus Route Updated Successfully !','redirect' => url('time_periods')]);
                    return response()->json(['status' => 'success','message' => 'Bus Route Updated Successfully !',]);  
                }
                $route_list = BusRoute::all();
                return view('master.bus.route.route_edit', ['data' => $data, 'dataview' => $route_list]);
            }
            
            public function busRouteDelete(Request $request){
                $id = $request->delete_id;
                $bus_route = BusRoute::find($id)->delete();
                return redirect::to('busRouteAdd')->with('message', 'Bus Route Deleted Successfully !');
            }
        
            public function assignBusRoute(Request $request){
                $route_id = $request->route_id;
                $bus_id = $request->bus_id;
                if ($request->isMethod('post')) {
                    $request->validate([
                        'route_id' => 'required',
                        'bus_id' => 'required',
                    ]);
                    $oldData = BusRouteAssign::where('route_id', $route_id)->where('bus_id', $bus_id)->where('branch_id', Session::get('branch_id'))->get()->first();
                    if (!empty($oldData)) {
                        $assign_route = $oldData;
                    } else {
                        $assign_route = new BusRouteAssign; //model name
                    }
                    $assign_route->user_id = Session::get('id');
                    $assign_route->session_id = Session::get('session_id');
                    $assign_route->branch_id = Session::get('branch_id');
                    $assign_route->route_id = $request->route_id;
                    $assign_route->bus_id = $request->bus_id;
                    $assign_route->save();
                    return response()->json(['status' => 'success','message' => 'Bus & Route Added Successfully !',]);  
                }
                $bus_route_list = BusRouteAssign::with('BusRoute')->with('Bus')->groupBy('route_id')->where('branch_id', Session::get('branch_id'));
                if (Session::get('role_id') > 1) {
                    $data = $bus_route_list->where('branch_id', Session::get('branch_id'));
                }
                $data = $bus_route_list->get();
                return view('master.bus.assignBusRoute', ['dataview' => $data]);
            }
    
            public function assignBusRouteEdit(Request $request, $id){
                $data = BusRouteAssign::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'route_id' => 'required',
                        'bus_id' => 'required',
                    ]);
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->route_id = $request->route_id;
                    $data->bus_id = $request->bus_id;
                    $data->save();
                    return response()->json(['status' => 'success', 'message' => 'Bus & Route Updated Successfully !','redirect' => url('assignBusRoute')]);
                }
                $bus_route_list = BusRouteAssign::with('BusRoute')->with('Bus')->groupBy('route_id')->where('branch_id', Session::get('branch_id'))->get();
                return view('master.bus.assignBusRouteEdit', ['data' => $data, 'dataview' => $bus_route_list]);
            }
        
            public function assignBusRouteDelete(Request $request){
                $id = $request->delete_id;
                $assign_route = BusRouteAssign::find($id)->delete();
                return redirect::to('assignBusRoute')->with('message', 'Bus & Route Deleted Successfully !');
            }
        
            public function assignBusSearchData(Request $request){
                $name = $request->get('name');
                $class_type_id = $request->get('class_type_id');
                $data = Admission::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->with('ClassTypes');
                if (!empty($name)) {
                    $data = $data->where("first_name", $name)->orwhere("last_name", $name)->orwhere("father_name", $name)->orwhere("mother_name", $name)->orwhere("admissionNo", $name)->orwhere("mobile", $name)->orwhere("aadhaar", $name)->orwhere("email", $name);
                }
                if (!empty($class_type_id)) {
                    $data = $data->where("class_type_id", $class_type_id);
                }
                $allstudents = $data->orderBy('id', 'DESC')->get();
                return view('master.bus.assign_Search', ['data' => $allstudents]);
            }
        
            public function assignBus(Request $request, $id){
                $alladmission = Admission::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                $bus = BusRouteAssign::find($id);
                $template = MessageTemplate::Select('message_templates.*', 'message_types.slug')
                    ->leftjoin('message_types', 'message_types.id', 'message_templates.message_type_id')
                    ->where('message_types.status', 1)->where('message_types.slug', 'assignBus')->first();
                $branch = Branch::find(Session::get('branch_id'));
                $setting = Setting::where('branch_id', Session::get('branch_id'))->first();
                if ($request->isMethod('post')) {
                    $student_session_id = $request->student_session_id;
                    $admissionNo = $request->admissionNo;
                    $admission_id = $request->student_id;
                    if (!empty($student_session_id)) {
                        for ($count = 0; $count <= count($student_session_id); $count++) {
                            if (isset($student_session_id[$count])) {
                                $oldData = BusAssign::where('admission_id', $student_session_id[$count])->get()->first();
                                if (!empty($oldData)) {
                                    $busAssign = $oldData;
                                } else {
                                    $busAssign = new BusAssign; //model name
                                }
                                $busAssign->user_id = Session::get('id');
                                $busAssign->session_id = Session::get('session_id');
                                $busAssign->branch_id = Session::get('branch_id');
                                $busAssign->route_id = $request->route_id;
                                $busAssign->bus_id = $request->bus_id;
                                $busAssign->admission_id = $student_session_id[$count];
                                $busAssign->save();
                                $bus = Bus::where('id', $request->bus_id)->first();
                                $student_data = Admission::where('id', $student_session_id[$count])->first();
                                $arrey1 = array(
                                    '{#name#}',
                                    '{#bus_no#}',
                                    '{#driver_name#}',
                                    '{#driver_no#}',
                                    '{#school_name#}'
                                );
                                $arrey2 = array(
                                    $student_data->first_name . ' ' . $student_data->last_name,
                                    $bus->name,
                                    $bus->bus_owmer_name,
                                    $bus->owner_no,
                                    $setting->name
                                );
                                if ($template->status != 1) {
                                    if ($student_data->email != "") {
                                        if ($branch->email_srvc != 0) {
                                            if ($template->email_status != 0) {
                                                $message = str_replace($arrey1, $arrey2, $template->email_content);
                                                $emailData = ['email' => $student_data->email, 'data' => $message, 'subject' => $template->title];
                                                Helper::sendMail('email_print.template_print', $emailData);
                                            }
                                        }
                                    }
                                    // dd($student_session_id[$count]);
                                    if ($branch->whatsapp_srvc != 0) {
                                        if ($student_data->mobile != "") {
                                            if ($template->whatsapp_status != 0) {
                                                $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content);
                                                Helper::sendWhatsappMessage($student_data->mobile, $whatsapp);
                                            }
                                        }
                                    }
                                    if ($branch->sms_srvc != 0) {
                                        if ($student_data->mobile != "") {
                                            if ($template->sms_status != 0) {
                                                $sms = str_replace($arrey1, $arrey2, $template->sms_content);
                                                Helper::SendMessage($student_data->mobile, $sms);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return redirect::to('studentBusView')->with('message', 'Bus Assigned Successfully !');
                    }
                }
                return view('master.bus.assignBus', ['data' => $alladmission, 'dataview' => $bus]);
            }
        
            public function studentBusView(){
                $stuBusView = BusAssign::with('busId')->with('studentId')->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->groupBy('bus_id')->get();
                $stulistbus = BusAssign::Select('bus_assign_students.*', 'admissions.admissionNo', 'admissions.first_name', 'class_types.name as class_name')
                    ->leftjoin('admissions', 'admissions.id', 'bus_assign_students.admission_id')
                    ->leftjoin('class_types', 'class_types.id', 'admissions.class_type_id')
                    ->where('bus_assign_students.branch_id', Session::get('branch_id'))->where('bus_assign_students.session_id', Session::get('session_id'))->orderBy('bus_assign_students.id', 'DESC')->get();
                // $stulistbus = BusAssign::with('busId')->with('studentId')->where('branch_id', Session::get('branch_id'))->where('session_id', Session::get('session_id'))->orderBy('id', 'DESC')->orderBy('bus_id')->get();
                return view('master.bus.studentBusView', ['data' => $stuBusView, 'stulistbus' => $stulistbus]);
            }
        
            public function busAssignEdit(Request $request, $id){
                $data = BusAssign::with('studentId')->where('id', $id)->where('branch_id', Session::get('branch_id'))->get()->first();
                $busRoute = BusRouteAssign::with('bus')->where('route_id', $data['route_id'])->where('branch_id', Session::get('branch_id'))->get();
                if ($request->isMethod('post')) {
                    $request->validate([
                        'route_id' => 'required',
                        'bus_id' => 'required',
                    ]);
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->route_id = $request->route_id;
                    $data->bus_id = $request->bus_id;
                    $data->save();
                    return redirect::to('studentBusView')->with('message', 'Bus Assign Updated Successfully.');
                }
                return view('master.bus.assignBusEdit', ['data' => $data, 'dataview' => $busRoute]);
            }
        
            public function assignBusDelete(Request $request){
                $assign_bus = BusAssign::find($request->delete_id)->delete();
                return redirect::to('studentBusView')->with('message', 'Assigned Bus Deleted Successfully.');
            }
        
            public function studentBusAssignView(Request $request){
                $bus = BusAssign::select('bus_assign_students.*', 'bus.name as busName', 'bus.bus_no', 'bus.bus_owmer_name', 'bus.owner_no', 'bus.bus_photo')
                    ->leftJoin('bus', 'bus.id', 'bus_assign_students.bus_id')
                    ->where('bus_assign_students.admission_id', Session::get('id'))
                    ->where('bus_assign_students.branch_id', Session::get('branch_id'))
                    ->first();
                return view('master.bus.student_bus_assign_view', ['bus' => $bus]);
            }
        
            public function busLateMessage(Request $request){
                $students = Admission::whereIn('id', $request->admission_id)->where('status', 1)->get(['mobile', 'first_name', 'last_name']);
                $template = MessageTemplate::Select('message_templates.*', 'message_types.slug')
                    ->leftjoin('message_types', 'message_types.id', 'message_templates.message_type_id')
                    ->where('message_types.status', 1)->where('message_types.slug', 'birthday-wishes')->first();
                $branch = Branch::find(1);
                $setting = Setting::where('session_id', 3)->where('branch_id', 1)->first();
                $arrey1 = array(
                    '{#name#}',
                    '{#school_name#}'
                );
                if ($branch->whatsapp_srvc == 1) {
                    foreach ($students as $stu) {
                        $arrey2 = array(
                            $stu->first_name . ' ' . $stu->last_name,
                            $setting->name
                        );
                        if ($stu->mobile != "") {
                            $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content);
                            Helper::sendWhatsappMessage(8209949186, $whatsapp);
                        }
                    }
                }
                return redirect::to('studentBusView')->with('message', 'Bus Late Message Sent Successfullay !');
            }

}
