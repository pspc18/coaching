<?php

namespace App\Http\Controllers\hostel;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\hostel\Head;
use App\Models\hostel\Mess;
use App\Models\hostel\HostelExpences;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelMessExpanceController extends Controller
{


            public function messExpanceAdd(Request $request){
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'description' => 'required',
                    ]);
                    $messHead = new Head; //model name
                    $messHead->name = $request->name;
                    $messHead->description = $request->description;
                    $messHead->type = 'mess';
                    $messHead->save();
                    return redirect::to('messExpanceAdd')->with('message', 'Mess Head Added Successfully !');
                }
                $data = Head::where('type', 'mess')->get();
                return view('hostel.mess_head.add', ['data' => $data]);
            }
            public function messExpanceEdit(Request $request, $id){
                // dd($request);
                $data = Head::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'description' => 'required',
                    ]);
                    $data->name = $request->name;
                    $data->description = $request->description;
                    $data->type = 'mess';
                    $data->save();
                    return redirect::to('messExpanceAdd')->with('message', 'Mess Head Updated Successfully !');
                }
                return view('hostel.mess_head.edit', ['data' => $data]);
            }
    
            public function messExpanceDelete(Request $request){
                $id = $request->delete_id;
                $building = Head::find($id)->delete();
                return redirect::to('messExpanceAdd')->with('message', 'Mess Head Deleted Successfully !');
            }
}