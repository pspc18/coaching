<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;

use Validator;
use Carbon;
use Str;
use App\Helpers\helpers;


class ExpenseController extends BaseController
{
    
 public function expenseView(Request $request)
    {

      


        $data = Expense::where('session_id', 3);
        
        // if(Session::get('role_id') > 1){
        //     $data = $data->where('branch_id', 1);
        // }

        if ($request->isMethod('post')) {

            if (!empty($request->name)) {
                $value = $request->name;
                $data = $data->where('name', 'LIKE', '%' . $value . '%');
            }

            if (!empty($request->from_date)) {
                $data = $data->whereBetween('date', [$request->from_date, $request->to_date]);
            }
        }


        //$all = $data->orderBy('id', 'ASC')->whereNull('deleted_at')->get();
        $data = $data->orderBy('id', 'ASC')->whereNull('deleted_at')->get();
        
        
         $list = array();
            foreach ($data as $key => $item) {
                $list[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->name,
                    'date' => $item->date,
                    'rate' => $item->rate,
                    'quantity' => $item->quantity,
                    'description' => $item->description,
                    'attachment' => env('IMAGE_SHOW_PATH').'expense/'.$item->attachment,
                   
                );
            }
return response()->json(['status' => true, 'message' => 'Enquiry Deleted Successfully' ,'data' => $list], 200);

    }


   public function expenseEdit(Request $request)
    {
        $data_request = json_decode($request->data);

        $data = Expense::find($data_request->id);
       if ($request->isMethod('post')) {
            $request->validate([

                //  'name' => 'required',    
                //  'date' => 'required',    
                //  'amount' => 'required',    
                //  'total_amt' => 'required',    

            ]);

         if ($request->hasfile('attachment')) {
                $image = $request->file('attachment');
                $path = $image->getRealPath();
                $attachment =  time() . uniqid() . $image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'expense';
                $image->move($destinationPath, $attachment);
                //  if (File::exists(env('IMAGE_UPLOAD_PATH') . 'expense/' . $data->attachment)) {
                //     File::delete(env('IMAGE_UPLOAD_PATH') . 'expense/' . $data->attachment);
                //     }
                $data->attachment = $attachment;
            }

            $data->user_id = 1;
            $data->session_id = 3;
            $data->branch_id = 1;
            $data->role_id = 1;
            $data->name = $data_request->name;
            $data->date = $data_request->date;
            $data->quantity = $data_request->quantity;
            $data->rate = $data_request->rate;
             $data->amount = $data_request->quantity*$data_request->rate;
                    $data->total_amt = $data_request->quantity*$data_request->rate;
            $data->description = $data_request->description;
        
            $data->save();

            return response()->json(['status' => true, 'message' => 'Expense Updated Successfully','data'=>$data], 200);

         }

      
    }
    
      public function expenseAdd(Request $request)
    {

$data_request = json_decode($request->data);
        if ($request->isMethod('post')) {

            $request->validate([

                //  'name' => 'required',    
                //  'date' => 'required',    
                //  'amount' => 'required',    
                //  'total_amt' => 'required',    

            ]);
                    $attachment = '';
                    if ($request->hasfile('attachment')) {
                        $image = $request->file('attachment');
                        $path = $image->getRealPath();
                        $attachment =  time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'expense';
                        $image->move($destinationPath, $attachment);
                    }

                    $add = new Expense; //model name
                    $add->user_id = 1;
                    $add->session_id = 3;
                    $add->branch_id = 1;
                    $add->role_id = 1;
                    $add->name = $data_request->name;
                    $add->date = $data_request->date;
                    $add->quantity = $data_request->quantity;
                    $add->rate = $data_request->rate;
                    $add->amount = $data_request->quantity*$data_request->rate;
                    $add->total_amt = $data_request->quantity*$data_request->rate;
                   $add->attachment = $attachment;
                    $add->description = $data_request->description;
                    $add->save();
                   
                  }
              return response()->json(['status' => true, 'message' => 'Expense Added Successfully'], 200);
            }

public function expenseDelete(Request $request)
	{
	    try{
	   
	   $delete_id = $request->delete_id;
	   
	   $delete_data = Expense::where('id',$delete_id)->delete();
           
	   return response()->json(['status' => true, 'message' => 'Expense Deleted Successfully'], 200);
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
	
	
	 public function test(Request $request)
    {
        //  $add = new Expense; //model name
        //             $add->user_id = 1;
        //             $add->session_id = 3;
        //             $add->branch_id = 1;
        //             $add->role_id = 1;
        //             $add->description = $request->description;
        //             $add->name = $request->name;
                    
        //             $add->save();
                   
                
	   
	   $data = Expense::orderBy('id','DESC')->get();

	
	     $list = array();
            foreach ($data as $key => $sdfglskfj) {
                
              
                $list[] = array(
                    'key' => $key+1,
                    'name'=>$sdfglskfj->name
                   
                );
                
            }
           
	   return response()->json(['status' => true, 'message' => ' Data Found','data'=>$list], 200);
    }
}