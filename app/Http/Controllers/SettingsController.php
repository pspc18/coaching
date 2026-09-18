<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Setting;
use App\Models\City;
use App\Models\LoginLog;
use App\Models\IPSetting;
use App\Models\CustomVillageList;
use App\Models\Master\Branch;
use App\Models\StudentField;
use Illuminate\Support\Facades\Schema;
use Session;
use Hash;
use Helper;
use File;
use Str;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller

{
            public function settings_dashboard (){
                $settingId = Setting::where('branch_id', Session::get('branch_id'))->value('id') ?? 1;
                return redirect('editSetting/' . $settingId);
            }
            
            public function addSetting(Request $request){
               $branch = Branch ::all();
                    if($request->isMethod('post')){
                        $request->validate([
                            'branch_id'  => 'required',
                        ]);
                  $add_setting = new Setting;//model name
                  $data = Branch::find($request->branch_id);
                        /*if($request->file('right_logo')){
                         $image = $request->file('right_logo');
                        $path = $image->getRealPath();      
                        $right_logo =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'setting/right_logo';
                        $image->move($destinationPath, $right_logo);    
                        $data->right_logo = $right_logo;
                     }
                        if($request->file('left_logo')){
                         $image = $request->file('left_logo');
                        $path = $image->getRealPath();      
                        $left_logo =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'setting/left_logo';
                        $image->move($destinationPath, $left_logo);     
                        $data->left_logo = $left_logo;
                     }
                        if($request->file('seal_sign')){
                         $image = $request->file('seal_sign');
                        $path = $image->getRealPath();      
                        $seal_sign =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'setting/seal_sign';
                        $image->move($destinationPath, $seal_sign);     
                        $data->seal_sign = $seal_sign;
                     }
                     */
            		$add_setting->user_id = Session::get('id'); 
            		$add_setting->session_id = Session::get('session_id');
            		$add_setting->role_id = Session::get('role_id');
                    $add_setting->branch_id = $data->id;
            		$add_setting->name = $data->branch_name;
            		$add_setting->mobile  = $data->mobile;
            		$add_setting->gmail = $data->email;
            		$add_setting->country_id = $data->country_id;
            		$add_setting->state_id = $data->state_id;
            		$add_setting->city_id = $data->city_id;
            		$add_setting->pincode = $data->pin_code;
            		$add_setting->address  = $data->address;
            	    $add_setting->save();
                return redirect::to('editSetting/' . $add_setting->id)->with('message', 'Setting Add Successfully.');     
                }
                return view('settings.setting.addSetting',['branch'=>$branch]);
            }
            
            public function viewSetting(Request $request){
                $settingId = Setting::where('branch_id', Session::get('branch_id'))->value('id') ?? 1;
                return redirect('editSetting/' . $settingId);
            }
            public function editSetting(Request $request, $id) {
                $branch = Branch::orderBy('id')->get();
                $data = Setting::find($id);
                if (!$data) {
                    $settingId = Setting::where('branch_id', Session::get('branch_id'))->value('id') ?? 1;
                    if ($id != $settingId && Setting::find($settingId)) {
                        return redirect('editSetting/' . $settingId)->with('error', 'Setting record not found.');
                    }
                    return redirect('/')->with('error', 'Setting record not found.');
                }
                $getcitys = City::where('state_id', $data->state_id)->get();

                if ($request->isMethod('post')) {
                    $request->validate([
                        'name'                      => 'required',
                        'gmail'                     => 'required',
                        'address'                   => 'required',
                        'mobile'                    => 'required',
                        'pincode'                   => 'required',
                        'current_active_session_id' => 'required',
                    ]);

                    // 1. Watermark Image
                    if ($request->file('watermark_image')) {
                        $image = $request->file('watermark_image');
                        $watermark_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $watermark_image);
                        if (!empty($data->watermark_image) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $data->watermark_image)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $data->watermark_image);
                        }
                        $data->watermark_image = $watermark_image;
                    } elseif ($request->filled('remove_watermark_image') && $request->remove_watermark_image == '1') {
                        if (!empty($data->watermark_image) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $data->watermark_image)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $data->watermark_image);
                        }
                        $data->watermark_image = null;
                    }

                    // 2. Left Logo
                    if ($request->file('left_logo')) {
                        $image = $request->file('left_logo');
                        $left_logo = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/left_logo';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $left_logo);
                        if (!empty($data->left_logo) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $data->left_logo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $data->left_logo);
                        }
                        $data->left_logo = $left_logo;
                    } elseif ($request->filled('remove_left_logo') && $request->remove_left_logo == '1') {
                        if (!empty($data->left_logo) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $data->left_logo)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $data->left_logo);
                        }
                        $data->left_logo = null;
                    }

                    // 3. Seal & Signature
                    if ($request->file('seal_sign')) {
                        $image = $request->file('seal_sign');
                        $seal_sign = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $seal_sign);
                        if (!empty($data->seal_sign) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $data->seal_sign)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $data->seal_sign);
                        }
                        $data->seal_sign = $seal_sign;
                    } elseif ($request->filled('remove_seal_sign') && $request->remove_seal_sign == '1') {
                        if (!empty($data->seal_sign) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $data->seal_sign)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $data->seal_sign);
                        }
                        $data->seal_sign = null;
                    }

                    // 4. Principal Sign
                    if ($request->file('principal_sign')) {
                        $image = $request->file('principal_sign');
                        $principal_sign = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/principal_sign';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $principal_sign);
                        if (!empty($data->principal_sign) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/principal_sign/' . $data->principal_sign)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/principal_sign/' . $data->principal_sign);
                        }
                        $data->principal_sign = $principal_sign;
                    }

                    // 5. Exam Sign
                    if ($request->file('exam_sign')) {
                        $image = $request->file('exam_sign');
                        $exam_sign = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/exam_sign';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $exam_sign);
                        if (!empty($data->exam_sign) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/exam_sign/' . $data->exam_sign)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/exam_sign/' . $data->exam_sign);
                        }
                        $data->exam_sign = $exam_sign;
                    }

                    // 6. Treasurer Sign
                    if ($request->file('treasurer_sign')) {
                        $image = $request->file('treasurer_sign');
                        $treasurer_sign = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'setting/treasurer_sign';
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $image->move($destinationPath, $treasurer_sign);
                        if (!empty($data->treasurer_sign) && File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/treasurer_sign/' . $data->treasurer_sign)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/treasurer_sign/' . $data->treasurer_sign);
                        }
                        $data->treasurer_sign = $treasurer_sign;
                    }

                    // 7. APK Upload
                    if ($request->hasFile('apk')) {
                        $apkFile = $request->file('apk');
                        $apkName = time() . uniqid() . '.' . $apkFile->getClientOriginalExtension();
                        $destinationPath = public_path('APK');
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0755, true);
                        }
                        $apkFile->move($destinationPath, $apkName);
                        if (!empty($data->apk)) {
                            $oldFile = public_path('APK/' . $data->apk);
                            if (File::exists($oldFile)) {
                                File::delete($oldFile);
                            }
                        }
                        $data->apk = $apkName;
                    } elseif ($request->filled('remove_apk') && $request->remove_apk == '1') {
                        if (!empty($data->apk)) {
                            $oldFile = public_path('APK/' . $data->apk);
                            if (File::exists($oldFile)) {
                                File::delete($oldFile);
                            }
                        }
                        $data->apk = null;
                    }

                    $data->user_id    = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->role_id    = Session::get('role_id');
                    if (Session::get('role_id') == 1 && $request->filled('branch_id')) {
                        $data->branch_id = $request->branch_id;
                    }
                    $data->account_id                = $request->account_id;
                    $data->name                      = $request->name;
                    $data->mobile                    = $request->mobile;
                    $data->gmail                     = $request->gmail;
                    $data->country_id                = $request->country_id;
                    $data->loginWithOtp              = $request->loginWithOtp ?? 'No';
                    $data->state_id                  = $request->state_id;
                    $data->firebase_notification     = $request->filled('firebase_notification') ? 1 : 0;
                    $data->city_id                   = $request->city_id;
                    $data->total                     = $request->total;
                    $data->rank                      = $request->rank;
                    $data->test_date                 = $request->test_date;
                    $data->pincode                   = $request->pincode;
                    $data->address                   = $request->address;
                    $data->tin_no                    = $request->tin_no;
                    $data->current_active_session_id = $request->current_active_session_id;
                    $data->save();

                    // Invalidate Settings Cache
                    \Illuminate\Support\Facades\Cache::forget('app_setting_' . ($data->branch_id ?: 1));
                    \Illuminate\Support\Facades\Cache::forget('app_setting_1');

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'status'   => 'success',
                            'message'  => 'Institute Setting Updated Successfully.',
                            'redirect' => url('editSetting/' . $data->id)
                        ]);
                    }
                    return redirect('editSetting/' . $data->id)->with('message', 'Institute Setting Updated Successfully.');
                }
                return view('settings.setting.editSetting', ['data' => $data, 'branch' => $branch, 'getcitys' => $getcitys]);
            } 

             public function deleteSetting(Request $request){
                $id = $request->delete_id;
                $setting = Setting::find($id);
               if (File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $setting->watermark_image)) {
                File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/watermark_image/' . $setting->watermark_image);
                }
             /*  if (File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/right_logo/' . $setting->right_logo)) {
                File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/right_logo/' . $setting->right_logo);
                }*/
               if (File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $setting->left_logo)) {
                File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/left_logo/' . $setting->left_logo);
                }
               if (File::exists(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $setting->seal_sign)) {
                File::delete(env('IMAGE_UPLOAD_PATH') . 'setting/seal_sign/' . $setting->seal_sign);
                }
                 $setting->delete();
                 return redirect::to('viewSetting')->with('message', 'Setting Delete Successfully.');
            }
        
        
            
            
            public function addVillageList(Request $request){
                 if($request->isMethod('post')){
                  $add = new CustomVillageList;//model name
                  $add->name = $request->village_name;
                  $add->save();
                   return redirect::to('editSetting/1')->with('message', 'Village Updated Successfully !');
                 }
             }
           
               public function login_logs(Request $request){
                $loginLogs = LoginLog::select('login_logs.*','role.name as role_name')
                        ->leftJoin('role as role','role.id','login_logs.role_id')
                        ->where('login_logs.branch_id', Session::get('branch_id'))
                        ->where('login_logs.session_id', Session::get('session_id'))
                        ->orderBy('id','DESC')
                        ->get();
              
                return view('settings.login_logs.view',['data'=>$loginLogs]);
               
            }
             public function deleteVillageList(Request $request){
                 if($request->isMethod('post')){
              $delete = CustomVillageList::find($request->delete_id);
              $delete->delete();
                   return redirect::to('editSetting/1')->with('error', 'Village Deleted Successfully !');
                 }
             }
            
         public function SystemStudentField(Request $request){

            $filed = StudentField::where('branch_id', Session::get('branch_id'))->where('type','old')->get();
                
                return view('settings.SystemStudentField.index',['data'=>$filed]);
            }
            
               public function AddStudentField(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'field_label'  => 'required',
                        'field_type'  => 'required',
                        'grid_column'  => 'required',
                        'sort_order'  => 'required',
                        'field_value' => 'required|alpha_dash|max:50', // safe column name
                    ]);
            
                    // ✅ पहले check करो कि admissions table में column exist तो नहीं
                    if (Schema::hasColumn('admissions', $request->field_value)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Field already exists in admissions table!'
                        ]);
                    }
            
                    // अगर column नहीं है तभी save करो
                    $fild = new StudentField; // model name
                    $fild->user_id = Session::get('id'); 
                    $fild->session_id = Session::get('session_id');
                    $fild->branch_id = Session::get('branch_id');
                    $fild->field_label = $request->field_label;
                    $fild->field_name = $request->field_value;
                    $fild->field_type = $request->field_type;
                    $fild->default_value = $request->default_value;
                    $fild->grid_column = $request->grid_column;
                    $fild->field_order = $request->sort_order;
                    $fild->type ='new_input';
                    $fild->required =1;
                    $fild->status =1;
                    $fild->stu_edit_perm =1;
                    $fild->save();
            
                    // ✅ अब column add करो
                    DB::statement("
                        ALTER TABLE `admissions`
                        ADD COLUMN `{$request->field_value}` VARCHAR(100) NULL AFTER `id`
                    ");
            
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Student Field Added Successfully.'
                    ]);
                }
            
                $filed = StudentField::where('branch_id', Session::get('branch_id'))
                            ->where('type','new_input')
                            ->get();
            
                return view('settings.SystemStudentField.add',['data'=>$filed]);
            }

            
            
            public function SystemStudentFieldStatusUpdate(Request $request)
                {
                    $field = StudentField::find($request->student_field_id);
                
                    if(!$field){
                        return response()->json(['success' => false, 'message' => 'Field not found']);
                    }
                
                    if(in_array($request->inputName, ['status','required','stu_edit_perm'])){
                        $field->{$request->inputName} = $request->status;
                        $field->save();
                        return response()->json(['success' => true]);
                    }
                
                    return response()->json(['success' => false, 'message' => 'Invalid field']);
                }
                
                
                public function UpdateStudentFieldLabel(Request $request)
                {
                    $field = StudentField::find($request->student_field_id);
                
                    if(!$field){
                        return response()->json(['success' => false, 'message' => 'Field not found']);
                    }
                
                    $field->field_label = $request->field_label;
                    $field->save();
                
                    return response()->json(['success' => true]);
                }

                
                
                
                public function SystemStudentFieldOrderUpdate(Request $request)
                {
                    $field = StudentField::find($request->student_field_id);
                
                    if(!$field){
                        return response()->json(['success' => false, 'message' => 'Field not found']);
                    }
                
                    $field->field_order = $request->field_order;
                    $field->save();
                
                    return response()->json(['success' => true]);
                }

                        
                public function DeleteStudentField($id)
                    {
                        $field = StudentField::find($id);
                    
                if (!$field) {
                    return response()->json(['success' => false, 'message' => 'Field not found.']);
                }
            
                try {
                   
                    DB::statement("ALTER TABLE `admissions` DROP COLUMN `$field->field_name`");
            
                    // ðŸ”¹ StudentField table se record delete karo
                    $field->delete();
            
                    return response()->json(['success' => true, 'message' => 'Field deleted successfully.']);
            
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()]);
                    }
                }



     
}    