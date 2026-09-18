<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Role;
use App\Models\PermissionManagement;
use App\Models\BillCounter;
use App\Models\AttendanceStatus;
use App\Models\Master\TeacherSubject;
use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\Master\MessageTemplate;
use App\Models\City;
use App\Models\Master\Branch;
use App\Models\Master\MessageContent;
use App\Jobs\Job;
use Session;
use Hash;
use Str;
use Helper;
use File;
use Redirect;
use Auth;
use DB;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
class UserController extends Controller

{


    
            public function user_dashboard(){
                return view('user/usre_dashboard');
            }
        

    
    


            public function addUser(Request $request)
            {
                $BillCounter = BillCounter::where('type','User')
                ->where('branch_id', Session::get('branch_id'))
                ->where('session_id', Session::get('session_id'))
                ->first();
            
                        if (!$BillCounter) {
                            $BillCounter = new BillCounter();
                            $BillCounter->type = 'User';
                            $BillCounter->branch_id = Session::get('branch_id');
                            $BillCounter->session_id = Session::get('session_id');
                            $BillCounter->counter = 1; // default
                            $BillCounter->save();
                        }
            
            
                $branch = Branch::all();
            
                if ($request->isMethod('post')) {
                    
                    /* ================= VALIDATION ================= */
                    $request->validate([
                        'first_name' => 'required',
                        'mobile' => 'required|digits:10',
                        'state' => 'required',
                        'city' => 'required',
                         'userName' => 'required|unique:users,userName,NULL,id,deleted_at,NULL',
                        'password' => 'required|min:4',
                        'role_id' => 'required',
                        'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                        'address' => 'required',
                        'access_branch_id' => 'required|array',
                        'dob' => 'required|date|before:today',
                        'joining_date' => 'required|date',
                        'joining_amount' => 'nullable|numeric|min:0',
            
                    ]);
            
                  
                      

                        $user_profile ='';
                        if($request->file('photo')){
                            $image = $request->file('photo');
                            $path = $image->getRealPath();      
                            $user_profile =  time().uniqid().$image->getClientOriginalName();
                            $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
                            $image->move($destinationPath, $user_profile);     
                        }
                        
                        
                     
                        $Id_proof_img = null;
                        if ($request->hasFile('id_proof')) {
                            $Id_proof_img = Helper::uploadImageUniversal(
                                $request->file('id_proof'),
                                'documents/id_proof',   // ID Proof Folder
                                $request->first_name,
                                $request->first_name.'_id_proof',
                                false
                            );
                        }
                        
                        
                       
                        $qualification_proof_img = null;
                        if ($request->hasFile('qualification_proof')) {
                            $qualification_proof_img = Helper::uploadImageUniversal(
                                $request->file('qualification_proof'),
                                'documents/qualification',   // Qualification Folder
                                null,
                                $request->first_name.'_qualification',
                                false
                            );
                        }
                        
                        
                       
                        $experience_letter = null;
                        if ($request->hasFile('experience_letter')) {
                            $experience_letter = Helper::uploadImageUniversal(
                                $request->file('experience_letter'),
                                'documents/experience',   // Experience Folder
                                null,
                                $request->first_name.'_experience',
                                false
                            );
                        }

                    $maxBiomax = User::selectRaw('MAX(CAST(id AS UNSIGNED)) as max_id')
                            ->value('max_id');
                    /* ================= USER SAVE ================= */
                    $add_user = new User();
                    $add_user->session_id = Session::get('session_id');
                    $add_user->branch_id = Session::get('branch_id');
                    if (!empty($request->access_branch_id) && is_array($request->access_branch_id)) {
                        $add_user->access_branch_id = implode(',', $request->access_branch_id);
                    }

                    $add_user->attendance_unique_id = 'aiu'.$maxBiomax + 1;                       
                    $add_user->userName = $request->userName;
                    $add_user->first_name = $request->first_name;
                    $add_user->last_name = $request->last_name;
                    $add_user->city_id = $request->city;
                    $add_user->state_id = $request->state;
                    $add_user->mobile = $request->mobile;
                    $add_user->address = $request->address;
                    $add_user->role_id = $request->role_id;
                    $add_user->email = $request->email;
                    $add_user->password = Hash::make($request->password);
                    $add_user->confirm_password  = $request->password;
                    $add_user->image = $user_profile;
                    $add_user->status = 1;
                    $add_user->joining_amount = $request->joining_amount;
                    $add_user->dob = $request->dob;
                    $add_user->joining_date = $request->joining_date;
                    $add_user->Id_proof_img = $Id_proof_img;
                    $add_user->qualification_proof_img = $qualification_proof_img;
                    $add_user->experience_letter_img = $experience_letter;
                    $add_user->pan_card = $request->pan_card;
                    $add_user->bank = $request->bank;
                    $add_user->account_no = $request->account_no;
                    $add_user->ifsc_code = $request->ifsc_code;
                    $add_user->class_type_id = $request->class_type_id;
            
                    $add_user->save();
                    $add_user->syncAttendanceUniqueId();
                    $user_id = $add_user->id;
            
                    /* ================= ROLE PERMISSIONS ================= */
                    $rolePermissions = DB::table('role_permissions')
                        ->where('role_id', $request->role_id)
                        ->get();
            
                    if ($rolePermissions->isNotEmpty()) {
                        $insertData = [];
                        foreach ($rolePermissions as $perm) {
                            $insertData[] = [
                                'user_id' => $user_id,
                                'sidebar_id' => $perm->sidebar_id,
                                'sidebar_name' => $perm->sidebar_name ?? '',
                                'sub_sidebar_id' => $perm->sub_sidebar_id ?? null,
                                'add' => $perm->add ?? 0,
                                'edit' => $perm->edit ?? 0,
                                'view' => $perm->view ?? 0,
                                'delete' => $perm->delete ?? 0,
                                'print' => $perm->print ?? 0,
                                'status' => $perm->status ?? 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::table('user_permission')->insert($insertData);
                    }
            
                    /* ================= WHATSAPP ================= */
                    $template = MessageTemplate::select(
                            'message_templates.*',
                            'message_types.slug',
                            'message_types.status as message_type_status'
                        )
                        ->leftJoin('message_types','message_types.id','message_templates.message_type_id')
                        ->where('message_types.slug','User')
                        ->first();
            
                    $branchData = Branch::find(Session::get('branch_id'));
                    $setting = Setting::where('branch_id', Session::get('branch_id'))->first();
                    $role_name = Role::find($request->role_id);
            
                    if ($template && $template->status != 1) {
                        if ($template->message_type_status == 1 && $branchData->whatsapp_srvc == 1) {
            
                            $arrey1 = [
                                '{#name#}','{#user_name#}','{#password#}','{#email#}',
                                '{#role#}','{#address#}','{#mobile_no#}',
                                '{#support_email#}','{#school_name#}','{#support_no#}'
                            ];
            
                            $arrey2 = [
                                $request->first_name.' '.$request->last_name,
                                $request->userName,
                                $request->password,
                                $request->email,
                                $role_name->name ?? '',
                                $request->address,
                                $request->mobile,
                                $setting->gmail ?? '',
                                $setting->name ?? '',
                                $setting->mobile ?? ''
                            ];
            
                            $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content);
                            Helper::MessageQueue($request->mobile, $whatsapp);
                        }
                    }
            
                    return response()->json([
                        'status' => 'success',
                        'message' => 'User Added Successfully.'
                    ]);
                }
            
                return view('user.users.add', [
                    'branch' => $branch,
                    
                ]);
            }

            public function viewUser(Request $request){
                $branchId = (int) Session::get('branch_id');
                $sessionId = (int) Session::get('session_id');
                $userRoleId = (int) Session::get('role_id');

                $page = max(1, (int) $request->input('page', 1));
                $perPageRaw = $request->input('per_page', 25);
                $perPage = ($perPageRaw === 'all') ? 'all' : (in_array((int) $perPageRaw, [10, 25, 50, 100], true) ? (int) $perPageRaw : 25);

                $search = [
                    'name' => trim((string) $request->input('name', '')),
                    'role_id' => $request->filled('role_id') ? (int) $request->input('role_id') : '',
                    'branch_id' => $request->filled('branch_id') ? (int) $request->input('branch_id') : '',
                    'mobile' => trim((string) $request->input('mobile', '')),
                    'email' => trim((string) $request->input('email', '')),
                    'userName' => trim((string) $request->input('userName', '')),
                    'status' => $request->has('status') && $request->input('status') !== '' ? $request->input('status') : 1,
                ];

                // Single query for overall stats
                $statsQuery = DB::table('users')->whereNull('deleted_at');
                if ($userRoleId > 1) {
                    $statsQuery->where('branch_id', $branchId);
                }
                $rawStats = $statsQuery->selectRaw("
                    COUNT(*) as total,
                    COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) as `active`,
                    COALESCE(SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END), 0) as `inactive`,
                    COALESCE(SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END), 0) as `dropped`
                ")->first();

                $userStats = [
                    'total' => (int) ($rawStats->total ?? 0),
                    'active' => (int) ($rawStats->active ?? 0),
                    'inactive' => (int) ($rawStats->inactive ?? 0),
                    'dropped' => (int) ($rawStats->dropped ?? 0),
                ];

                // Build filtered users query
                $users = User::select('users.*', 'branch.branch_name')
                    ->leftJoin('branch', 'branch.id', '=', 'users.branch_id')
                    ->with('roleName')
                    ->whereNull('users.deleted_at');

                if ($userRoleId > 1) {
                    $users->where('users.branch_id', $branchId);
                }

                if (!empty($search['branch_id'])) {
                    $users->where('users.branch_id', (int) $search['branch_id']);
                }

                if (!empty($search['role_id'])) {
                    $users->where('users.role_id', (int) $search['role_id']);
                }

                if ($search['status'] !== '' && $search['status'] !== 'all') {
                    $users->where('users.status', (int) $search['status']);
                }

                if (!empty($search['name'])) {
                    $val = $search['name'];
                    $users->where(function ($query) use ($val) {
                        $query->where('users.first_name', 'like', '%' . $val . '%')
                              ->orWhere('users.last_name', 'like', '%' . $val . '%')
                              ->orWhere('users.mobile', 'like', '%' . $val . '%')
                              ->orWhere('users.email', 'like', '%' . $val . '%')
                              ->orWhere('users.userName', 'like', '%' . $val . '%')
                              ->orWhere('users.attendance_unique_id', 'like', '%' . $val . '%')
                              ->orWhere('users.father_name', 'like', '%' . $val . '%')
                              ->orWhere('users.address', 'like', '%' . $val . '%')
                              ->orWhereRaw("CONCAT_WS(' ', users.first_name, users.last_name) LIKE ?", ['%' . $val . '%']);
                    });
                }

                if (!empty($search['mobile'])) {
                    $users->where('users.mobile', 'like', '%' . $search['mobile'] . '%');
                }

                if (!empty($search['email'])) {
                    $users->where('users.email', 'like', '%' . $search['email'] . '%');
                }

                if (!empty($search['userName'])) {
                    $users->where('users.userName', 'like', '%' . $search['userName'] . '%');
                }

                $totalCount = $users->count();
                $users->orderBy('users.id', 'DESC');

                if ($perPage === 'all') {
                    $paginated = $users->get();
                    $startIndex = 0;
                    $lastPage = 1;
                } else {
                    $paginated = $users->forPage($page, $perPage)->get();
                    $startIndex = ($page - 1) * $perPage;
                    $lastPage = max(1, (int) ceil($totalCount / $perPage));
                }

                $branchLookup = DB::table('branch')->whereNull('deleted_at')->pluck('branch_name', 'id')->all();
                $roles = Helper::roleType();
                $branches = Helper::getAllBranch();

                if ($request->ajax() || $request->wantsJson() || $request->input('ajax') == '1') {
                    $html = view('user.users.table_rows', [
                        'data' => $paginated,
                        'startIndex' => $startIndex,
                        'branchLookup' => $branchLookup,
                    ])->render();

                    return response()->json([
                        'status' => true,
                        'html' => $html,
                        'total' => $totalCount,
                        'from' => $totalCount > 0 ? $startIndex + 1 : 0,
                        'to' => $perPage === 'all' ? $totalCount : min($startIndex + count($paginated), $totalCount),
                        'current_page' => $page,
                        'last_page' => $lastPage,
                        'per_page' => $perPage,
                        'stats' => $userStats,
                    ]);
                }

                return view('user.users.view', [
                    'data' => $paginated,
                    'search' => $search,
                    'userStats' => $userStats,
                    'branchLookup' => $branchLookup,
                    'role' => $roles,
                    'branch' => $branches,
                    'totalCount' => $totalCount,
                    'currentPage' => $page,
                    'perPage' => $perPage,
                    'lastPage' => $lastPage,
                    'startIndex' => $startIndex,
                ]);
            }
    
            public function editUser(Request $request,$id){
                $branch = Branch::all();
                $data = User::find($id);
                $getcitie = City::where('state_id',$data['state_id'])->get();
                if($request->isMethod('post')){
                    $request->validate([
                    'userName' => 'required|unique:users,userName,'.$id.',id,deleted_at,NULL',
                     'first_name' => 'required',
                    'mobile' => 'required|digits:10',
                    'state' => 'required',
                    'city' => 'required',
                    'password' => 'required|same:confirm_password|min:4',
                    'confirm_password' => 'required|min:4',
                    'email' => 'required|unique:users,email,'.$id.',id,deleted_at,NULL',
                    'address' => 'required',
                    'dob' => 'required|date|before:today',
                    'joining_date' => 'required|date',
                    'joining_amount' => 'nullable|numeric|min:0',
                ]);

                
                   
                    
                        if($request->file('photo')){
                            $image = $request->file('photo');
                            $path = $image->getRealPath();      
                            $photo =  time().uniqid().$image->getClientOriginalName();
                            $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
                            $image->move($destinationPath, $photo);  
                            if(File::exists(env('IMAGE_UPLOAD_PATH').'profile/'.$data->image)){
                                    File::delete(env('IMAGE_UPLOAD_PATH').'profile/'.$data->image);
                                }      
                            $data->image = $photo;
                        } 
                        
                        $Id_proof_img = null;
                        if ($request->hasFile('id_proof')) {
                            $Id_proof_img = Helper::uploadImageUniversal(
                                $request->file('id_proof'),
                                'documents/id_proof',   // ID Proof Folder
                                $data->Id_proof_img,// Old image (if updating)
                                $request->first_name.'_id_proof', // File name
                                false // Branch wise folder (true/false)
                            );
                        $data->Id_proof_img = $Id_proof_img;
                        }
                        
                        
                        $qualification_proof_img = null;
                        if ($request->hasFile('qualification_proof')) {
                            $qualification_proof_img = Helper::uploadImageUniversal(
                                $request->file('qualification_proof'),
                                'documents/qualification',   // Qualification Folder
                                $data->qualification_proof_img,// Old image (if updating)
                                $request->first_name.'_qualification', // File name
                                false // Branch wise folder (true/false)
                            );
                            $data->qualification_proof_img = $qualification_proof_img;
                        }
                        
                        $experience_letter = null;
                        if ($request->hasFile('experience_letter')) {
                            $experience_letter = Helper::uploadImageUniversal(
                                $request->file('experience_letter'),
                                'documents/experience',   // Experience Folder
                                $data->experience_letter_img,// Old image (if updating)
                                $request->first_name.'_experience', // File name
                                false // Branch wise folder (true/false)
                            );
                            $data->experience_letter_img = $experience_letter;
                        }
                
            
                   
            
                    
                               
                                $data->session_id = Session::get('session_id');
                              if (!empty($request->access_branch_id)) {
                                 
                                        $data->access_branch_id = implode(",", $request->access_branch_id);
                                         
                                    }
            
                                    $data->branch_id = Session::get('branch_id');     
                                          
                                $data->userName =$request->userName;
                        		$data->first_name =$request->first_name;
                        		$data->last_name =$request->last_name;
                        		$data->city_id= $request->city;
                            	$data->country_id= $request->country_id;
                            	$data->state_id= $request->state;
                        		$data->mobile= $request->mobile;
                        		$data->address  = $request->address;
                                $data->role_id  = $request->role_id;
                        		$data->email  = $request->email;
                        		$data->password  =  Hash::make($request->password);
                        		$data->confirm_password  = $request->confirm_password;
                                $data->joining_amount = $request->joining_amount;
                                $data->dob = $request->dob;
                                $data->joining_date = $request->joining_date;
                                $data->pan_card = $request->pan_card;
                                $data->bank = $request->bank;
                                $data->account_no = $request->account_no;
                                $data->ifsc_code = $request->ifsc_code;
                                $data->class_type_id = $request->class_type_id;
                        		$data->save();
                                $data->syncAttendanceUniqueId();
            		           
                                
                             
                               return response()->json(['status' => 'success', 'message' => 'User Updated Successfully.','redirect' => url('viewUser')]);
                            }
                            return view('user.users.edit',['data'=>$data, 'branch'=>$branch,'getcitie'=>$getcitie]);
                        }
                        
                        
                        public function relievingLetter(Request $request, $id)
                            {
                                $data = User::select(
                                        'users.*',
                                        'role.name as role_name',
                                        'branch.branch_name'
                                    )
                                    ->leftJoin('role', 'role.id', '=', 'users.role_id')
                                    ->leftJoin('branch', 'branch.id', '=', 'users.branch_id')
                                    ->where('users.id', $id)
                                    ->first();
                            
                                return view(
                                    'master.printFilePanel.UserManagement.relieving_letter_print',
                                    ['data' => $data]
                                );
                            }
    
    
    public function joiningLater(Request $request, $id)
    {
        $data = User::select(
                'users.*',
                'role.name as role_name',
                'staff.dob as staff_dob',
                'staff.joining_date as staff_joining_date'
            )
            ->leftJoin('role', 'role.id', '=', 'users.role_id')
            ->leftJoin('teachers as staff', 'staff.user_id', '=', 'users.id')
            ->where('users.id', $id)
            ->first(); 
        return view('master.printFilePanel.UserManagement.joining_letter_print', ['data' => $data]);
    }

    
     public function usersidCard(Request $request,$id){
                $data = User::find($id);
                //$data = User::select('teachers.*','doc.photo')
               
                // $pdf = PDF::loadView('master.printFilePanel.StaffManagement.template05', ['data'=>$data]);
                //  $pdf->setPaper('A4', 'portrait');
                // return $pdf->download('StaffManagement.pdf');
                //$printPreviewId = Helper::printPreview('Teacher Id Card');
               // return view($printPreviewId, ['data' => $data]);
                return view('master.printFilePanel.UserManagement.id_print',['data'=>$data]);
            } 
            
            
            public function deleteUser(Request $request){
                $id = $request->delete_id;
                // TeacherDocuments::where('user_id',$id)->delete();
                $students = User::find($id);

                if (!$students) {
                    return Redirect::to('viewUser')->with('message', 'User not found.');
                }

                if (!empty($students->teacher_id)) {
                    DB::table('teachers')->where('id', $students->teacher_id)->delete();
                }
                /*if(File::exists(env('IMAGE_UPLOAD_PATH').'profile/'.$students->image)){
                    File::delete(env('IMAGE_UPLOAD_PATH').'profile/'.$students->image);
                } */ 
                $students->delete();
                //PermissionManagement::where('reg_user_id',$id)->delete();
                return redirect::to('viewUser')->with('message', 'User Deleted Successfully.');
            }
    
            public function userStatus(Request $request){
                $data = User::where('id',$request->id)->update(['status'=> $request->status_id]);
                return redirect::to('viewUser')->with('message', 'Status Changed Successfully.');
            }
    
         
            
         public function user_permission(Request $request, $id) {
                    $permissionTypes = ['add','edit','view','delete','status','print'];
                
                    if ($request->isMethod('post')) {
                        $modules = $request->modules ?? [];
                        $subModules = $request->sub_modules ?? []; // get submodules from form
                
                        foreach ($modules as $sidebarId => $perms) {
                            $sidebar = DB::table('sidebars')->where('id', $sidebarId)->first();
                
                            
                            $row = [
                                'sidebar_name'   => $sidebar->name ?? '',
                                'updated_at'     => now(),
                                'sub_sidebar_id' => isset($subModules[$sidebarId]) ? implode(',', $subModules[$sidebarId]) : null
                            ];
                
                            foreach ($permissionTypes as $type) {
                                $row[$type] = in_array($type, $perms) ? 1 : 0;
                            }
                
                            $existing = DB::table('user_permission')->where('user_id', $id)->where('sidebar_id', $sidebarId)->first();
                
                            if ($existing) {
                                DB::table('user_permission')->where('user_id', $id)->where('sidebar_id', $sidebarId)->update($row);
                            } else {
                                DB::table('user_permission')->insert(array_merge($row, [
                                    'user_id'    => $id,
                                    'sidebar_id' => $sidebarId,
                                    'created_at' => now()
                                ]));
                            }
                        }
                
                        if ($request->ajax()) {
                            return response()->json(['success' => true, 'message' => 'User permissions saved successfully!']);
                        }
                        return back()->with('success', 'User permissions saved successfully!');
                    }
                
                       $branch = Branch::find(Session::get('branch_id'));

                        $branchSidebarIds = !empty($branch->branch_sidebar_id) ? explode(',', $branch->branch_sidebar_id) : [];
                      
                    $modules = DB::table('sidebars')->whereNull('deleted_at')->whereIn('id', $branchSidebarIds)->get();
                    $subs = DB::table('sidebar_sub')->where('sub_sidebar','yes')->whereIn('sidebar_id', $branchSidebarIds)->whereNull('deleted_at')->get()->groupBy('sidebar_id');
                    $permissions = DB::table('user_permission')->where('user_id', $id)->get()->keyBy('sidebar_id');
                    return view('user.users.user_permission', ['modules' => $modules,'subs' => $subs,'permissions' => $permissions,'permissionTypes' => $permissionTypes,'user_id' => $id,]);
                }
                
                
                public function module_status(Request $request) {
                               $user_id = $request->user_id;
                                $sidebar_id = $request->module_id;
                                $status = $request->status;

                                if ($status == 1) {
                                    DB::table('user_permission')->where('user_id', $user_id)->where('sidebar_id', $sidebar_id)->update(['deleted_at' => null]);
                                } else {
                                    DB::table('user_permission')->where('user_id', $user_id)->where('sidebar_id', $sidebar_id)->update(['deleted_at' => now()]);
                                }
                                if ($status == 1) {
                                  return response()->json(['success' => true, 'message' => 'Permissions added successfully!']);
                                }
                                else {
                                  return response()->json(['success' => true, 'message' => 'Permissions removed successfully!']);
                                }
                }
                
                
public function showTimeTable($userId)
{
    $classType = Helper::classType();

    $selectedSubjects = TeacherSubject::where('user_id', $userId)
        ->where('branch_id', Session::get('branch_id'))
        ->where('session_id', Session::get('session_id'))
        ->pluck('subject_id')
        ->toArray();

    return view('user.users.user_time_table', compact(
        'classType',
        'selectedSubjects',
        'userId'
    ));
} 
public function saveTimeTable(Request $request)
{
    $classId   = $request->class_id;
    $userId    = $request->user_id;
    $subjectId = $request->subject_id;
    $status    = $request->status;

    $branchId  = Session::get('branch_id');
    $sessionId = Session::get('session_id');

    $user = User::find($userId);

    if($status == true){

        // पहले check करें record पहले से है या नहीं
        $exists = TeacherSubject::where('class_type_id', $classId)
            ->where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->exists();

        if(!$exists){
            TeacherSubject::create([
                'class_type_id' => $classId,
                'user_id'       => $userId,
                'subject_id'    => $subjectId,
                'branch_id'     => $branchId,
                'session_id'    => $sessionId,
            ]);
        }

        // अब users table में class जोड़ें
        if($user){

            $existingClasses = !empty($user->class_type_id)
                ? explode(',', $user->class_type_id)
                : [];

            if(!in_array($classId, $existingClasses)){
                $existingClasses[] = $classId;
            }

            $user->class_type_id = implode(',', array_unique($existingClasses));
            $user->save();
        }

    } else {

        // Uncheck → TeacherSubject से delete
        TeacherSubject::where('class_type_id', $classId)
            ->where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->delete();

        // users table से class हटाएँ
        if($user && !empty($user->class_type_id)){

            $existingClasses = explode(',', $user->class_type_id);

            $existingClasses = array_diff($existingClasses, [$classId]);

            $user->class_type_id = implode(',', $existingClasses);
            $user->save();
        }
    }

    return response()->json(['status' => true]);
}

                
  
}    
