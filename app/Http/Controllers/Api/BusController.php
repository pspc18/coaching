<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\BusAssign;
use Validator;
use Hash;
use Session;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;

class BusController extends BaseController
{
 public function busAssigned(Request $request){
     
     $admission_id = $request->admission_id;
        $bus = BusAssign::select('bus_assign_students.*','bus.name as busName','bus.bus_no','bus.bus_owmer_name','bus.owner_no','bus.bus_photo')
                            ->leftJoin('bus','bus.id','bus_assign_students.bus_id')
                            ->where('bus_assign_students.admission_id',$admission_id)
                            ->first();
  if(!empty($bus))
            {
              return response()->json(['status' => true, 'message' => 'Success','data'=>$bus], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','data'=>[]], 200);
            }
    }
}
 
 
 

    
    


