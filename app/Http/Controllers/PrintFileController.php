<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\PrintFileModule;
use App\Models\PrintFileSubModule;
use App\Models\PrintFileDetails;
use App\Models\PrintFileSetting;
use Helper;
use Session;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrintFileController extends Controller

{
            public function printFilePanel(Request $request){
                $data = PrintFileModule::orderBy('name','ASC')->get();
                if($request->isMethod('post')){
                    if(!empty($request->id)){
                        foreach($request->id as $key =>$item){
                            $old = PrintFileModule::find($item);
                            $old->name_show = $request->module[$key];
                            $old->save();
                        }
                    }
                    return redirect::to('printFilePanel')->with('message', 'Module Edited Successfully !');
                }
                return view('master.printFilePanel.view',['data'=>$data]);
            }
    
            public function printFileModuleWiseView(Request $request,$id){
                $data = PrintFileSubModule::where('print_file_modules_id',$id)->get();
                if($request->isMethod('post')){
                    foreach($request->print_file_id as $key => $item){
                        $old = PrintFileSetting::where('print_file_sub_modules_id',$key)
                        // ->where('print_file_details_id',$item[0])
                        ->where('branch_id',Session::get('branch_id'))
                        ->where('session_id',Session::get('session_id'))
                        ->first();
                        if(!empty($old)){
                            $old->print_file_details_id = $item[0];
                            $old->save();
                        }
                        else{
                            $data = new PrintFileSetting;
                            $data->user_id = Session::get('id');
                            $data->branch_id =  Session::get('branch_id');
                            $data->session_id = Session::get('session_id');
                            $data->print_file_sub_modules_id = $key;
                            $data->print_file_details_id = $item[0];
                            $data->save();
                        }
                    }
                    return redirect::to('printFilePanel')->with('message', 'Print File Added Successfully !');
                }
                return view('master.printFilePanel.moduleWise',['data'=>$data,'id'=>$id]); 
            }
    
            public function template(Request $request,$id){
                $data = PrintFileDetails::Select('print_file_details.*','print_file_modules.name as print_file_modules_name' )
                ->leftjoin('print_file_modules','print_file_modules.id', 'print_file_details.print_file_modules_id')->where('print_file_details.id',$id)->first();
                $module_name = str_replace(' ', '', $data->print_file_modules_name);
                return env('IMAGE_SHOW_PATH').'print_file_samples/'.$module_name.'/'.$data->name.'.jpg';
                //return view('master.printFilePanel.'.$module_name.'.'.$data->name,['data'=>$data]);
            }

}
