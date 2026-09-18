<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\Salary;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\StaffAttendance;
use App\Models\TeacherCategory;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\Teacher;
use App\Models\Master\MessageTemplate;
use App\Models\AttendanceStatus;
use App\Models\Setting;
use App\Models\Master\Branch;
use App\Models\CronJobs;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CronJobController extends Controller

{

 
public function attendanceSendMassage()
{
    $todate = date('Y-m-d');

    // 1️⃣ Normalize attendance first
    $this->updateAttendanceStatus($todate);

    // 2️⃣ Send messages (IN / OUT)
    $this->attendanceMessageStatuss();
}

public function attendanceMessageStatuss()
{
    $todate = date('Y-m-d');

    $students = StudentAttendance::leftJoin('admissions','admissions.id','student_attendance.admission_id')
        ->whereDate('student_attendance.date', $todate)
        ->where('student_attendance.message_status', 0) // ONLY UNSENT
        ->select(
            'student_attendance.id',
            'student_attendance.admission_id',
            'student_attendance.attendance_status_message',
            'student_attendance.time',
            'student_attendance.date',
            'admissions.first_name',
            'admissions.mobile',
            'admissions.class_type_id'
        )
        ->distinct('student_attendance.admission_id')
        ->get();

    foreach ($students as $student) {

        if (empty($student->mobile)) {
            continue;
        }

        $array = json_decode($student->attendance_status_message, true);
        if (!is_array($array) || empty($array[0])) continue;

        /* ===================== IN MESSAGE ===================== */
        if (!empty($array[0]['in']) && ($array[0]['in_message_status'] ?? '') != 'Checked') {

            $SmsSetting = SmsSetting::where('category','StudentIN')->first();
            if ($SmsSetting) {

                $message = $SmsSetting->message;
                $message = preg_replace('/\{#var#\}/', $student->first_name, $message, 1);
                $message = preg_replace('/\{#var#\}/', $array[0]['in'], $message, 1);
                $message = preg_replace('/\{#var#\}/', $student->date, $message, 1);

                Helper::SendMessage(
                    $student->mobile,
                    $message,
                    $SmsSetting->template_id,
                    $SmsSetting->apirequest
                );

                $array[0]['in_message_status'] = 'Checked';
            }
        }

        /* ===================== OUT MESSAGE ===================== */
        if (!empty($array[0]['out']) && ($array[0]['out_message_status'] ?? '') != 'Checked') {

            $SmsSetting = SmsSetting::where('category','StudentOUT')->first();
            if ($SmsSetting) {

                $message = $SmsSetting->message;
                $message = preg_replace('/\{#var#\}/', $student->first_name, $message, 1);
                $message = preg_replace('/\{#var#\}/', $array[0]['out'], $message, 1);
                $message = preg_replace('/\{#var#\}/', $student->date, $message, 1);

                Helper::SendMessage(
                    $student->mobile,
                    $message,
                    $SmsSetting->template_id,
                    $SmsSetting->apirequest
                );

                $array[0]['out_message_status'] = 'Checked';
            }
        }

        /* ===================== SAVE FINAL ===================== */
        StudentAttendance::where('id', $student->id)->update([
            'attendance_status_message' => json_encode($array),
            'message_status' => 1, // 🔒 LOCKED
            'class_type_id' => $student->class_type_id, // 🔒 LOCKED
        ]);
    }
}



public function updateAttendanceStatus($todate)
{
    // Get unique students for today
    $admissionIds = StudentAttendance::whereDate('date', $todate)
        ->orderBy('id', 'ASC')
        ->pluck('admission_id')
        ->unique();

    foreach ($admissionIds as $admissionId) {

        // Get all records of a student for today
        $records = StudentAttendance::whereDate('date', $todate)
            ->where('admission_id', $admissionId)
            ->orderBy('id', 'ASC')
            ->get();

        if ($records->isEmpty()) continue;

        // First record = MASTER record
        $master = $records->first();

        // Prepare default JSON
        $status = [
            [
                'biomatric' => 'yes',
                'in' => '',
                'in_message_status' => '',
                'out' => '',
                'out_message_status' => ''
            ]
        ];

        if (!empty($master->attendance_status_message)) {
            $status = json_decode($master->attendance_status_message, true);
        }

        /* ===================== IN TIME ===================== */
        $inTime = Carbon::parse($records->first()->time);
        $status[0]['in'] = $inTime->format('H:i');

        /* ===================== OUT TIME ===================== */
        if ($records->count() > 1) {

            $lastRecord = $records->last();
            $outTime = Carbon::parse($lastRecord->time);

            // Minimum 10 min difference
            if ($inTime->diffInMinutes($outTime) >= 10) {
                $status[0]['out'] = $outTime->format('H:i');
            }
        }

        // Save MASTER
        $master->attendance_status_message = json_encode($status);
        $master->save();

        /* ===================== DELETE DUPLICATES ===================== */
        if ($records->count() > 1) {
            $records->skip(1)->each(function ($item) {
                $item->forceDelete();
            });
        }
    }
}












    
   
}
