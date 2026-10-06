<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Homework;
use App\Models\Master\HourlyHomework;
use App\Models\Master\UploadHomework;
use App\Models\Master\HomeworkDocuments;
use App\Models\SmsSetting;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\TeacherSubject;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Admission;
use App\Models\Teacher;
use App\Helpers\helper;
use Session;
use Hash;
use Str;
use File;
use Redirect;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Image;
use DB;
class HomeworkController extends Controller

{

            public function hwDetailWithoutLogin(Request $request, $id){
                $students = UploadHomework::orderBy('id','DESC')->groupBy('admission_id')->get();
                return view('master.home_work.home_work.details',['students'=>$students]);
            }
    
            public function dashboard(Request $request){
                return view('master.home_work.dashboard');
            }    
    
            public function add(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'title'  => 'required',
                        'class_type_id'  => 'required',
                        'subject'  => 'required',
                        'homework_issue_date'  => 'required',
                        'submission_date'  => 'required',
                        'description'  => 'required',
                    ]);

                    $title = trim($request->title);
                    if(!empty($request->homework_type) && strpos($title, '[' . $request->homework_type . ']') === false){
                        $title = '[' . $request->homework_type . '] ' . $title;
                    }

                    $homework = '';
                    if($request->hasFile('content_file')){
                        $file = $request->file('content_file');
                        $extension = $file->getClientOriginalExtension();
                        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $homework = time() . '_' . uniqid() . '_' . substr($sanitizedName, 0, 30) . '.' . $extension;
                        $destinationPath = base_path('schoolimage/homework');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0775, true);
                        }
                        $file->move($destinationPath, $homework);      
                    }

                    $addhomework = new Homework;
                    $addhomework->user_id = Session::get('id');
                    $addhomework->session_id = Session::get('session_id');
                    $addhomework->branch_id = Session::get('branch_id');
                    $addhomework->teacher_id = Session::get('teacher_id');
            		$addhomework->class_type_id = $request->class_type_id;
            		$addhomework->title = $title;
            		$addhomework->subject  = $request->subject;
            		$addhomework->homework_date = $request->homework_issue_date ?? date('Y-m-d');
            		$addhomework->homework_issue_date  = $request->homework_issue_date;
            		$addhomework->submission_date  = $request->submission_date;
            		$addhomework->content_file = $homework;
            		$addhomework->description = $request->description;
            		$addhomework->view_status = '1';
                    $addhomework->max_marks = $request->filled('max_marks') ? (int)$request->max_marks : null;
                    $addhomework->allow_late_submission = $request->has('allow_late_submission') ? (int)$request->allow_late_submission : 1;
                    if($request->filled('section_id')){
                        $addhomework->section_id = $request->section_id;
                    }
                    $addhomework->save();

                    // Optional student notification loop (controlled via toggle for high performance)
                    $shouldNotify = $request->has('notify_students') ? (int)$request->notify_students : 1;
                    if($shouldNotify == 1){
                        $template = MessageTemplate::select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                            ->where('message_types.slug','homework')->first();
                        $branch = Branch::find(Session::get('branch_id'));
                        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                        $students = Admission::where('class_type_id',$request->class_type_id)
                            ->where('session_id',Session::get('session_id'))
                            ->where('branch_id',Session::get('branch_id'))
                            ->get();
                        $subject = Subject::where('id',$request->subject)->first();
                        $subjectName = $subject->name ?? 'Subject';

                        if(!empty($students) && $students->count() > 0){
                            foreach($students as $stu){
                                $arrey1 = [
                                    '{#name#}',
                                    '{#subject#}',
                                    '{#title#}',
                                    '{#description#}',
                                    '{#submission_date#}',
                                    '{#school_name#}'
                                ];
                                $arrey2 = [
                                    trim(($stu->first_name ?? '') . ' ' . ($stu->last_name ?? '')),
                                    $subjectName,
                                    $title,
                                    strip_tags($request->description ?? ''),
                                    date('d-m-Y', strtotime($request->submission_date)),
                                    $setting->name ?? ''
                                ];
                                $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                
                                if (!empty($setting) && $setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Homework',
                                        $whatsapp,
                                        'student',
                                        $stu->id
                                    ); 
                                }
                                 
                                if (!empty($template) && $template->message_type_status == 1) {
                                    if (!empty($branch) && $branch->whatsapp_srvc == 1) {
                                        $mobile = $stu->mobile ?? '';
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile, $whatsapp);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($request->ajax() || $request->wantsJson()){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Homework Published Successfully.',
                            'redirect' => url('homework/index')
                        ]);
                    }

                    return redirect('homework/index')->with('message', 'Homework Published Successfully.');
                }
                return view('master.home_work.home_work.add');
            }
            
            public function index(Request $request){
                $search = [
                    'class_type_id' => $request->input('class_type_id'),
                    'subject' => $request->input('subject'),
                    'homework_type' => $request->input('homework_type'),
                    'title' => $request->input('title'),
                    'assigned_by' => $request->input('assigned_by'),
                    'homework_issue_date' => $request->input('homework_issue_date'),
                    'submission_date' => $request->input('submission_date'),
                    'status' => $request->input('status'),
                ];

                $query = Homework::with(['Subject', 'ClassType', 'Teacher', 'User', 'Section'])
                    ->withCount(['UploadHomework as submitted_students_count' => function($q) {
                        $q->select(DB::raw('count(distinct(admission_id))'));
                    }])
                    ->withCount('UploadHomework')
                    ->where('homeworks.session_id', Session::get('session_id'))
                    ->where('homeworks.branch_id', Session::get('branch_id'));

                // Role-based scoping
                if (Session::get('role_id') == 3) {
                    $query->where('homeworks.class_type_id', Session::get('class_type_id'));
                } elseif (Session::get('role_id') == 2) {
                    $teacherId = Session::get('teacher_id');
                    $checkClassTeacher = Teacher::where('id', $teacherId)->first();
                    $teacherSubjects = TeacherSubject::where('teacher_id', $teacherId)->pluck('subject_id')->toArray();
                    if (!empty($checkClassTeacher) && !empty($checkClassTeacher->class_type_id)) {
                        $classSubIds = Subject::where('class_type_id', $checkClassTeacher->class_type_id)->pluck('id')->toArray();
                        $teacherSubjects = array_unique(array_merge($teacherSubjects, $classSubIds));
                    }
                    $query->where(function($q) use ($teacherSubjects, $teacherId) {
                        if (!empty($teacherSubjects)) {
                            $q->whereIn('homeworks.subject', $teacherSubjects);
                        }
                        $q->orWhere('homeworks.teacher_id', $teacherId);
                    });
                }

                // In-column filters
                if (!empty($search['class_type_id'])) {
                    $query->where('homeworks.class_type_id', $search['class_type_id']);
                }

                if (!empty($search['subject'])) {
                    $query->where('homeworks.subject', $search['subject']);
                }

                if (!empty($search['homework_type'])) {
                    $query->where('homeworks.title', 'LIKE', '%[' . $search['homework_type'] . ']%');
                }

                if (!empty($search['title'])) {
                    $term = $search['title'];
                    $query->where(function($q) use ($term) {
                        $q->where('homeworks.title', 'LIKE', '%' . $term . '%')
                          ->orWhere('homeworks.description', 'LIKE', '%' . $term . '%');
                    });
                }

                if (!empty($search['assigned_by'])) {
                    $term = $search['assigned_by'];
                    $query->where(function($q) use ($term) {
                        $q->whereHas('Teacher', function($tq) use ($term) {
                            $tq->where(DB::raw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))"), 'LIKE', '%' . $term . '%');
                        })->orWhereHas('User', function($uq) use ($term) {
                            $uq->where(DB::raw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))"), 'LIKE', '%' . $term . '%');
                        });
                    });
                }

                if (!empty($search['homework_issue_date'])) {
                    $query->whereDate('homeworks.homework_issue_date', $search['homework_issue_date']);
                }

                if (!empty($search['submission_date'])) {
                    $query->whereDate('homeworks.submission_date', $search['submission_date']);
                }

                if (!empty($search['status'])) {
                    $today = date('Y-m-d');
                    if ($search['status'] === 'active') {
                        $query->whereDate('homeworks.submission_date', '>', $today);
                    } elseif ($search['status'] === 'due_today') {
                        $query->whereDate('homeworks.submission_date', '=', $today);
                    } elseif ($search['status'] === 'overdue') {
                        $query->whereDate('homeworks.submission_date', '<', $today);
                    }
                }

                $perPage = max(10, min((int)$request->input('per_page', 25), 100));
                $allhomework = $query->orderBy('homeworks.id', 'DESC')->paginate($perPage);
                $startIndex = ($allhomework->currentPage() - 1) * $allhomework->perPage();

                // Compute total active students per class for accurate submission progress indicators
                $totalStudentsPerClass = Admission::select('class_type_id', DB::raw('count(id) as total_students'))
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->whereNull('deleted_at')
                    ->groupBy('class_type_id')
                    ->pluck('total_students', 'class_type_id')
                    ->toArray();

                if ($request->ajax() || $request->has('ajax')) {
                    $html = view('master.home_work.home_work.index_rows', [
                        'data' => $allhomework,
                        'startIndex' => $startIndex,
                        'totalStudentsPerClass' => $totalStudentsPerClass
                    ])->render();

                    return response()->json([
                        'status' => true,
                        'html' => $html,
                        'total' => $allhomework->total(),
                        'from' => $allhomework->firstItem() ?? 0,
                        'to' => $allhomework->lastItem() ?? 0,
                        'current_page' => $allhomework->currentPage(),
                        'last_page' => $allhomework->lastPage(),
                        'per_page' => $allhomework->perPage()
                    ]);
                }

                $classType = Helper::classType();
                $allSubjects = Subject::whereNull('deleted_at')
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->orderBy('name', 'ASC')
                    ->get();

                return Helper::view('master.home_work.home_work.index', [
                    'data' => $allhomework,
                    'startIndex' => $startIndex,
                    'search' => $search,
                    'classType' => $classType,
                    'allSubjects' => $allSubjects,
                    'totalStudentsPerClass' => $totalStudentsPerClass,
                    'currentPage' => $allhomework->currentPage(),
                    'lastPage' => $allhomework->lastPage(),
                    'perPage' => $perPage,
                    'totalCount' => $allhomework->total()
                ]);
            }

            public function edit(Request $request, $id){
                $data = Homework::find($id);
                if(!$data){
                    return redirect('homework/index')->with('error', 'Homework record not found.');
                }

                if($request->isMethod('post')){
                    $request->validate([
                        'title'  => 'required',
                        'class_type_id'  => 'required',
                        'subject'  => 'required',
                        'homework_issue_date'  => 'required',
                        'submission_date'  => 'required',
                        'description'  => 'required',      
                    ]);

                    $title = trim($request->title);
                    if(!empty($request->homework_type) && strpos($title, '[' . $request->homework_type . ']') === false){
                        $title = '[' . $request->homework_type . '] ' . $title;
                    }

                    if($request->hasFile('content_file')){
                        $file = $request->file('content_file');
                        $extension = $file->getClientOriginalExtension();
                        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $homework = time() . '_' . uniqid() . '_' . substr($sanitizedName, 0, 30) . '.' . $extension;
                        $destinationPath = base_path('schoolimage/homework');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0775, true);
                        }
                        $file->move($destinationPath, $homework);

                        // Safely remove previous file if it existed
                        if (!empty($data->content_file)) {
                            $oldFilePath = base_path('schoolimage/homework/' . $data->content_file);
                            if (file_exists($oldFilePath)) {
                                @unlink($oldFilePath);
                            }
                        }
                        $data->content_file = $homework;
                    }

                    $data->user_id = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    if(!empty(Session::get('teacher_id'))){
                        $data->teacher_id = Session::get('teacher_id');
                    }
                    $data->title = $title;
                    $data->class_type_id = $request->class_type_id;
                    if($request->filled('section_id')){
                        $data->section_id = $request->section_id;
                    }
                    $data->subject  = $request->subject;
                    $data->homework_date = $request->homework_issue_date ?? $data->homework_date ?? date('Y-m-d');
                    $data->homework_issue_date  = $request->homework_issue_date;
                    $data->submission_date  = $request->submission_date;
                    $data->description  = $request->description;
                    $data->max_marks = $request->filled('max_marks') ? (int)$request->max_marks : null;
                    $data->allow_late_submission = $request->has('allow_late_submission') ? (int)$request->allow_late_submission : 1;
                    $data->save();

                    // Optional student notification on edit if explicitly requested
                    $shouldNotify = $request->has('notify_students') ? (int)$request->notify_students : 0;
                    if($shouldNotify == 1){
                        $template = MessageTemplate::select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                            ->where('message_types.slug','homework')->first();
                        $branch = Branch::find(Session::get('branch_id'));
                        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                        $students = Admission::where('class_type_id',$request->class_type_id)
                            ->where('session_id',Session::get('session_id'))
                            ->where('branch_id',Session::get('branch_id'))
                            ->get();
                        $subject = Subject::where('id',$request->subject)->first();
                        $subjectName = $subject->name ?? 'Subject';

                        if(!empty($students) && $students->count() > 0){
                            foreach($students as $stu){
                                $arrey1 = [
                                    '{#name#}',
                                    '{#subject#}',
                                    '{#title#}',
                                    '{#description#}',
                                    '{#submission_date#}',
                                    '{#school_name#}'
                                ];
                                $arrey2 = [
                                    trim(($stu->first_name ?? '') . ' ' . ($stu->last_name ?? '')),
                                    $subjectName,
                                    $title,
                                    strip_tags($request->description ?? ''),
                                    date('d-m-Y', strtotime($request->submission_date)),
                                    $setting->name ?? ''
                                ];
                                $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                
                                if (!empty($setting) && $setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Homework',
                                        $whatsapp,
                                        'student',
                                        $stu->id
                                    ); 
                                }
                                 
                                if (!empty($template) && $template->message_type_status == 1) {
                                    if (!empty($branch) && $branch->whatsapp_srvc == 1) {
                                        $mobile = $stu->mobile ?? '';
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile, $whatsapp);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($request->ajax() || $request->wantsJson()){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Homework Updated Successfully.',
                            'redirect' => url('homework/index')
                        ]);
                    }
                    return redirect('homework/index')->with('message', 'Homework Updated Successfully.');
                }

                // Pre-load subjects for selected class to ensure instant rendering without AJAX delay
                $subjects = Subject::where('class_type_id', $data->class_type_id)
                    ->whereNull('deleted_at')
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->orderBy('name', 'ASC')
                    ->get();

                return view('master.home_work.home_work.add', [
                    'data' => $data,
                    'subjects' => $subjects,
                    'isEdit' => true
                ]);
            }
            public function downloadHomework(Request $request,$id){
                $upload_list = Homework::find($id);
                if(File::exists(env('IMAGE_UPLOAD_PATH').'homework/'.$upload_list->content_file)){
                    $image = 'schoolimage/homework/'.$upload_list['content_file'];
                    return Response::download($image);               
                }else{
                    return redirect::to('assignments')->with('error', 'No File Found.');
                }
            }    
            public function downloadAssignment(Request $request,$img_name){
                $image = 'schoolimage/uploadHomework/'.$img_name; 
                return Response::download($image);
            } 
    
            public function delete(Request $request){
                $id = $request->delete_id;
                $HourlyHomework = Homework::find($id);
                if (File::exists(env('IMAGE_UPLOAD_PATH') . 'homework/' . $HourlyHomework->content_file)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'homework/' . $HourlyHomework->content_file);
                }
                $HourlyHomework->delete();
                return redirect::to('homework/index')->with('message', ' Homework Deleted Successfully.');
            }

            public function uploadHomework(Request $request){
                $adminMail = User::where('role_id','1')->get()->first();
                $teacherMail = Teacher::where('class_type_id',Session::get('class_type_id'))->get()->first();
                $stu = Admission::where('id',Session::get('id'))->get()->first();
                if($request->isMethod('post')){
                    $request->validate([]);
                    $uploadHW = new UploadHomework;//model name
                    $uploadHW->user_id = Session::get('id');
                    $uploadHW->session_id = Session::get('session_id');
                    $uploadHW->admission_id = Session::get('id');
                    $uploadHW->branch_id = Session::get('branch_id');
            		$uploadHW->class_type_id = Session::get('class_type_id');
            		$uploadHW->submission_date  = date('Y-m-d');
            		$uploadHW->message  = $request->message;
            		$uploadHW->homework_id  = $request->homework_id;
                    $uploadHW->save();
                    $upload_hw_id = $uploadHW->id;
                    for ($count = 0; $count <= count($request->content_file); $count++) {
                        if (isset($request->content_file[$count])) {   
                            $uploadDocument = new HomeworkDocuments;//model name
                            if ($request->hasFile('content_file') && isset($request->file('content_file')[$count])) {

                                $file = $request->file('content_file')[$count];

                                $extension = strtolower($file->getClientOriginalExtension());

                                $document = uniqid() . '.' . $extension;

                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'uploadHomework/';

                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0755, true);
                                }

                                // Compress only image files
                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {

                                    $compressedImage = Image::make($file)
                                        ->resize(300, null, function ($constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        })
                                        ->encode('jpg', 70); // Image quality

                                    $compressedImage->save($destinationPath . $document);

                                } else {
                                    // PDF or other files
                                    $file->move($destinationPath, $document);
                                }
                            }
                            $uploadDocument->user_id = Session::get('id');
                            $uploadDocument->session_id = Session::get('session_id');
                            $uploadDocument->branch_id = Session::get('branch_id');   
                            $uploadDocument->admission_id = Session::get('id');
                            $uploadDocument->upload_hw_id  = $upload_hw_id;
                            $uploadDocument->content_file  = $document;
                            $uploadDocument->save(); 
                        }
                    }  
                    if(Session::get('role_id') == 3){
                        return redirect::to('student_homework')->with('message', ' Assignment Upload Successfully.');
                    }else{
                        return redirect::to('homework/index')->with('message', ' Assignment Upload Successfully.');
                    }
                }
            }    

            public function homeworkDetails(Request $request, $id){
                $search = [
                    'name' => $request->input('name'),
                    'status' => $request->input('status'),
                ];

                $homework = Homework::with(['Subject', 'ClassType', 'Teacher', 'User'])->find($id);

                $query = UploadHomework::select('upload_homeworks.*')
                    ->with(['Admission', 'ClassType'])
                    ->where('upload_homeworks.session_id', Session::get('session_id'))
                    ->where('upload_homeworks.branch_id', Session::get('branch_id'))
                    ->where('upload_homeworks.homework_id', $id)
                    ->leftJoin('admissions as Admission', 'Admission.id', '=', 'upload_homeworks.admission_id');

                if (!empty($search['name'])) {
                    $term = $search['name'];
                    $query->where(function($q) use ($term) {
                        $q->where('Admission.first_name', 'LIKE', '%' . $term . '%')
                          ->orWhere('Admission.last_name', 'LIKE', '%' . $term . '%')
                          ->orWhere(DB::raw("CONCAT(COALESCE(Admission.first_name,''), ' ', COALESCE(Admission.last_name,''))"), 'LIKE', '%' . $term . '%')
                          ->orWhere('Admission.admissionNo', 'LIKE', '%' . $term . '%')
                          ->orWhere('Admission.mobile', 'LIKE', '%' . $term . '%')
                          ->orWhere('Admission.father_name', 'LIKE', '%' . $term . '%');
                    });
                }

                if (Session::get('role_id') == 3) {
                    $query->where('upload_homeworks.class_type_id', Session::get('class_type_id'))
                          ->where('upload_homeworks.admission_id', Session::get('id'));
                }

                // Get latest submission per student
                $students = $query->orderBy('upload_homeworks.id', 'DESC')
                    ->get()
                    ->unique('admission_id');

                // Pre-aggregate document evaluation counts in ONE query (0 N+1 queries)
                $docStats = DB::table('homework_documents')
                    ->join('upload_homeworks', 'upload_homeworks.id', '=', 'homework_documents.upload_hw_id')
                    ->where('upload_homeworks.homework_id', $id)
                    ->select(
                        'homework_documents.admission_id',
                        DB::raw('COUNT(*) as total_docs'),
                        DB::raw('SUM(CASE WHEN homework_documents.status = 1 THEN 1 ELSE 0 END) as checked_docs')
                    )
                    ->groupBy('homework_documents.admission_id')
                    ->get()
                    ->keyBy('admission_id');

                // Total attempts count per student
                $attemptStats = DB::table('upload_homeworks')
                    ->where('homework_id', $id)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->select('admission_id', DB::raw('COUNT(*) as total_attempts'), DB::raw('MAX(submission_date) as last_date'))
                    ->groupBy('admission_id')
                    ->get()
                    ->keyBy('admission_id');

                // Filter by status if requested (checked vs pending)
                if (!empty($search['status'])) {
                    $students = $students->filter(function($stu) use ($docStats, $search) {
                        $stats = $docStats[$stu->admission_id] ?? null;
                        $total = $stats->total_docs ?? 0;
                        $checked = $stats->checked_docs ?? 0;
                        $isChecked = ($total > 0 && $total == $checked);
                        if ($search['status'] === 'checked') {
                            return $isChecked;
                        } elseif ($search['status'] === 'pending') {
                            return !$isChecked;
                        }
                        return true;
                    });
                }

                // Overall metrics for header stat cards
                $totalSubmittedStudents = $attemptStats->count();
                $totalAttempts = $attemptStats->sum('total_attempts');
                $checkedCount = 0;
                foreach ($attemptStats as $admId => $att) {
                    $st = $docStats[$admId] ?? null;
                    if ($st && $st->total_docs > 0 && $st->total_docs == $st->checked_docs) {
                        $checkedCount++;
                    }
                }
                $pendingCount = max(0, $totalSubmittedStudents - $checkedCount);

                $statsSummary = [
                    'total_students' => $totalSubmittedStudents,
                    'total_attempts' => $totalAttempts,
                    'checked' => $checkedCount,
                    'pending' => $pendingCount
                ];

                $viewData = [
                    'students' => $students,
                    'id' => $id,
                    'search' => $search,
                    'homework' => $homework,
                    'docStats' => $docStats,
                    'attemptStats' => $attemptStats,
                    'statsSummary' => $statsSummary
                ];

                if (Session::get('role_id') == 3) {
                    return view('master.home_work.student_view.details', $viewData);
                }

                return view('master.home_work.home_work.details', $viewData);
            }

            public function particularHomeworkDetails(Request $request){
                $homeworkId = $request->homework_id;
                $currentAdmId = (int)$request->admission_id;

                $data = UploadHomework::with([
                    'Admission.ClassType',
                    'Admission.Section',
                    'HomeworkDocuments' => function($q) {
                        $q->orderBy('id', 'ASC');
                    }
                ])
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->where('admission_id', $currentAdmId)
                ->where('homework_id', $homeworkId)
                ->orderBy('id', 'DESC')
                ->get();

                $student = null;
                if ($data->isNotEmpty() && !empty($data->first()->Admission)) {
                    $student = $data->first()->Admission;
                } else {
                    $student = Admission::with(['ClassType', 'Section'])->find($currentAdmId);
                }

                $homework = Homework::with(['ClassType', 'Subject'])->find($homeworkId);

                // Fetch full roster of submitted students for instant Next / Prev navigation
                $allStudentIds = UploadHomework::where('homework_id', $homeworkId)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->orderBy('id', 'ASC')
                    ->pluck('admission_id')
                    ->unique()
                    ->values()
                    ->toArray();

                $currentPos = array_search($currentAdmId, $allStudentIds);
                $prevStudentId = ($currentPos !== false && $currentPos > 0) ? $allStudentIds[$currentPos - 1] : null;
                $nextStudentId = ($currentPos !== false && $currentPos < count($allStudentIds) - 1) ? $allStudentIds[$currentPos + 1] : null;
                $currentIndex = ($currentPos !== false) ? ($currentPos + 1) : 1;
                $totalStudents = max(1, count($allStudentIds));

                return view('master.home_work.home_work.data_homework', [
                    'data' => $data,
                    'student' => $student,
                    'homework' => $homework,
                    'homeworkId' => $homeworkId,
                    'prevStudentId' => $prevStudentId,
                    'nextStudentId' => $nextStudentId,
                    'currentIndex' => $currentIndex,
                    'totalStudents' => $totalStudents
                ]);
            }

            public function evaluateHomework(Request $request){
                if($request->isMethod('post')){
                    $status = '1';
                    if(!empty($request->id) && is_array($request->id)){
                        foreach($request->id as $key => $item){
                            $updateData = [
                                'hw_review' => $request->review[$key] ?? '',
                                'status' => $status,
                                'evaluate_date' => date('Y-m-d'),
                                'teacher_id' => Session::get('teacher_id')
                            ];
                            if(isset($request->marks[$key]) && $request->marks[$key] !== ''){
                                $updateData['marks'] = $request->marks[$key];
                            }
                            HomeworkDocuments::where('id', $request->id[$key])->update($updateData);

                            // Keep upload_homeworks parent table in sync
                            $doc = HomeworkDocuments::find($request->id[$key]);
                            if($doc && !empty($doc->upload_hw_id)){
                                $uploadHwUpdate = ['evaluate_date' => date('Y-m-d')];
                                if(isset($updateData['marks'])){
                                    $uploadHwUpdate['marks'] = $updateData['marks'];
                                }
                                UploadHomework::where('id', $doc->upload_hw_id)->update($uploadHwUpdate);
                            }
                        }
                    }

                    if($request->ajax() || $request->wantsJson()){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Evaluation and remarks saved successfully.'
                        ]);
                    }
                    return redirect::to('homework/index')->with('message', ' Homework Evaluated Successfully.');
                }
            }

            public function remindDefaulters(Request $request){
                $homeworkId = $request->homework_id;
                $homework = Homework::with(['ClassType', 'Subject'])->find($homeworkId);
                if(!$homework){
                    return response()->json(['status' => false, 'message' => 'Homework assignment record not found.']);
                }

                $submittedStudentIds = UploadHomework::where('homework_id', $homeworkId)
                    ->pluck('admission_id')
                    ->toArray();

                $pendingStudents = Admission::where('class_type_id', $homework->class_type_id)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->whereNull('deleted_at')
                    ->whereNotIn('id', $submittedStudentIds)
                    ->get();

                $queuedCount = 0;
                $setting = Setting::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->first();
                $schoolName = $setting->name ?? 'School';

                foreach($pendingStudents as $stu){
                    $mobile = $stu->mobile ?? '';
                    if(!empty($mobile)){
                        $stuName = trim(($stu->first_name ?? '') . ' ' . ($stu->last_name ?? ''));
                        $subName = $homework->Subject->name ?? 'Subject';
                        $dueDate = !empty($homework->submission_date) ? date('d-m-Y', strtotime($homework->submission_date)) : '';
                        $msg = "Dear {$stuName}, reminder to submit your pending assignment for {$subName} ({$homework->title}) due on {$dueDate}. - {$schoolName}";
                        Helper::MessageQueue($mobile, $msg);
                        $queuedCount++;
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => "WhatsApp reminder queued for {$queuedCount} pending student(s).",
                    'queued_count' => $queuedCount
                ]);
            }

            public function bulkEvaluate(Request $request){
                $homeworkId = $request->homework_id;
                $admissionIds = $request->admission_ids;
                $remarks = $request->input('remarks', 'Checked & Approved ⭐');

                if(empty($admissionIds) || !is_array($admissionIds)){
                    return response()->json(['status' => false, 'message' => 'Please select at least one student to evaluate.']);
                }

                $uploadHwIds = UploadHomework::where('homework_id', $homeworkId)
                    ->whereIn('admission_id', $admissionIds)
                    ->pluck('id')
                    ->toArray();

                if(!empty($uploadHwIds)){
                    UploadHomework::whereIn('id', $uploadHwIds)->update([
                        'evaluate_date' => date('Y-m-d'),
                        'teacher_id' => Session::get('teacher_id')
                    ]);

                    HomeworkDocuments::whereIn('upload_hw_id', $uploadHwIds)->update([
                        'status' => '1',
                        'hw_review' => $remarks,
                        'evaluate_date' => date('Y-m-d'),
                        'teacher_id' => Session::get('teacher_id')
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => count($admissionIds) . ' student submission(s) marked as evaluated.'
                ]);
            }

            public function exportSubmissions(Request $request, $id){
                $homework = Homework::with(['ClassType', 'Subject'])->findOrFail($id);
                $submissions = UploadHomework::with(['Admission.ClassType', 'Admission.Section', 'HomeworkDocuments'])
                    ->where('homework_id', $id)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->orderBy('id', 'DESC')
                    ->get();

                $filename = 'Homework_Submissions_HW' . $id . '_' . date('Ymd_His') . '.csv';

                $headers = [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Pragma' => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0'
                ];

                $callback = function() use ($submissions, $homework) {
                    $file = fopen('php://output', 'w');
                    // UTF-8 Byte Order Mark for Excel
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                    fputcsv($file, ['ARISE ERP - HOMEWORK SUBMISSIONS ROSTER']);
                    fputcsv($file, ['Title:', $homework->title]);
                    fputcsv($file, ['Class / Batch:', $homework->ClassType->name ?? 'N/A']);
                    fputcsv($file, ['Subject:', $homework->Subject->name ?? 'N/A']);
                    fputcsv($file, ['Max Marks:', $homework->max_marks ?? 'Not Specified']);
                    fputcsv($file, ['Generated At:', date('d-m-Y H:i:s')]);
                    fputcsv($file, []);

                    fputcsv($file, [
                        'Sr No',
                        'Admission No',
                        'Student Name',
                        'Father Name',
                        'Mobile',
                        'Class',
                        'Section',
                        'Submission Date',
                        'Status',
                        'Marks Scored',
                        'Max Marks',
                        'Teacher Feedback'
                    ]);

                    $sr = 1;
                    foreach ($submissions as $sub) {
                        $stu = $sub->Admission;
                        $doc = $sub->HomeworkDocuments->first();
                        $isChecked = ($doc && $doc->status == 1);
                        $status = $isChecked ? 'Evaluated' : 'Pending Review';
                        $marks = $doc ? ($doc->marks ?? '') : '';
                        $remarks = $doc ? ($doc->hw_review ?? '') : '';

                        fputcsv($file, [
                            $sr++,
                            $stu->admissionNo ?? '',
                            trim(($stu->first_name ?? '') . ' ' . ($stu->last_name ?? '')),
                            $stu->father_name ?? '',
                            $stu->mobile ?? '',
                            $stu->ClassType->name ?? ($homework->ClassType->name ?? ''),
                            $stu->Section->name ?? '',
                            $sub->submission_date ? date('d-m-Y H:i', strtotime($sub->submission_date)) : '',
                            $status,
                            $marks,
                            $homework->max_marks ?? '',
                            $remarks
                        ]);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }  

            public function hwAdd(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'class_type_id'  => 'required',
                        'subject'  => 'required',
                        'admission_id'  => 'required',
                        'times'  => 'required',
                        'content_file'  => 'required',
                        'title'  => 'required',
                    ]);
                    $homework ='';
                    if($request->file('content_file')){
                        $image = $request->file('content_file');
                        $path = $image->getRealPath();      
                        $homework =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'homework';
                        $image->move($destinationPath, $homework);     
                    }
                    $addhomework = new HourlyHomework;//model name
                    $addhomework->user_id = Session::get('id');
                    $addhomework->session_id = Session::get('session_id');
                    $addhomework->branch_id = Session::get('branch_id');
            		$addhomework->class_type_id = $request->class_type_id;
            	    $addhomework->admission_id  = $request->admission_id;
            		$addhomework->subject  = $request->subject;
            		$addhomework->homework_date  = $request->homework_date;
            		$addhomework->content_file = $homework;
            		$addhomework->title = $request->title;
            		$addhomework->times = $request->times;
                    $addhomework->save();
                   return response()->json(['status' => 'success','message' => 'Homework Added Successfully.',]);   
                }
                return view('master.home_work.hourly.add');
            }

            public function hourlyHomeworkView(Request $request){
                $search['class_type_id'] = $request->class_type_id;
                $search['admissionNo'] = $request->admissionNo;
                $search['name'] = $request->name;
                $homework = HourlyHomework::select('hourly_homework.*','admission.first_name as first_name','admission.last_name as last_name')           
    		    ->leftjoin('admissions as admission','admission.id','hourly_homework.admission_id')
    		    ->where('hourly_homework.session_id',Session::get('session_id'))
    		    ->where('hourly_homework.branch_id',Session::get('branch_id')); 
                if(Session::get('role_id') == 2 ){
                    $allhomework = $homework->where('hourly_homework.class_type_id',Session::get('class_type_id'));
                }
                if(Session::get('role_id') == 3 ){
                    $allhomework = $homework->where('hourly_homework.admission_id',Session::get('id'));
                }            
    		    if($request->isMethod('post')){
        		    if(!empty($request->name)){
                		$allhomework = $homework->where('admission.first_name', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.last_name', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.mobile', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.email', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.aadhaar', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.father_name', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.mother_name', 'LIKE', '%'.$request->name.'%')
                        ->orWhere('admission.address', 'LIKE', '%'.$request->name.'%');
        		    }
        		    if(!empty($request->class_type_id)){
        		        $allhomework = $homework->where('hourly_homework.class_type_id',$request->class_type_id);
        		    }
        		    if(!empty($request->admissionNo)){
        		        $allhomework = $homework->where('admission.admissionNo',$request->admissionNo);
        		    }    		    
        		}
        		$allhomework = $homework->groupBy('hourly_homework.id')->orderBy('id','DESC')->get();
                // 		$students = UploadHomework::with('Admission')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'));
                //     		if(Session::get('role_id') == 1){
                //     		    $students = $students->orderBy('id','DESC')->get();
                    		 
                //     		}else{
                //     		    $students = $students->where('class_type_id',Session::get('class_type_id'))->orderBy('id','DESC')->get();
                //     		}
                return view('master.home_work.hourly.view',['data'=>$allhomework,'search'=>$search]);
            }

            public function downloadHourlyHomework(Request $request,$id){
                $upload_list = HourlyHomework::find($id);
                $image = 'schoolimage/homework/'.$upload_list['content_file'];
                return Response::download($image);
            } 
            public function findStudent(Request $request){
                $data = array();
                if($request->isMethod('post')){
                    $data = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                    ->where('class_type_id',$request->class_type_id)->orderBy('id','DESC')->get();
                    $userData ='<option value="">Select</option>';
                    foreach($data as $user){
                        $userData.='
                        <option value="'.$user['id'].'">'.$user['first_name'].'&nbsp;'.$user['last_name'].'</option>';
                    }
                    echo $userData;
                }
            } 
            
            public function uploadHourlyHomework(Request $request){
                if($request->isMethod('post')){
                    $request->validate([]);
                    if($request->hasfile('student_upload_file')){
                        $image = $request->file('student_upload_file');
                        $path = $image->getRealPath();      
                        $document =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'uploadHomework';
                        $image->move($destinationPath, $document);     
                    }
                    $uploadHW = HourlyHomework::where('id',$request->homework_id)->update(['message'=>$request->message,'student_upload_file'=>$document]);
                    /*for ($count = 0; $count <= count($request->content_file); $count++) {
                        if (isset($request->content_file[$count])) {   
                            $uploadDocument = new HomeworkDocuments;//model name
                            if($request->hasfile('content_file')){
                                $image = $request->file('content_file')[$count];
                                $path = $image->getRealPath();      
                                $document =  time().uniqid().$image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH').'uploadHomework';
                                $image->move($destinationPath, $document);     
                            }
                            $uploadDocument->user_id = Session::get('id');
                            $uploadDocument->session_id = Session::get('session_id');
                            $uploadDocument->branch_id = Session::get('branch_id');   
                            $uploadDocument->admission_id = Session::get('id');
                            $uploadDocument->upload_hw_id  = $upload_hw_id;
                            $uploadDocument->content_file  = $document;
                            $uploadDocument->save(); 
                        }
                    }*/ 
                    return redirect::to('hourly/hw/view')->with('message', ' Assignment Upload Successfully.');
                }
            } 

            public function hourlyHomeworkDetails(Request $request, $id){
                $students = HourlyHomework::select('hourly_homework.*','admission.first_name as first_name','admission.last_name as last_name','admission.father_name')           
		        ->leftjoin('admissions as admission','admission.id','hourly_homework.admission_id')->where('hourly_homework.id',$id)->with('ClassType');
        		if(Session::get('role_id') == 1){
        		   $students =  $students->orderBy('id','DESC')->groupBy('hourly_homework.admission_id')->get();
        		}
        		else if(Session::get('role_id') == 2){
		            $students = $students->where('hourly_homework.class_type_id',Session::get('class_type_id'))->groupBy('hourly_homework.admission_id')->orderBy('id','DESC')->get();
		        }
		        else{
    		        $students = $students->where('hourly_homework.class_type_id',Session::get('class_type_id'))->where('hourly_homework.admission_id',Session::get('id'))->groupBy('hourly_homework.admission_id')->orderBy('id','DESC')->get();
    		    }
            
                return view('master.home_work.hourly.details',['students'=>$students, 'id'=>$id]);
            }

            public function particularHourlyHomeworkDetails(Request $request){
                $data = HourlyHomework::with('Admission')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('admission_id',$request->admission_id)->orderBy('id','DESC')->get();
                return view('master.home_work.hourly.data_homework',['data'=>$data]);
            }

            public function evaluateHourlyHomework(Request $request){
                if($request->isMethod('post')){
                  $status = '1';
                    foreach($request->id as $key=>$item){
                            $data = HourlyHomework::where('id',$request->id[$key])->update(['hw_review'=>$request->review[$key],'status'=>$status,'evaluate_date'=>date('Y-m-d'),'teacher_id'=>Session::get('teacher_id')]);
                        }
                    return redirect::to('hourly/hw/view')->with('message', ' Homework Evaluated Successfully.');
                }
            }  
    
            public function studentview(Request $request){
                $homework = Homework::select('homeworks.*')->with('Subject')->with('ClassType')->with('Teacher')
    		    ->leftjoin('admissions as admission','admission.class_type_id','homeworks.class_type_id')
    		    ->where('homeworks.session_id',Session::get('session_id'))
    		    ->where('homeworks.branch_id',Session::get('branch_id'))->where('homeworks.class_type_id',Session::get('class_type_id'))
                ->whereDate('homeworks.homework_issue_date','<=', date('Y-m-d'));
        		$allhomework = $homework->groupBy('homeworks.id')->orderBy('id','DESC')->get();
                return view('master.home_work.student_view.index',['data'=>$allhomework]);
            
            }
    
}
