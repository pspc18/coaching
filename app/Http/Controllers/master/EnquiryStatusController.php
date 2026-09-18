<?php

namespace App\Http\Controllers\master;

use App\Models\Master\EnquiryStatus;
use Session;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnquiryStatusController extends Controller
{
    public function add(Request $request){
        $type = $request->get('type','reference'); // default reference
        
        if($request->isMethod('post')){
            $request->validate([
                'name'  => 'required',
            ]);
            
            $EnquiryStatus = new EnquiryStatus;
            $EnquiryStatus->user_id   = Session::get('id');
            $EnquiryStatus->session_id= Session::get('session_id');
            $EnquiryStatus->branch_id = Session::get('branch_id');
            $EnquiryStatus->name      = $request->name;
            $EnquiryStatus->type      = $type;
            $EnquiryStatus->save();

            return response()->json([
                'status' => 'success',
                'message' => ucfirst($type).' added successfully.'
            ]);
        }

        $data = EnquiryStatus::where('type',$type)->whereNull('deleted_at')->get();
        return view('master.EnquiryStatus.reference_add',['data'=>$data,'type'=>$type]);
    }

    public function edit(Request $request,$id){
        $data = EnquiryStatus::find($id);

        if($request->isMethod('post')){
            $request->validate([
                'name'  => 'required',
            ]);

            $data->session_id = Session::get('session_id');
            $data->branch_id  = Session::get('branch_id');
            $data->name       = $request->name;
            $data->save();
            return response()->json([
    'status' => 'success',
    'message' => ucfirst($data->type).' updated successfully.',
    'redirect' => $data->type == 'reference' 
                    ? url('enquiry_status_add') 
                    : url($data->type.'_add')
]);

        }

        return view('master.EnquiryStatus.reference_edit',['data'=>$data,'type'=>$data->type]);
    }
    
    public function delete(Request $request){
    $id = $request->delete_id;
    $record = EnquiryStatus::find($id);
    $type   = $record->type;
    $record->delete();

    if($type == 'reference'){
        return Redirect::to('enquiry_status_add')->with('message', 'Reference deleted successfully.');
    }
    return Redirect::to($type.'_add')->with('message', ucfirst(str_replace('_',' ',$type)).' deleted successfully.');
}

}
