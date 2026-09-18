<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\ExamTerm;
use App\Models\exam\AssignExam;
use App\Models\Admission;
use App\Models\Notification;
use App\Services\FcmDirectService;
use DB;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function viewExam(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');

        $searchName = trim($request->input('name', ''));
        $termId = $request->input('term_id');
        $classTypeId = $request->input('class_type_id');
        $status = $request->input('status');
        $page = max(1, (int) $request->input('page', 1));
        $perPageInput = $request->input('per_page', 25);
        $perPage = ($perPageInput === 'all' || (int)$perPageInput === 0) ? 999999 : max(1, (int) $perPageInput);

        // Base Query with Filters
        $query = Exam::query()
            ->leftJoin('exam_terms', 'exam_terms.id', '=', 'exams.exam_term_id')
            ->where('exams.session_id', $sessionId)
            ->where('exams.branch_id', $branchId)
            ->whereNull('exams.deleted_at');

        if (!empty($searchName)) {
            $query->where('exams.name', 'like', '%' . $searchName . '%');
        }

        if (!empty($termId)) {
            $query->where('exams.exam_term_id', $termId);
        }

        if (!empty($classTypeId)) {
            $query->whereExists(function($q) use ($classTypeId, $sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.class_type_id', $classTypeId)
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        }

        if ($status === 'assigned') {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        } elseif ($status === 'unassigned') {
            $query->whereNotExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        } elseif ($status === 'published' && Schema::hasTable('exam_result_publications')) {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('exam_result_publications')
                  ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                  ->where('exam_result_publications.session_id', $sessionId)
                  ->where('exam_result_publications.branch_id', $branchId);
            });
        } elseif ($status === 'pending') {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
            if (Schema::hasTable('exam_result_publications')) {
                $query->whereNotExists(function($q) use ($sessionId, $branchId) {
                    $q->select(DB::raw(1))
                      ->from('exam_result_publications')
                      ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                      ->where('exam_result_publications.session_id', $sessionId)
                      ->where('exam_result_publications.branch_id', $branchId);
                });
            }
        }

        // Total Count for Pagination
        $totalCount = $query->count('exams.id');
        $lastPage = $perPage > 0 ? (int) ceil($totalCount / $perPage) : 1;
        if ($page > $lastPage && $lastPage > 0) {
            $page = $lastPage;
        }
        $offset = ($page - 1) * $perPage;

        // Fetch ONLY Requested Page Slice
        $data = $query->select('exams.id', 'exams.name', 'exams.exam_date', 'exams.exam_term_id', 'exam_terms.name as exam_term_name')
            ->orderBy('exams.id', 'DESC')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $examIds = $data->pluck('id')->toArray();
        $examNames = $data->pluck('name');

        // Batch load assigned classes only for current page slice
        $allAssignedClasses = collect();
        if (!empty($examIds)) {
            $allAssignedClasses = AssignExam::query()
                ->join('class_types', 'class_types.id', '=', 'assign_exams.class_type_id')
                ->whereIn('assign_exams.exam_id', $examIds)
                ->where('assign_exams.session_id', $sessionId)
                ->where('assign_exams.branch_id', $branchId)
                ->whereNull('assign_exams.deleted_at')
                ->select('assign_exams.exam_id', 'assign_exams.class_type_id', 'assign_exams.exam_date', 'class_types.name as class_name')
                ->groupBy('assign_exams.exam_id', 'assign_exams.class_type_id', 'assign_exams.exam_date', 'class_types.name')
                ->get()
                ->groupBy('exam_id');
        }

        // Batch load result publication statuses only for current page slice
        $publishedSet = [];
        if (!empty($examIds) && Schema::hasTable('exam_result_publications')) {
            $publishedRecords = DB::table('exam_result_publications')
                ->whereIn('exam_id', $examIds)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->select('exam_id', 'class_type_id')
                ->get();

            foreach ($publishedRecords as $pr) {
                $publishedSet[$pr->exam_id . '_' . $pr->class_type_id] = true;
            }
        }

        foreach ($data as $exam) {
            $baseName = preg_replace('/-\d+$/', '', $exam->name);
            $nextNumber = 1;
            $pattern = '/^' . preg_quote($baseName, '/') . '-(\d+)$/';

            foreach ($examNames as $existingName) {
                if (preg_match($pattern, $existingName, $matches)) {
                    $nextNumber = max($nextNumber, ((int) $matches[1]) + 1);
                }
            }

            $exam->copy_name = $baseName . '-' . $nextNumber;
            
            $examClasses = $allAssignedClasses->get($exam->id, collect());
            foreach ($examClasses as $assignedClass) {
                $assignedClass->is_published = !empty($publishedSet[$exam->id . '_' . $assignedClass->class_type_id]);
            }
            $exam->assigned_classes = $examClasses;
        }

        // Overall Global KPI Stats
        $statsTotalExams = Exam::where('session_id', $sessionId)->where('branch_id', $branchId)->whereNull('deleted_at')->count();
        $statsAssignedClasses = AssignExam::where('session_id', $sessionId)->where('branch_id', $branchId)->whereNull('deleted_at')->count();
        $statsPublished = 0;
        if (Schema::hasTable('exam_result_publications')) {
            $statsPublished = DB::table('exam_result_publications')->where('session_id', $sessionId)->where('branch_id', $branchId)->count();
        }
        $statsPending = max(0, $statsAssignedClasses - $statsPublished);

        $stats = [
            'total' => $statsTotalExams,
            'assigned' => $statsAssignedClasses,
            'published' => $statsPublished,
            'pending' => $statsPending,
        ];

        // Real-time AJAX response
        if ($request->ajax()) {
            $permission = Helper::permissioncheck(8);
            $rowsHtml = view('examination.offline_exam.exam.table_rows', [
                'data' => $data,
                'startIndex' => $offset,
                'permission' => $permission
            ])->render();

            $modalsHtml = view('examination.offline_exam.exam.modals', [
                'data' => $data
            ])->render();

            return response()->json([
                'status' => 'success',
                'html' => $rowsHtml,
                'modals_html' => $modalsHtml,
                'total' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPageInput === 'all' ? 'all' : $perPage,
                'last_page' => $lastPage,
                'from' => $totalCount === 0 ? 0 : ($offset + 1),
                'to' => min($offset + count($data), $totalCount),
                'stats' => $stats,
            ]);
        }

        return Helper::view('examination.offline_exam.exam.view', [
            'data' => $data,
            'search' => ['name' => $searchName, 'term_id' => $termId, 'class_type_id' => $classTypeId, 'status' => $status],
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'perPage' => $perPageInput,
            'lastPage' => $lastPage,
            'stats' => $stats,
            'classType' => Helper::classType(),
            'examTerms' => ExamTerm::where('session_id', $sessionId)->where('branch_id', $branchId)->orderBy('name')->get()
        ]);
    }

    public function publishResult(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
        ]);

        if (!Schema::hasTable('exam_result_publications')) {
            return Redirect::to('view/exam')->with('error', 'Result publication setup is pending. Please run database migrations.');
        }

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $examId = (int) $request->exam_id;
        $classTypeId = (int) $request->class_type_id;

        $exam = Exam::query()
            ->where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        $assignmentExists = AssignExam::query()
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->exists();

        if (!$exam || !$assignmentExists) {
            return Redirect::to('view/exam')->with('error', 'Exam or assigned class not found.');
        }

        $hasResultData = DB::table('fill_marks')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where(function ($query) {
                $query->where(function ($marks) {
                    $marks->whereNotNull('student_marks')->where('student_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('r_marks')->where('r_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('w_marks')->where('w_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('l_marks')->where('l_marks', '!=', '');
                });
            })
            ->exists();

        if (!$hasResultData) {
            return Redirect::to('view/exam')->with('error', 'Result cannot be published because marks have not been entered for this class.');
        }

        $alreadyPublished = DB::table('exam_result_publications')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->exists();

        if ($alreadyPublished) {
            return Redirect::to('view/exam')->with('message', 'Result is already published for this class.');
        }

        $students = Admission::query()
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->get();

        $title = 'Exam Result Published';
        $body = "Your result for {$exam->name} has been published. Open Exam Result to view your result.";
        $now = now();

        DB::beginTransaction();
        try {
            DB::table('exam_result_publications')->insert([
                'exam_id' => $examId,
                'class_type_id' => $classTypeId,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'published_by' => (int) Session::get('id'),
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($students as $student) {
                Notification::create([
                    'title' => $title,
                    'content' => $body,
                    'type' => 'exam_result',
                    'admission_id' => (int) $student->id,
                    'user_id' => null,
                    'device_token' => null,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'message_seen' => 0,
                    'show_status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Exam result publication failed.', [
                'exam_id' => $examId,
                'class_type_id' => $classTypeId,
                'error' => $e->getMessage(),
            ]);

            return Redirect::to('view/exam')->with('error', 'Result could not be published. ' . $e->getMessage());
        }

        $pushSent = 0;
        $pushFailed = 0;
        $fcmService = new FcmDirectService();

        foreach ($students as $student) {
            $tokens = $this->studentFirebaseTokens($student);
            foreach ($tokens as $token) {
                $result = $fcmService->send(
                    $token,
                    [
                        'type' => 'exam_result',
                        'notification_type' => 'default',
                        'channel_id' => 'default',
                        'channelId' => 'default',
                        'exam_id' => (string) $examId,
                        'class_type_id' => (string) $classTypeId,
                    ],
                    'high',
                    $title,
                    $body
                );

                if (!empty($result['success'])) {
                    $pushSent++;
                } else {
                    $pushFailed++;
                }
            }
        }

        return Redirect::to('view/exam')->with(
            'message',
            "Result published successfully. Students notified: {$students->count()}. Push sent: {$pushSent}. Failed: {$pushFailed}."
        );
    }

    public function resetResultPublication(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
        ]);

        if (!Schema::hasTable('exam_result_publications')) {
            return Redirect::to('view/exam')->with('error', 'Result publication setup is not available.');
        }

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $examId = (int) $request->exam_id;
        $classTypeId = (int) $request->class_type_id;

        $exam = Exam::query()
            ->where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->first();

        $assignmentExists = AssignExam::query()
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exam || !$assignmentExists) {
            return Redirect::to('view/exam')->with('error', 'Exam or assigned class not found.');
        }

        $deleted = DB::table('exam_result_publications')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->delete();

        if (!$deleted) {
            return Redirect::to('view/exam')->with('error', 'This result is not currently published.');
        }

        return Redirect::to('view/exam')->with(
            'message',
            "{$exam->name} result publication has been reset. Students can no longer view this result."
        );
    }

    private function studentFirebaseTokens(Admission $student): array
    {
        if (!Schema::hasTable('notification_tokens')) {
            return [];
        }

        $attendanceUniqueId = trim((string) ($student->attendance_unique_id ?? ''));
        $hasAdmissionId = Schema::hasColumn('notification_tokens', 'admission_id');
        $hasAttendanceUniqueId = Schema::hasColumn('notification_tokens', 'attendance_unique_id');

        if (!$hasAdmissionId && (!$hasAttendanceUniqueId || $attendanceUniqueId === '')) {
            return [];
        }

        $query = DB::table('notification_tokens')
            ->where(function ($query) use ($student, $attendanceUniqueId, $hasAdmissionId, $hasAttendanceUniqueId) {
                if ($hasAdmissionId) {
                    $query->where('admission_id', (int) $student->id);
                }
                if ($hasAttendanceUniqueId && $attendanceUniqueId !== '') {
                    $query->orWhere('attendance_unique_id', $attendanceUniqueId);
                }
            })
            ->where('platform', 'android')
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '');

        if (Schema::hasColumn('notification_tokens', 'branch_id')) {
            $query->where('branch_id', (int) $student->branch_id);
        }
        if (Schema::hasColumn('notification_tokens', 'session_id')) {
            $query->where('session_id', (int) $student->session_id);
        }
        if (Schema::hasColumn('notification_tokens', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query
            ->orderByDesc('id')
            ->pluck('device_token')
            ->filter(fn ($token) => trim((string) $token) !== '')
            ->map(fn ($token) => trim((string) $token))
            ->unique()
            ->values()
            ->all();
    }

    public function copyExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'exam_date' => 'required|date',
        ]);

        $sourceExam = Exam::where('id', $request->exam_id)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->first();

        if (!$sourceExam) {
            return Redirect::to('view/exam')->with('error', 'Exam not found.');
        }

        DB::beginTransaction();

        try {
            $copiedExam = $sourceExam->replicate();
            $copiedExam->name = trim($request->name);
            $copiedExam->exam_date = $request->exam_date;
            $copiedExam->user_id = Session::get('id');
            $copiedExam->session_id = Session::get('session_id');
            $copiedExam->branch_id = Session::get('branch_id');
            $copiedExam->deleted_at = null;
            $copiedExam->save();

            $assignments = AssignExam::where('exam_id', $sourceExam->id)
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->get()
                ->unique('class_type_id');

            foreach ($assignments as $assignment) {
                $copiedAssignment = $assignment->replicate();
                $copiedAssignment->exam_id = $copiedExam->id;
                $copiedAssignment->user_id = Session::get('id');
                $copiedAssignment->session_id = Session::get('session_id');
                $copiedAssignment->branch_id = Session::get('branch_id');
                $copiedAssignment->deleted_at = null;
                $copiedAssignment->save();
            }

            DB::commit();

            return Redirect::to('view/exam')->with('message', 'Exam copied successfully with the same assigned classes.');
        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect::to('view/exam')->with('error', 'Exam could not be copied. ' . $e->getMessage());
        }
    }
    
    public function addExam(Request $request){
        if($request->isMethod('post')){
            $request->validate([
                'name' => 'required|string|max:255',
                'class_type_id' => 'required|integer',
            ]);

             DB::beginTransaction();
             try {
                 $add = new Exam;
                 $add->user_id = Session::get('id');
                 $add->session_id = Session::get('session_id');
                $add->branch_id = Session::get('branch_id');
                $add->name = trim($request->name);
                $add->class_type_id = $request->class_type_id;
                $add->exam_term_id = $request->exam_term_id;
                $add->description = $request->description;
                 $add->save();

                 $assignment = AssignExam::withTrashed()
                     ->where('exam_id', $add->id)
                     ->where('class_type_id', $request->class_type_id)
                     ->where('session_id', Session::get('session_id'))
                     ->where('branch_id', Session::get('branch_id'))
                     ->first();

                 if (!$assignment) {
                     $assignment = new AssignExam;
                 }

                 $assignment->exam_id = $add->id;
                 $assignment->class_type_id = $request->class_type_id;
                 $assignment->session_id = Session::get('session_id');
                 $assignment->branch_id = Session::get('branch_id');
                 $assignment->user_id = Session::get('id');
                 $assignment->deleted_at = null;
                 $assignment->save();

                 DB::commit();

                 return Redirect::to('fill_marks?class_type_id='.$request->class_type_id.'&exam_id='.$add->id)
                     ->with('message', 'Exam created and assigned to the class. You can now fill marks.');
             } catch (\Exception $e) {
                 DB::rollBack();
                 return Redirect::back()->withInput()->with('error', 'Exam could not be created. '.$e->getMessage());
             }
        }

        return view('examination.offline_exam.exam.add');
    } 
    
     public function editExam(Request $request, $id){
         $data = Exam::find($id);
         
        
            if($request->isMethod('post')){
                $request->validate([

         'name'  => 'required',
   
         ]);

	     $data->user_id = Session::get('id');
	     $data->session_id = Session::get('session_id');
         $data->branch_id = Session::get('branch_id');	     
		 $data->name =$request->name;
		 $data->class_type_id =$request->class_type_id;
		 $data->exam_term_id =$request->exam_term_id;
	     $data->save();

            return redirect::to('view/exam')->with('message', 'Exam Updated Successfully.');
        }

        return view('examination.offline_exam.exam.edit',['data'=>$data]);
    } 
    
   public function assignExam(Request $request, $id){
        $examId = $id;
          $data2 = Exam::where('id',$id)->first();
        $AssignExam = AssignExam::select('assign_exams.*','class_types.name as class_name')
        ->leftjoin('class_types','class_types.id','assign_exams.class_type_id')->where('assign_exams.exam_id',$id)->where('assign_exams.session_id',Session::get('session_id'))->where('assign_exams.branch_id',Session::get('branch_id'))->get();
        
        
          if($request->isMethod('post')){
                 if ($request->input('submit_action') === 'global') {
                     $request->validate([
                         'exam_date' => 'required|date',
                     ]);
                 } else {
                     $request->validate([
                         'class_type_id'  => 'required',
                         'exam_date'  => 'required|date',
                     ]);
                 }

         if ($request->input('submit_action') === 'global') {
             $updated = AssignExam::where('exam_id', $examId)
                 ->where('session_id', Session::get('session_id'))
                 ->where('branch_id', Session::get('branch_id'))
                 ->whereNull('deleted_at')
                 ->update(['exam_date' => $request->exam_date]);

             return redirect::to('assign/exam/'.$examId)->with('message', $updated > 0
                 ? 'Exam date applied to all assigned classes successfully.'
                 : 'No assigned classes found to update.');
         }

         if($request->filled('assign_id')){
             $assignment = AssignExam::where('id', $request->assign_id)
                ->where('exam_id', $examId)
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->first();

             if(!$assignment){
                 return redirect::to('assign/exam/'.$examId)->with('error', 'Assignment not found !');
             }

             $assignment->exam_date = $request->exam_date;
             $assignment->save();

             return redirect::to('assign/exam/'.$examId)->with('message', 'Exam date updated successfully.');
         }

         $old = AssignExam::where('class_type_id',$request->class_type_id)->where('exam_id',$examId)->first();
         if(!empty($old)){
           $old->exam_date = $request->exam_date;
           $old->save();
           return redirect::to('assign/exam/'.$examId)->with('message', 'Exam date updated successfully.');
         }
         $add = new AssignExam; //model name
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');    
		 $add->class_type_id = $request->class_type_id;
         $add->exam_id = $examId;
         $add->exam_date = $request->exam_date;
	     $add->save();
	     
	          
		 return redirect::to('assign/exam/'.$examId)->with('message', 'Exam Assigned Successfully.');
        }
        return view('examination.offline_exam.exam.assign',['AssignExam'=>$AssignExam,'data'=>$data2]);
    } 
    

    
     public function deleteAssignExam(Request $request){
        $question = AssignExam::find($request->assign_id)->delete();
        return redirect::to('assign/exam/'.$request->exam_id)->with('message', 'Class Unassigned Successfully.');
    }

    public function deleteExam(Request $request)
    {
        $examId = (int) $request->delete_id;

        $exam = Exam::withTrashed()
            ->where('id', $examId)
            ->where('branch_id', Session::get('branch_id'))
            ->where('session_id', Session::get('session_id'))
            ->first();

        if (!$exam) {
            return Redirect::to('view/exam')->with('error', 'Exam not found.');
        }

        DB::beginTransaction();

        try {
            DB::table('examination_admit_cards')->where('exam_id', $examId)->delete();
            DB::table('examination_schedules')->where('exam_id', $examId)->delete();
            DB::table('exam_result_updates')->where('exam_id', $examId)->delete();
            DB::table('assign_questions')->where('exam_id', $examId)->delete();
            DB::table('fill_marks')->where('exam_id', $examId)->delete();
            DB::table('fill_min_max_marks')->where('exam_id', $examId)->delete();
            DB::table('fill_marks_by_excel')->where('exam_id', $examId)->delete();
            if (Schema::hasTable('exam_result_publications')) {
                DB::table('exam_result_publications')->where('exam_id', $examId)->delete();
            }
            DB::table('assign_exams')->where('exam_id', $examId)->delete();
            DB::table('exams')->where('id', $examId)->delete();

            DB::commit();

            return Redirect::to('view/exam')->with('message', 'Exam and all related data were permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect::to('view/exam')->with('error', 'Exam could not be deleted. ' . $e->getMessage());
        }
    }
    
    
    public function viewExamTerm(Request $request){
        $search['name'] = $request->name;
            $data = ExamTerm::select('exam_terms.*')
            ->where('exam_terms.session_id',Session::get('session_id'))
            ->where('exam_terms.branch_id',Session::get('branch_id'));

            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("exam_terms.name",'like','%'.$request->name.'%');
                }
            }
            $data = $data->groupBy('exam_terms.id')->orderBy('id','DESC')->get();
          
          
     //    dd(Session::get('role_id'));

     
      
      
    return view('examination.offline_exam.exam_term.view ',['data'=>$data,'search'=>$search]);
    }
    
    
    public function addExamTerm(Request $request){
         if($request->isMethod('post')){
                 $request->validate([
                     
         'name'  => 'required',
        //  'class_type_id'  => 'required',
        
         ]);
         $add = new ExamTerm;//model name
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');
		 $add->name =$request->name;
	     $add->save();
	
		  return redirect::to('view/exam_term')->with('message', 'Exam Term added Successfully.');
        }

        return view('examination.offline_exam.exam_term.add');
    } 
    
     public function editExamTerm(Request $request, $id){
         $data = ExamTerm::find($id);
            if($request->isMethod('post')){
                $request->validate([

         'name'  => 'required',
        //  'class_type_id'  => 'required',
   
         ]);

	     $data->user_id = Session::get('id');
         $data->session_id = Session::get('session_id');
         $data->branch_id = Session::get('branch_id');	     
		 $data->name =$request->name;
	     $data->save();

            return redirect::to('view/exam_term')->with('message', 'Exam Term Updated Successfully.');
        }

        return view('examination.offline_exam.exam_term.edit',['data'=>$data]);
    } 
    
    public function deleteExamTerm(Request $request)
{
    $examTerm = ExamTerm::find($request->id);

    if (!$examTerm) {
        return Redirect::to('view/exam_term')->with('error', 'Exam Term not found.');
    }

    $examTerm->delete();

    return Redirect::to('view/exam_term')->with('message', 'Exam Term Deleted Successfully.');
}
    
}
