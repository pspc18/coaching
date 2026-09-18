<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\Setting;
use App\Models\StudentsSidebar;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\WalletDetail;
use App\Models\ForgotOtps;
use App\Models\NewsLetter;
use App\Models\EmailTamplate;
use App\Models\PermissionManagement;
use App\Models\Sidebar;
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


class SidebarController extends BaseController
{

	public function sidebarPermission(Request $request)
	{
	   
	    try{
	        
	          $user_id = $request->user_id;
	          if($user_id == 3){
	              $sidebar = StudentsSidebar::orderBy('id','ASC')->whereNull('deleted_at')->get();
	              $data = array();
                    foreach ($sidebar as $item) {
                        $data[] = array(
                            'id' => $item->id,
                            'name' => $item->name,
                            'hindi_name' => $item->hindi_name,
                            'url' => $item->url,
                            'icon' => trim($item->ican),
                            'apk_icon' => $item->apk_icon,
                            'apk_url' => $item->apk_url,
                            
                        );
                    }
	          }else{
                  $Permisn = PermissionManagement::where('reg_user_id', $user_id)->first();
                    $sidebar_ids = [];
                    if(!empty($Permisn)){
                        foreach(explode(',', $Permisn->sidebar_id) as $info){
                                $sidebar_ids[] =$info;
                        }
                    
                        $sidebar = Sidebar::whereIn('id',$sidebar_ids)->where('id', '!=', 1)->orderBy('id','ASC')->get();
                
                        $data = array();
                            foreach ($sidebar as $item) {
                                $data[] = array(
                                    'id' => $item->id,
                                    'name' => $item->name,
                                    'hindi_name' => $item->hindi_name,
                                    'url' => $item->url,
                                    'icon' => trim($item->ican),
                                    'apk_icon' => $item->apk_icon,
                                    'apk_url' => $item->apk_url,
                                    
                                );
                            }
                    }
	          }

	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}

// 	public function sidebarPermission(Request $request)
// 	{
// 	   // dd($request);
//         $data = new StudentsSidebar;
//         $data->user_id = 1;
//         $data->branch_id = 1;
//         $data->session_id = 3;
//         $data->name = $request->name;
//         $data->hindi_name = $request->hindi_name;
//         $data->url = $request->url; 	 	
//         $data->apk_url = $request->apk_url;
//         $data->ican = $request->icon;
//         $data->apk_icon = $request->apk_icon;
//         $data->save();
        
// 	    return $this->sendResponseData($data, 'success');
// 	}






}