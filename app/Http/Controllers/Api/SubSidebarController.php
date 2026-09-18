<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\Admission;
use App\Models\BillCounter;
use App\Models\UserDocument;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\WalletDetail;
use App\Models\ForgotOtps;
use App\Models\NewsLetter;
use App\Models\EmailTamplate;
use App\Models\Sidebar;
use App\Models\SidebarSub;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class SubSidebarController extends BaseController
{

	public function subSidebar(Request $request){
	    if($request->isMethod('post')){
	        try{
	            $subsidebar = SidebarSub::where('sidebar_id',$request->sidebar_id)->where('app_item_show',1)->get();
	            
	            $list = array();
	            $colors = ['453343','245444','3498db','9b59b6','34495e','16a085','27ae60','2980b9','8e44ad','2c3e50','f1c40f','e67e22','e74c3c','9b59b6','95a5a6','f39c12','d35400'
                            ,'c0392b','bdc3c7','7f8c8d'];
                            
                foreach ($subsidebar as $key => $item) {
                    $randomNumber = rand(0, 19);
                    $list[] = array(
                        'key' => $key+1,
                        'name' => $item->name,
                        'color_code' => '#'.$colors[$randomNumber],
                        'navigation'=>$item->url
                    );
                }
	            return response()->json(['status' => true, 'message' => 'Data Found','data'=>$list], 200);
	        }
	        catch (Exception $e) {
                return $this->sendError('Validation Error.', 'Error');
            }
	    }
	}
}