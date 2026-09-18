<?php

namespace App\Http\Controllers\fees;

use Illuminate\Validation\Validator;
use App\Models\Setting;
use App\Models\fees\FeesCounter;
use Session;
use Helper;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesCounterController extends Controller

{
    
            public function feesCounter(Request $request)  {
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'password' => 'required',
                    ]);
                    $counter = new FeesCounter; //model name
                    $counter->user_id = Session::get('id');
                    $counter->session_id = Session::get('session_id');
                    $counter->branch_id = Session::get('branch_id');
                    $counter->name = $request->name;
                    $counter->password = $request->password;
                    $counter->save();
                    return redirect::to('feesCounterView')->with('message', 'fees Counter Added Successfully !');
                }
                return view('fees.counter.add');
            }
            public function checkAuthentication(Request $request)  {
                if ($request->isMethod('post')) {
                    $data = FeesCounter::where('id',$request->counter_id)->where('password',$request->password)->first();
                    if(!empty($data)){
                        $request->session()->put('counter_name',$data->name);
                        $request->session()->put('fees_counter_id',$data->id);
                        echo "DONE";
                    }
                    else
                    {
                        echo "ERROR";
                    }
                }       
            }
            
            public function feesCounterEdit(Request $request,$id)  {
                $counter = FeesCounter::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        'name' => 'required',
                        'password' => 'required',
                    ]);
                    $counter->user_id = Session::get('id');
                    $counter->session_id = Session::get('session_id');
                    $counter->branch_id = Session::get('branch_id');
                    $counter->name = $request->name;
                    $counter->password = $request->password;
                    $counter->save();
                    return redirect::to('feesCounterView')->with('message', 'fees Counter Edit Successfully !');
                }
                return view('fees.counter.edit',['counter'=>$counter]);
            }
    
            public function feesCounterview(Request $request)  {
                $data = FeesCounter::where('branch_id',Session::get('branch_id'))->orderBy('id','desc')->get();
                return view('fees.counter.view',['data'=>$data]);
            }

            public function feesCounterDelete(Request $request){
                $id = $request->delete_id;
                $data = FeesCounter::find($id)->delete();
                return redirect::to('feesCounterView')->with('message', 'Counter  Deleted Successfully.');
            }
}
