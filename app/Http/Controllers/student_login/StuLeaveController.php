<?php

namespace App\Http\Controllers\student_login;

use App\Models\Master\LeaveManagement;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\User;
use App\Models\ClassType;
use App\Models\Admission;
use Session;
use Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StuLeaveController extends Controller
{
    public function leaveAdd(Request $request)
    {
        $admissionId = Session::get('id');
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $classTypeId = Session::get('class_type_id');

        if ($request->isMethod('post')) {
            $request->validate([
                'subject' => 'required|string|max:255',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'required|string',
            ]);

            $add_leave = new LeaveManagement;
            $add_leave->session_id = $sessionId;
            $add_leave->branch_id = $branchId;
            $add_leave->admission_id = $admissionId;
            $add_leave->class_type_id = $classTypeId;
            $add_leave->subject = trim($request->subject);
            $add_leave->from_date = $request->from_date;
            $add_leave->to_date = $request->to_date;
            $add_leave->reason = trim($request->reason);
            $add_leave->status = '2'; // 2 = Pending
            $add_leave->save();

            // Safe notification delivery without blocking UI performance
            try {
                $template = MessageTemplate::select('message_templates.*', 'message_types.slug')
                    ->leftJoin('message_types', 'message_types.id', '=', 'message_templates.message_type_id')
                    ->where('message_types.status', 1)
                    ->where('message_types.slug', 'Leaves')
                    ->first();

                $adminMail = User::where('id', '1')->first();
                $branch = Branch::find($branchId);
                $className = ClassType::where('id', $classTypeId)->value('name') ?? '';
                $student = Admission::find($admissionId);

                if ($template && $template->status != 1 && $branch && $branch->whatsapp_srvc != 0 && $adminMail && !empty($adminMail->mobile)) {
                    if ($template->whatsapp_status != 0) {
                        $search = ['{#admin_name#}', '{#from_date#}', '{#to_date#}', '{#reason#}', '{#name#}', '{#class_name#}'];
                        $replace = [
                            trim(($adminMail->first_name ?? '') . ' ' . ($adminMail->last_name ?? '')),
                            date('d-M-Y', strtotime($request->from_date)),
                            date('d-M-Y', strtotime($request->to_date)),
                            $request->reason,
                            trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                            $className
                        ];
                        $whatsapp = str_replace($search, $replace, $template->whatsapp_content);
                        Helper::sendWhatsappMessage($adminMail->mobile, $whatsapp);
                    }
                }
            } catch (\Throwable $e) {
                // Silently skip notification failures to preserve page speed
            }

            return redirect('applyLeaveStudent')->with('message', 'Leave application submitted successfully.');
        }

        // Optimized query: Only fetch the logged-in student's leaves
        $dataview = LeaveManagement::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('admission_id', $admissionId)
            ->orderBy('id', 'DESC')
            ->get();

        return Helper::view('student_login.leave.student_leave', ['dataview' => $dataview]);
    }

    public function leaveEdit(Request $request, $id)
    {
        $admissionId = Session::get('id');
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');

        $data = LeaveManagement::where('id', $id)
            ->where('admission_id', $admissionId)
            ->firstOrFail();

        if ($request->isMethod('post')) {
            $request->validate([
                'subject' => 'required|string|max:255',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'reason' => 'required|string',
            ]);

            $data->subject = trim($request->subject);
            $data->from_date = $request->from_date;
            $data->to_date = $request->to_date;
            $data->reason = trim($request->reason);
            $data->save();

            return redirect('applyLeaveStudent')->with('message', 'Leave application updated successfully.');
        }

        $dataview = LeaveManagement::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('admission_id', $admissionId)
            ->orderBy('id', 'DESC')
            ->get();

        return Helper::view('student_login.leave.edit_student_leave', ['data' => $data, 'dataview' => $dataview]);
    }

    public function leaveDelete(Request $request)
    {
        $id = $request->delete_id;
        $admissionId = Session::get('id');

        LeaveManagement::where('id', $id)
            ->where('admission_id', $admissionId)
            ->delete();

        return redirect('applyLeaveStudent')->with('message', 'Leave application cancelled successfully.');
    }
}