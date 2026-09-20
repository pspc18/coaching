<?php
 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Redirect;
use App\Models\MessageQueue; 
use App\Jobs\SendMessageJob;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\offline_exam\MarksImportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------

| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::match(['get', 'post'], 'privacy_policy', function () {
    return view('privacy_policy');
});
Route::get('csrf-token/refresh', function () {
    return response()->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache');
})->name('csrf.refresh');
Route::match(['get', 'post'], 'test-fcm', 'MultipleCronController@dispatchAttendanceMessagesByType');
Route::match(['get', 'post'], 'attendance/messages/dispatch', 'MultipleCronController@dispatchAttendanceMessagesByType')->name('attendance.messages.dispatch');
Route::match(['get', 'post'], 'attendance/messages/send', 'MultipleCronController@sendAttendanceMessages');

Route::get('/dispatch-all-messages', function () {

    $messages = MessageQueue::where('message_status', 0)->take(50)->get();

    if ($messages->isEmpty()) {
        return "No pending messages found.";
    }

    (new SendMessageJob($messages->all()))->handle();

    return "✅ Dispatched {$messages->count()} messages in parallel.";
});
Route::get('/clear-cache', function () {
	Artisan::call('cache:clear');
	Artisan::call('route:clear');
	Artisan::call('view:clear');

	return redirect('/')->with('message', 'Cache clear successfully.');
});

Route::match(['get', 'post'], 'set_session_count', 'WhatsappController@setCountSession');



Route::match(['get', 'post'], 'folderCompressor', 'StudentsAdmissionController@folderCompressor');
Route::match(['get', 'post'], 'folderClear', 'Auth\AuthController@folderClear');

Route::get('/access-denied', function () {
    return view('access.denied');
})->name('access.denied');

Route::match(['get','post'],'attendanceSendMassage', 'CronJobController@attendanceSendMassage');
Route::match(['get','post'],'birthday_auto_massage', 'CronJobController@birthday_auto_massage');
Route::match(['get','post'],'attendance/detect-type', 'MultipleCronController@detectAttendanceType');
Route::match(['get','post'],'attendance/attendance-cron-manager', 'MultipleCronController@detectAttendanceType')->name('attendance.attendance-cron-manager');
// Public cron endpoints: intentionally outside the authenticated route group.
Route::match(['get','post'],'attendance/tomorrow-holiday-notification-cron-manager', 'MultipleCronController@nextDayHolidayNotification')->name('attendance.tomorrow-holiday-notification-cron-manager');
Route::match(['get','post'],'attendance/today-holiday-notification-cron-manager', 'MultipleCronController@todayHolidayNotification')->name('attendance.today-holiday-notification-cron-manager');
Route::post('attendance-biomatric', 'MultipleCronController@testBiometricAttendance')
    ->name('attendance-biomatric')
    ->middleware('guest');
Route::match(['get', 'post'], 'login', 'Auth\AuthController@getLogin');
Route::match(['get', 'post'], 'mpin-login', 'Auth\AuthController@mpinLogin');
Route::match(['get', 'post'], 'save-mpin', 'Auth\AuthController@saveMpin');

Route::group(['middleware' => 'islogin'], function () {

Route::get('softwareTokenStatus', function(){ session()->put('softwareTokenStatus',1); });

Route::match(['get', 'post'], 'whatsappOtpRequest', 'Auth\AuthController@whatsappOtpRequest');
Route::match(['get', 'post'], 'logout', 'Auth\AuthController@logout');
Route::match(['get', 'post'], 'sibling/login/{id}', 'Auth\AuthController@siblingLogin');
Route::match(['get', 'post'], 'siblingList', 'Auth\AuthController@siblingList');

Route::any('newStudentRegistration', 'Auth\AuthController@newStudentRegistration');

Route::match(['get', 'post'], 'qr_code', 'EnquiryController@qrCode'); 


    
Route::match(['get', 'post'], 'firebase-notification', 'NotificationController@notification');

Route::get('user-notifications', 'UserNotificationController@index')->name('user.notifications.index');
Route::post('user-notifications/{id}/mark-read', 'UserNotificationController@markRead')
    ->where('id', '[0-9]+')->name('user.notifications.mark-read');
Route::post('user-notifications/mark-all-read', 'UserNotificationController@markAllRead')
    ->name('user.notifications.mark-all-read');
Route::delete('user-notifications/clear-all', 'UserNotificationController@clearAll')
    ->name('user.notifications.clear-all');




//Staff
Route::get('staff_file', 'StaffController@staffFile');
Route::match(['get', 'post'], 'teachers/add', 'StaffController@add');
Route::match(['get', 'post'], 'imageDeletestaff', 'StaffController@deleteImage');
Route::match(['get', 'post'], 'teachers/index', 'StaffController@index');
Route::get('joining_letter_print/{id}', 'StaffController@joiningLater');
Route::get('teachers_idCard/{id}', 'StaffController@teachersidCard');
Route::match(['get', 'post'], 'teachers/edit/{id}', 'StaffController@edit');
Route::match(['get', 'post'], 'delete_staff', 'StaffController@delete');
Route::match(['get', 'post'], 'teacher/image', 'StaffController@teacherUpdateImg');
Route::match(['get', 'post'], 'staff_attendance_add', 'StaffAttendanceController@add');
Route::match(['get', 'post'], 'staff/Attendance/view', 'StaffAttendanceController@view');
Route::match(['get', 'post'], 'drop_index', 'StaffController@Dropindex');
Route::match(['get', 'post'], 'getDataClassSubject', 'StaffController@getDataClassSubject');

//CallLog
Route::match(['get', 'post'], 'reception_file', 'ReceptionController@receptionfile');
Route::match(['get', 'post'], 'callLog/add', 'ReceptionController@createCallLog');
Route::match(['get', 'post'], 'callLogDelete', 'ReceptionController@callLogDelete');


//VisterController
Route::match(['get', 'post'], 'visitorAdd', 'VisitorController@visitorAdd');
Route::match(['get', 'post'], 'visitorView', 'VisitorController@visitorView');
Route::match(['get', 'post'], 'visitorEdit/{id}', 'VisitorController@visitorEdit');
Route::match(['get', 'post'], 'visitorDelete', 'VisitorController@visitorDelete');


//BalanceSheetControllers
Route::match(['get','post'],'add_balance_sheet', 'BalanceSheetControllers@add'); 
Route::match(['get','post'],'view_balance_sheet', 'BalanceSheetControllers@view'); 
Route::match(['get','post'],'edit_balance_sheet', 'BalanceSheetControllers@edit');
Route::match(['get','post'],'filter_balance', 'BalanceSheetControllers@filterBalance');
Route::match(['get','post'],'delete_balance', 'BalanceSheetControllers@delete');

//EnquiryController
Route::match(['get', 'post'], 'studentsDashboard', 'EnquiryController@studentsDashboard');
Route::match(['get', 'post'], 'enquiryAdd', 'EnquiryController@enquiryAdd');
Route::match(['get', 'post'], 'enquiryView', 'EnquiryController@enquiryView');
Route::get('registrationPrint/{id}', 'EnquiryController@registrationPrint');
Route::post('enquiryFollowUpAdd/{id}', 'EnquiryController@enquiryFollowUpAdd');
Route::post('followupDelete', 'EnquiryController@followupDelete');
Route::match(['get', 'post'], 'enquiryEdit/{id}', 'EnquiryController@enquiryEdit');
Route::match(['get', 'post'], 'enquiryDelete', 'EnquiryController@enquiryDelete');
Route::match(['get', 'post'], 'studentRegistrationDetail/{id}', 'EnquiryController@studentRegistrationDetail');



//Route::post('/get-students-by-class', 'StudentsAdmissionController@getStudentsByClass');

//Students Admission
Route::match(['get', 'post'], 'admissionAdd', 'StudentsAdmissionController@admissionAdd');
Route::match(['get', 'post'], 'admissionView', 'StudentsAdmissionController@admissionView');
Route::match(['get', 'post'], 'studentDetail/{id}', 'StudentsAdmissionController@studentDetail');
Route::get('studentDetail/{id}/export', 'StudentsAdmissionController@studentProfileExcel')->name('student.profile.excel');
Route::get('admissionStudentIdPrint/{id}', 'StudentsAdmissionController@admissionStudentIdPrint');
Route::get('admissionStudentPrint/{id}', 'StudentsAdmissionController@admissionStudentPrint');
Route::match(['get', 'post'], 'admissionEdit/{id}', 'StudentsAdmissionController@admissionEdit');
Route::match(['get', 'post'], 'admissionDelete', 'StudentsAdmissionController@admissionDelete');
Route::match(['get', 'post'], 'checkStudentData', 'StudentsAdmissionController@checkStudentData');

Route::match(['get', 'post'], 'document_delete/{id}', 'ProfileController@document_delete');
Route::match(['get', 'post'], 'stream_update', 'StudentsAdmissionController@streamUpdate');
Route::match(['get', 'post'], 'category_wise_report', 'StudentsAdmissionController@category_wise_report');
Route::match(['get', 'post'], 'login_credential_reports', 'StudentsAdmissionController@login_credential_reports');
Route::get('login_credential_reports/pdf', 'StudentsAdmissionController@loginCredentialReportsPdf')->name('login-credential-reports.pdf');
Route::get('login_credential_reports/excel', 'StudentsAdmissionController@loginCredentialReportsExcel')->name('login-credential-reports.excel');
Route::get('student_logs', 'StudentsAdmissionController@student_logs');
Route::match(['get', 'post'], 'studentCsvImport', 'StudentCsvImportController@studentCsvImport');
Route::match(['get', 'post'], 'imageRotateSave', 'StudentsAdmissionController@imageRotateSave');


//Student Attendence
// Route::match(['get', 'post'], 'studentsAttendanceAdd', 'StudentAttendanceController@add');
// Route::match(['get', 'post'], 'studentsAttendanceViewTable', 'StudentAttendanceController@viewTable');
// Route::match(['get', 'post'], 'SearchValueAtten', 'StudentAttendanceController@SearchValueAtten');
// Route::match(['get', 'post'], 'studentsAttendancdDelete', 'StudentAttendanceController@attendancedelete');


//Students Id
Route::match(['get', 'post'], 'student/id/index', 'StudentsIdController@studentIdIndex');

//Promote add
Route::match(['get', 'post'], 'student/promote_add', 'PromoteController@promoteAdd');
Route::match(['get', 'post'], 'studentsPromoteAdd', 'PromoteController@studentsPromoteAdd');
Route::match(['get', 'post'], 'get-class-by-session', 'PromoteController@getClassBySession');

// Promote Add End... //



Route::match(['get', 'post'],'fill-marks-by-excel', [MarksImportController::class, 'FillMarksByExcel'])->name('student.subject.excel');
Route::post('marks-mapping-prepare', [MarksImportController::class, 'prepareMapping'])->name('marks.mapping.prepare');
Route::post('marks-mapping-save', [MarksImportController::class, 'saveMappedMarks'])->name('marks.mapping.save');






//Certificate Dashboard
Route::match(['get', 'post'], 'certificate_dashboard', 'CertificateController@certificate_dashboard');
Route::match(['get', 'post'], 'cc/form/add', 'CertificateController@add');
Route::match(['get', 'post'], 'cc/form/index', 'CertificateController@ccFormIndex');
Route::match(['get', 'post'], 'evente/certificate/add', 'CertificateController@eventeCertificateAdd');
Route::match(['get', 'post'], 'evente/certificate/index', 'CertificateController@certificateIndex');
Route::match(['get', 'post'], 'sport/certificate/add', 'CertificateController@sportAdd');
Route::match(['get', 'post'], 'sport/certificate/index', 'CertificateController@sportndex');
Route::match(['get', 'post'], 'tc/certificate/add', 'CertificateController@tcCertificateAdd');
Route::match(['get', 'post'], 'tc/certificate/index', 'CertificateController@tcIndex');
Route::match(['get', 'post'], 'certificate_editor', 'CertificateController@certificateEditor');



// ====================== STAFF PAYROLL ROUTES ======================

Route::match(['get', 'post'], 'payroll/staff', 'StaffPayrollController@index');
Route::match(['get', 'post'], 'payroll/staff/edit', 'StaffPayrollController@edit');
Route::match(['get', 'post'], 'payroll/staff/loans', 'StaffLoanController@index');
Route::match(['get', 'post'], 'payroll/staff/deductions', 'StaffDeductionController@index');
Route::match(['get'], 'payroll/staff/slip', 'StaffPayrollController@slip');
Route::match(['get'], 'payroll/staff/slip-pdf', 'StaffPayrollController@slipPdf');
Route::get('payroll/staff/payments-excel', 'StaffPayrollController@paymentsExcel');

// ====================== END STAFF PAYROLL ROUTES ======================



//User Dashboard
Route::match(['get', 'post'], 'user_dashboard', 'UserController@user_dashboard');
Route::match(['get', 'post'], 'addUser', 'UserController@addUser'); 
Route::match(['get', 'post'], 'viewUser', 'UserController@viewUser');
Route::get('joining_letter_print_user/{id}', 'UserController@joiningLater');
Route::get('users_idCard/{id}', 'UserController@usersidCard');
Route::match(['get', 'post'], 'editUser/{id}', 'UserController@editUser');	
Route::match(['get', 'post'], 'deleteUser', 'UserController@deleteUser');
Route::match(['get', 'post'], 'user/Attendance/view', 'UserController@userAttendanceView');
Route::match(['get', 'post'], 'user_side_per', 'UserController@userSidePer');
Route::match(['get', 'post'], 'userStatus', 'UserController@userStatus');
Route::prefix('user/timetable')->group(function () {
    Route::post('save', 'UserController@saveTimeTable');
    Route::get('{userId}', 'UserController@showTimeTable');
});

//Generate Salary Slip
Route::match(['get', 'post'], 'generate/salary/slip', 'SalaryController@generateSalarySlip');
Route::match(['get', 'post'], 'salary_details', 'SalaryController@salaryDetails');
Route::match(['get', 'post'], 'assign/salary', 'SalaryController@assignSalary');
Route::match(['get', 'post'], 'stateData/{id}', 'HomeController@stateData');
Route::match(['get', 'post'], 'subjectGetData/{id}', 'HomeController@subjectGetData');

Route::match(['get', 'post'], 'attendance/settings', 'AttendanceSettingsController@index');
Route::match(['get', 'post'], 'attendance/marking-window', 'AttendanceMarkingWindowController@index')
    ->name('attendance.marking-window');
Route::match(['get', 'post'], 'attendance/mark', 'AttendanceMarkController@index');
Route::match(['get', 'post'], 'attendance/view', 'AttendanceViewController@index');
// Route::match(['get', 'post'], 'AttendanceView_staff', 'AttendanceViewController@AttendanceViewstaff');
Route::match(['get', 'post'], 'attendance/report', 'AttendanceReportController@index');
Route::match(['get', 'post'], 'attendance/self', 'SelfAttendanceController@index');
Route::match(['get', 'post'], 'add_weekend', 'AttendanceSettingsController@addWeekend');
Route::match(['get', 'post'], 'attendance/add_weekend', 'AttendanceSettingsController@addWeekend');
Route::match(['get', 'post'], 'academic_calendar', 'AttendanceSettingsController@academicCalendar');
Route::match(['get'], 'attendance/leave/approvals', 'AttendanceLeaveApprovalController@index');
Route::match(['post'], 'attendance/leave/approvals/action', 'AttendanceLeaveApprovalController@action');


Route::match(['get', 'post'], 'AttendanceView_staff', 'AttendanceViewController@AttendanceViewstaff');

Route::match(['get', 'post'], 'monthlyReport', 'AttendanceViewController@monthlyReport');

Route::match(['get','post'],'qrcode_Dashboard', 'QrCodeAttendanceController@qrcode_Dashboard');
Route::match(['get','post'],'qrcode_user', 'QrCodeAttendanceController@qrcode_user');
Route::match(['get','post'],'qrcode_student', 'QrCodeAttendanceController@qrcode_student');
Route::match(['get','post'],'qrcode_attendance', 'QrCodeAttendanceController@qrcode_attendance');


//Examination Panel Start
Route::match(['get', 'post'], 'examination_dashboard', 'ExaminationController@examinationDashboard');
Route::match(['get', 'post'], 'view/exam', 'offline_exam\ExamController@viewExam');
Route::post('exam/result/publish', 'offline_exam\ExamController@publishResult')
    ->name('exam.result.publish');
Route::post('exam/result/reset-publication', 'offline_exam\ExamController@resetResultPublication')
    ->name('exam.result.reset-publication');
Route::match(['get', 'post'], 'add/exam', 'offline_exam\ExamController@addExam');
Route::post('copy/exam', 'offline_exam\ExamController@copyExam');
Route::match(['get', 'post'], 'add/examination_schedule', 'offline_exam\ExamScheduleController@addExaminationSchedule');
Route::match(['get', 'post'], 'download_admit_card', 'offline_exam\AdmitCardController@download_admit_card');
Route::match(['get', 'post'], 'admit_card_notes', 'offline_exam\AdmitCardController@AdmitCardNotes');
Route::match(['get', 'post'], 'fill_marks', 'offline_exam\FillMarkController@fillMarks');
Route::match(['get', 'post'], 'bulk_marksheet', 'offline_exam\FillMarkController@bulk_marksheet');
Route::match(['get', 'post'], 'performance_marks', 'offline_exam\FillMarkController@performanceMarks');
Route::match(['get', 'post'], 'view/exam_term', 'offline_exam\ExamController@viewExamTerm');
Route::match(['get', 'post'], 'student_performance', 'StudentPerformanceController@studentPerformance');


//Examination Offline
Route::match(['get', 'post'], 'exam_wise_report', 'offline_exam\ReportController@exam_wise_report');
Route::get('exam_wise_report/pdf', 'offline_exam\ReportController@downloadExamWiseReportPdf')
    ->name('exam-wise-report.pdf');
Route::match(['get', 'post'], 'exam_result_update', 'offline_exam\ExamResultUpdateController@examResultUpdate');
Route::match(['get', 'post'], 'subject_wise_report', 'offline_exam\ReportController@subjectWiseReport');
Route::match(['get', 'post'], 'green_sheet_report', 'offline_exam\ReportController@greenSheetReport');
Route::match(['get', 'post'], 'student_exam_comparison_report', 'offline_exam\ReportController@studentExamComparisonReport');
Route::match(['get', 'post'], 'student_exam_comparison_students', 'offline_exam\ReportController@studentExamComparisonStudents');


//Account Dashboard
Route::match(['get', 'post'], 'bank/account/index', 'AccountController@accountList');
Route::match(['get', 'post'], 'bank/account/add', 'AccountController@add');


//PrintFileController
 Route::match(['get', 'post'], 'printFilePanel', 'PrintFileController@printFilePanel');
Route::match(['get', 'post'], 'printFileModuleWiseView/{id}', 'PrintFileController@printFileModuleWiseView');
Route::match(['get', 'post'], 'printFilePanel', 'PrintFileController@printFilePanel');                               //Edit Module Name
Route::match(['get', 'post'], 'template/{id}', 'PrintFileController@template');
Route::match(['get', 'post'], 'feesRemainderCron', 'fees\FeesController@feesRemainderCron');

Route::match(['get', 'post'], 'sample_id_print', 'IdCardController@sample_id_print');
Route::match(['get', 'post'], 'save_template', 'IdCardController@save_template');




//FeesController
Route::match(['get', 'post'], 'fee_dashboard', 'fees\FeesController@feeDashboard');
Route::match(['get', 'post'], 'fees/index', 'fees\FeesController@viewFees');

//Fees Settings
Route::match(['get', 'post'], 'fees/settings', 'fees\FeesController@feesSettings');
Route::post('fees/settings/update', 'fees\FeesController@feesSettingsUpdate');
Route::post('fees/settings/preview-receipt', 'fees\FeesController@previewReceiptNumber');

//Fees ledger
Route::match(['get', 'post'], 'fees/ledger/collect', 'fees\FeesController@feesLedgerCollect');
	Route::match(['get', 'post'], 'ledger_update', 'fees\FeesController@ledgerUpdate');
	Route::match(['get', 'post'], 'fees/ledger', 'fees\FeesController@feesLedger');
	Route::match(['get', 'post'], 'fees_ledger_view', 'fees\FeesController@fees_ledger_view');
	Route::match(['get', 'post'], 'fees_cheque', 'fees\FeesController@fees_cheque');
	
//Fees Group
Route::match(['get', 'post'], 'feesGroup', 'fees\FeesController@feesGroup');
Route::match(['get', 'post'], 'feesGroupEdit/{id}', 'fees\FeesController@feesGroupEdit');	
Route::match(['get', 'post'], 'feesGroupDelete', 'fees\FeesController@feesGroupDelete');
Route::match(['get', 'post'], 'assignFeesMultipleStudents', 'fees\FeesController@assignFeesMultipleStudents');
Route::match(['get', 'post'], 'getStudentsList', 'fees\FeesController@getStudentsList');
Route::match(['get', 'post'], 'getMasterData', 'fees\FeesController@getMasterData');
Route::match(['get', 'post'], 'ca_report', 'fees\FeesController@caReport');


//Fees Master
Route::match(['get', 'post'], 'feesMasterAdd', 'fees\FeesMasterController@feesMaster');
Route::match(['get', 'post'], 'feesMasterEdit/{id}', 'fees\FeesMasterController@feesMasterEdit');

//Fees Collect
Route::match(['get', 'post'], 'Fees/add', 'fees\FeesController@addFees');

//AdvanceFees
Route::match(['get', 'post'], 'AdvanceFees', 'fees\AdvanceFeesController@AdvanceFees');
Route::match(['get', 'post'], 'AddAdvanceFees', 'fees\AdvanceFeesController@AddAdvanceFees');
Route::match(['get', 'post'], 'viewAdvanceFees', 'fees\AdvanceFeesController@viewAdvanceFees');

//Download Center
Route::match(['get', 'post'], 'download_center', 'DownloadController@downloadCenter');
Route::match(['get', 'post'], 'upload/content', 'DownloadController@upload');
Route::match(['get', 'post'], 'upload/content_edit/{id}', 'DownloadController@upload_edit');
Route::match(['get', 'post'], 'upload_delete', 'DownloadController@uploadDelete');
Route::match(['get', 'post'], 'assignments', 'DownloadController@assignments');
Route::match(['get', 'post'], 'syllabus', 'DownloadController@syllabus');
Route::match(['get', 'post'], 'study_material', 'DownloadController@studyMaterials');
Route::match(['get', 'post'], 'other_downloads', 'DownloadController@otherDownloads');


//SMS Service
Route::match(['get', 'post'], 'send_message_terminal', 'Sms_ServiceController@sendMessageTerminal');
Route::match(['get', 'post'], 'send_message', 'Sms_ServiceController@sendMessage');
Route::match(['get', 'post'], 'resend_message', 'Sms_ServiceController@resendMessage');

Route::match(['get', 'post'], 'happy_birthday', 'BirthdayController@happy_birthday');
Route::match(['get', 'post'], 'send_wishes', 'BirthdayController@send_wishes');



//Expenses 
Route::match(['get', 'post'], 'expenseView', 'ExpenseController@expenseView');
Route::match(['get', 'post'], 'expensePrint/{id}', 'ExpenseController@expensePrint');
Route::match(['get', 'post'], 'expenseEdit/{id}', 'ExpenseController@expenseEdit');
Route::match(['get', 'post'], 'expenseDelete', 'ExpenseController@expenseDelete');
Route::match(['get', 'post'], 'expenseAdd', 'ExpenseController@expenseAdd');


//Setting
Route::match(['get', 'post'], 'viewSetting', 'SettingsController@viewSetting');
Route::match(['get', 'post'], 'editSetting/{id}', 'SettingsController@editSetting');
Route::match(['get', 'post'], 'deleteSetting', 'SettingsController@deleteSetting');
Route::match(['get', 'post'], 'user_login_logs', 'SettingsController@login_logs');
Route::match(['get', 'post'], 'SystemStudentField', 'SettingsController@SystemStudentField');
Route::match(['get', 'post'], 'AddStudentField', 'SettingsController@AddStudentField');
Route::match(['get', 'post'], 'SystemStudentFieldStatusUpdate', 'SettingsController@SystemStudentFieldStatusUpdate');
Route::delete('/student-fields-delete/{id}','SettingsController@DeleteStudentField');
Route::post('SystemStudentFieldOrderUpdate', 'SettingsController@SystemStudentFieldOrderUpdate');
Route::post('UpdateStudentFieldLabel', 'SettingsController@UpdateStudentFieldLabel');
Route::match(['get', 'post'], 'settings_dashboard', 'SettingsController@settings_dashboard');
Route::match(['get', 'post'], 'addSetting', 'SettingsController@addSetting');
Route::match(['get', 'post'], 'addVillageList', 'SettingsController@addVillageList');
Route::match(['get', 'post'], 'deleteVillageList', 'SettingsController@deleteVillageList');

//Invantory Start
Route::match(['get', 'post'], 'invantory_dashboard', 'inventory\InvantoryController@invantoryDashboard');
Route::match(['get', 'post'], 'invantory_item_add', 'inventory\InvantoryController@addInvantoryItem');
Route::match(['get', 'post'], 'invantory_item_edit/{id}', 'inventory\InvantoryController@editInvantoryItem');
Route::match(['get', 'post'], 'invantory_item_delete', 'inventory\InvantoryController@deleteInvantoryItem');
Route::match(['get', 'post'], 'invantory_add', 'inventory\InvantoryController@addInvantory');
Route::match(['get', 'post'], 'invantory_view', 'inventory\InvantoryController@viewInvantory');
Route::match(['get', 'post'], 'invantory_edit/{id}', 'inventory\InvantoryController@editInvantory');
Route::match(['get', 'post'], 'delete_inventory_detail', 'inventory\InvantoryController@deleteInvantoryDetail');
Route::match(['get', 'post'], 'invantory_delete', 'inventory\InvantoryController@deleteInvantory');
Route::match(['get', 'post'], 'sale_inventory_view', 'inventory\SalesInvantoryController@SalesViewInvantory');
Route::match(['get', 'post'], 'sales_invantory_add', 'inventory\SalesInvantoryController@SalesAddInvantory');
Route::match(['get', 'post'], 'sales_invantory_edit/{id}', 'inventory\SalesInvantoryController@SalesEditInvantory');
Route::match(['get', 'post'], 'sale_inventory_print/{id}', 'inventory\SalesInvantoryController@sale_inventory_print');
Route::match(['get', 'post'], 'sales_invantory_delete', 'inventory\SalesInvantoryController@SalesDeleteInvantory');
Route::match(['get', 'post'], 'getInvantoryItemQtyCheck', 'inventory\SalesInvantoryController@getInvantoryItemQtyCheck');
Route::match(['get', 'post'], 'getAutoCompleteStudent', 'inventory\SalesInvantoryController@getAutoCompleteStudent');
Route::match(['get', 'post'], 'getStudentsForInventory', 'inventory\SalesInvantoryController@getStudentsForInventory');
Route::match(['get', 'post'], 'getStudentSaleHistory', 'inventory\SalesInvantoryController@getStudentSaleHistory');
//Parent/Teacher Conversation
Route::match(['get', 'post'], 'parent_teacher_conversation', 'student_login\ComplaintController@parent_teacherConversation');

//Store Management
Route::match(['get', 'post'], 'storeDashboard', 'StoreController@storeDashboard');
Route::match(['get', 'post'], 'viewStoreRequest', 'StoreController@viewStoreRequest');
Route::match(['get', 'post'], 'addStoreItem', 'StoreController@addStoreItem');
Route::match(['get', 'post'], 'addStationaryRequest', 'StoreController@addStationaryRequest');






























Route::match(['get','post'],'qrcode_attendance_save', 'QrCodeAttendanceController@qrcode_attendance_save');
Route::match(['get','post'],'user_attendence_qr_download', 'QrCodeAttendanceController@user_attendence_qr_download');
Route::match(['get','post'],'student_attendence_qr_download', 'QrCodeAttendanceController@student_attendence_qr_download');
    


Route::match(['get', 'post'], 'validateOtpWhatsapp', 'WhatsappController@validateOtpWhatsapp');

    



//User



//EnquiryController 
    
    // Route::match(['get', 'post'], 'enquiry_status_update', 'EnquiryController@enquiryStatusUpdate');
    Route::match(['get', 'post'], 'enquiryRemarkEdit', 'EnquiryController@enquiryRemarkEdit');
    Route::get('personal_details', 'EnquiryController@personal_details');
    Route::post('student_id_print_multiple', 'EnquiryController@studentIdPrintMultiple');
    Route::match(['get', 'post'], 'class_type_search', 'EnquiryController@class_type_search');
    Route::match(['get', 'post'], 'enquiry_qr_generate', 'EnquiryController@enquiry_qr_generate');


//students Admission
// Route::match(['get', 'post'], 'unique_system_id', 'StudentsAdmissionController@unique_system_id');
Route::match(['get', 'post'], 'bulkIdPrint', 'StudentsAdmissionController@bulkIdPrint');
Route::match(['get', 'post'], 'studentUserNameCreate', 'StudentsAdmissionController@studentUserNameCreate');

Route::match(['get', 'post'], 'getStreamSubjects', 'StudentsAdmissionController@getStreamSubjects');

Route::match(['get', 'post'], 'saveAdmissionDatatableFields', 'StudentsAdmissionController@saveAdmissionDatatableFields');


Route::match(['get', 'post'], 'admissionStudentSearch', 'StudentsAdmissionController@admissionStudentSearch');
Route::match(['get', 'post'], 'admissionStudentOnClick', 'StudentsAdmissionController@admissionStudentOnClick');



Route::match(['get', 'post'], 'studentExcelAdd', 'StudentCsvImportController@studentExcelAdd');

Route::match(['get', 'post'], 'studentBulkImageUpload', 'StudentsAdmissionController@studentBulkImageUpload');

Route::match(['get', 'post'], 'stream_update_save', 'StudentsAdmissionController@streamUpdateSave');
Route::post('stream_remove/{admission_id}/{subject_id}', 'StudentsAdmissionController@streamRemove');

//student attendance


// Route::match(['get', 'post'], 'getAttendanceDates', 'StudentAttendanceController@getAttendanceDates');
// Route::match(['get', 'post'], 'sundayAutoSubmitAttendance', 'StudentAttendanceController@sundayAutoSubmitAttendance');  //Need to start cron...
// Route::match(['get', 'post'], 'autoStudentAttendance', 'StudentAttendanceController@autoStudentAttendance');
// Route::match(['get', 'post'], 'studentsAttendanceView', 'StudentAttendanceController@view');

// Route::match(['get', 'post'], 'studentPanelAttendanceView', 'StudentAttendanceController@studentPanelAttendanceView');



//Invantory Start...//

	
	

	
	
	


	
	
	
	Route::match(['get', 'post'], 'getAutoCompleteInvantoryItem', 'inventory\InvantoryController@getAutoCompleteInvantoryItem');
    
    
	
	
	
	
	


//Fees Group

Route::match(['get', 'post'], 'getFeesGroup', 'fees\FeesController@getFeesGroup');
Route::match(['get', 'post'], 'createFeesInstallment', 'fees\FeesController@createFeesInstallment');
Route::match(['get', 'post'], 'createFeesInstallmentClassWise', 'fees\FeesController@createFeesInstallmentClassWise');
Route::match(['get', 'post'], 'updateAssignedFees', 'fees\FeesController@updateAssignedFees');



//Fees Group End..//
 

//Fees Master


Route::match(['get', 'post'], 'feesMasterDelete', 'fees\FeesMasterController@feesMasterDelete');
Route::match(['get', 'post'], 'mesterClassAmt', 'fees\FeesMasterController@mesterClassAmt');
//AdvanceFees



//RefundFees
Route::match(['get', 'post'], 'refund_fees', 'fees\RefundFeesController@RefundFees');
Route::match(['get', 'post'], 'AddrefundFees', 'fees\RefundFeesController@AddRefundFees');
Route::match(['get', 'post'], 'viewrefundFees', 'fees\RefundFeesController@viewRefundFees');


//Fees counter
Route::match(['get', 'post'], 'feesCounterAdd', 'fees\FeesCounterController@feesCounter');
Route::match(['get', 'post'], 'check_authentication', 'fees\FeesCounterController@checkAuthentication');
Route::match(['get', 'post'], 'feesCounterEdit/{id}', 'fees\FeesCounterController@feesCounterEdit');
Route::match(['get', 'post'], 'feesCounterDelete', 'fees\FeesCounterController@feesCounterDelete');
Route::match(['get', 'post'], 'feesCounterView', 'fees\FeesCounterController@feesCounterview');

//certificate_dashboard

Route::match(['get', 'post'], 'certificate_student_dashboard', 'CertificateController@certificate_student_dashboard');
Route::match(['get', 'post'], 'CC_Form_std_data', 'CertificateController@CCFormStdData');


Route::match(['get', 'post'], 'cc/form/edit/{id}', 'CertificateController@formEdit');
Route::match(['get', 'post'], 'student_search_certificate', 'CertificateController@certificateSearch');
Route::match(['get', 'post'], 'certificate_delete', 'CertificateController@delete');
Route::match(['get', 'post'], 'certificate_add_click', 'CertificateController@certificateAddClick');
Route::get('cc_print/{id}', 'CertificateController@ccPrint');





Route::match(['get', 'post'], 'evente/certificate/edit/{id}', 'CertificateController@certificateEdit');
Route::match(['get', 'post'], 'search_evente', 'CertificateController@eventeSearch');
Route::match(['get', 'post'], 'evente_add_click', 'CertificateController@eventeAddClick');
Route::match(['get', 'post'], 'evente_delete', 'CertificateController@evente_delete');
Route::get('evente_print/{id}', 'CertificateController@eventePrint');
 


Route::match(['get', 'post'], 'sport/certificate/edit/{id}', 'CertificateController@sportEdit');
Route::match(['get', 'post'], 'search_sport', 'CertificateController@sportSearch');
Route::match(['get', 'post'], 'sport_add_click', 'CertificateController@sportAddClick');
Route::match(['get', 'post'], 'sport_delete', 'CertificateController@sport_delete');
Route::match(['get', 'post'], 'CCFormStdData', 'CertificateController@CCFormStdData');
Route::get('sport_print/{id}', 'CertificateController@sportPrint');



Route::match(['get', 'post'], 'tc/certificate/edit/{id}', 'CertificateController@tcEdit');
Route::match(['get', 'post'], 'search_tc', 'CertificateController@tcSearch');
Route::match(['get', 'post'], 'tc_add_click', 'CertificateController@tcAddClick');
Route::match(['get', 'post'], 'tc_delete', 'CertificateController@tc_delete');
Route::get('tc_print/{id}', 'CertificateController@tcPrint');
Route::get('tc_print_formate', 'CertificateController@tcPrintFormate');
Route::match(['get', 'post'], 'noc_print/{id}', 'CertificateController@nocPrint');

//Student Dashboard

   
   

    //	Route::match(['get', 'post'], 'duedate/{id}', 'DashboardController@duedate');



  
	Route::match(['get', 'post'], 'SubmitSchedule', 'offline_exam\ExamScheduleController@SubmitSchedule');
  Route::match(['get', 'post'], 'examResultGraph', 'offline_exam\ExamResultController@examResultGraph');


   
    Route::match(['get', 'post'], 'exam_result_update_save', 'offline_exam\ExamResultUpdateController@examResultUpdateSave');

    
     
    
// Examination

   

//FeesCollect
	
	
	Route::match(['get', 'post'], 'student_fees_onclick', 'fees\FeesController@studentFeesOnclick');
	Route::match(['get', 'post'], 'student_pay_submit', 'fees\FeesController@studentPaySubmit');
	Route::match(['get', 'post'], 'inventoryPaySubmit', 'fees\FeesController@inventoryPaySubmit');
	Route::match(['get', 'post'], 'fees_search_data', 'fees\FeesController@feesSearchData');
	Route::match(['get', 'post'], 'print_payement/{id}', 'fees\FeesController@printPayement');
	Route::match(['get', 'post'], 'printFeesInvoice', 'fees\FeesController@printFeesInvoice');
	Route::match(['get', 'post'], 'print_payement_generate/{id}', 'fees\FeesController@printPayementGenerate');
	Route::match(['get', 'post'], 'getSessionwiseFeesDetails', 'fees\FeesController@getSessionwiseFeesDetails');
	Route::match(['get', 'post'], 'sendReceiptOnWhatsapp', 'fees\FeesController@sendReceiptOnWhatsapp');

//fees Detail
	
	Route::match(['get', 'post'], 'collect_fees_delete', 'fees\FeesController@collectFeesDelete');


//Fees Ledger
	

	Route::match(['get', 'post'], 'ledger_save', 'fees\FeesController@ledgerSave');
	
	


	

	


//no idea
	Route::match(['get', 'post'], 'fees/getFeesDetail', 'fees\FeesController@getFeesDetail');
	Route::match(['get', 'post'], 'fees_add_click', 'fees\FeesController@feesAddClick');
	Route::match(['get', 'post'], 'fees_masterData', 'fees\FeesController@feesMasterData');
//







 // Store Management
    
    
    
    Route::match(['get', 'post'], 'deleteStoreItem', 'StoreController@deleteStoreItem');
    
    
    Route::match(['get', 'post'], 'storeReceipt/{receipt_no}', 'StoreController@storeReceipt');
    Route::match(['get', 'post'], 'editInvoiceInventory/{receipt_no}', 'StoreController@editInvoiceInventory');
    Route::match(['get', 'post'], 'deleteInvoiceInventory', 'StoreController@deleteInvoiceInventory');
    Route::match(['get', 'post'], 'deleteReceiptInventory', 'StoreController@deleteReceiptInventory');
    














//Generate Slary Slip...//

Route::match(['get', 'post'], 'staff_salary_view', 'SalaryController@staff_salary_view');

Route::match(['get', 'post'], 'assignSalaryDetail', 'SalaryController@assignSalaryDetail');

Route::match(['get', 'post'], 'find/staff', 'SalaryController@findStaff');
Route::match(['get', 'post'], 'salary_print/{id}/{month_id}', 'SalaryController@salaryPrint');
Route::match(['get', 'post'], 'download/salary/slip/{id}/{month_id}', 'SalaryController@downloadSalarySlip');
Route::match(['get', 'post'], 'generate/salary', 'SalaryController@generateSalary');

Route::match(['get', 'post'], 'updateSingleField', 'HomeController@updateSingleField');
Route::match(['get', 'post'], 'countryData/{id}', 'HomeController@countryData');

Route::match(['get', 'post'], 'studentData/{id}', 'HomeController@studentData');
Route::match(['get', 'post'], 'streamData/{id}/{classType}', 'HomeController@streamData');

Route::match(['get', 'post'], 'calendarElement', 'HomeController@calendarElement');












	//dashboard
Route::match(['get'], '/', 'DashboardController@dashboard');
Route::match(['get'], 'dashboard', 'DashboardController@dashboard');
Route::get('attendance/today-absent-not-marked-pdf', 'DashboardController@todayAttendanceAbsentNotMarkedPdf')
    ->name('attendance.today.absent-not-marked.pdf');
// Route::get('attendance/today-absent-class-wise-pdf', 'DashboardController@todayAttendanceAbsentClassWisePdf')
//     ->name('attendance.today.absent-class-wise.pdf');
Route::get('attendance/today-absent-class-wise', 'DashboardController@todayAttendanceAbsentClassWise')
    ->name('attendance.today.absent-class-wise');

// PDF Generation Route
Route::get('attendance/today-absent-class-wise-pdf', 'DashboardController@todayAttendanceAbsentClassWisePdf')
    ->name('attendance.today.absent-class-wise.pdf');

Route::match(['get', 'post'], 'minidashboard', 'DashboardController@minidashboard');
    Route::match(['get'], 'test', 'DashboardController@test');

	Route::match(['get', 'post'], 'profile', 'ProfileController@profile');
	Route::match(['get', 'post'], 'profile/edit/{id}', 'ProfileController@profileEdit');
	Route::match(['get', 'post'], 'all_students_search', 'DashboardController@allStudentsSearch');
	Route::match(['get', 'post'], 'get_modules', 'DashboardController@getModules');
	Route::match(['get', 'post'], 'stu_status', 'DashboardController@stuStatus');
    Route::match(['get', 'post'], 'document_upload/{id}', 'ProfileController@document_upload');


	
	Route::match(['get', 'post'], 'add/task', 'ToDoListController@addTask');
	Route::match(['get', 'post'], 'taskStatusChange', 'ToDoListController@taskStatusChange');
	Route::match(['get', 'post'], 'to_do_assign', 'ToDoListController@toDoAssign');
	Route::match(['get', 'post'], 'to_do_assign_view', 'ToDoListController@toDoAssignView');
	Route::match(['get', 'post'], 'to_do_assign_edit/{id}', 'ToDoListController@toDoAssignEdit');
	Route::match(['get', 'post'], 'to_do_assign_delete', 'ToDoListController@toDoAssignDelete');
	Route::match(['get', 'post'], 'status/task', 'ToDoListController@statusTask');
	Route::match(['get', 'post'], 'delete/task', 'ToDoListController@deleteTask');
	Route::match(['get', 'post'], 'task_list', 'ToDoListController@taskList');

	//user
	Route::get('create-user', 'UserController@createuser');


	//students Id
	Route::match(['get', 'post'], 'students_id_data', 'StudentsIdController@studentsIdData');
	

	//account_dashboard 
	Route::match(['get', 'post'], 'account_dashboard', 'AccountController@account_dashboard');
	
	Route::match(['get', 'post'], 'bank/account/edit/{id}', 'AccountController@editBank');
	Route::match(['get', 'post'], 'account_delete', 'AccountController@delete');

	//staff
		Route::match(['get', 'post'], 'checkClassTeacher', 'StaffController@checkClassTeacher');
		Route::match(['get', 'post'], 'removeClassTeacher', 'StaffController@removeClassTeacher');
	

	Route::get('drop_teacher_letter/{id}', 'StaffController@dropTeacherPrint');

	Route::get('attendance_panel', 'StaffController@attendancePanel');
	Route::get('fill_attendance', 'StaffController@fillAttendance');
	

	
	
	
	

	Route::get('salary_generate', 'SalaryController@salaryGenerate');

	
	Route::match(['get', 'post'], 'SearchValueStaffAtten', 'StaffAttendanceController@searchValueStaffAtten');
	Route::match(['get', 'post'], 'staff_attendance_save', 'StaffAttendanceController@staff_attendance_save');

	Route::match(['get', 'post'], 'autoStaffAttendance', 'StaffAttendanceController@autoStaffAttendance');
	
	Route::post('Staff_categiry_data/{id}', 'StaffAttendanceController@StaffCategiryData');


	//Settings
	
	Route::match(['get', 'post'], 'discountdata/{id}', 'DashboardController@discountdata');

	// Download Center
	
	Route::match(['get'], 'download/{id}', 'DownloadController@download');


	
	Route::match(['get', 'post'], 'studentAdmitCard', 'DownloadController@studentAdmitCard');


	// SMS Service
	
	
	Route::match(['get', 'post'], 'sms_search_data', 'Sms_ServiceController@smsSearchData');
	Route::match(['get', 'post'], 'group_view', 'Sms_ServiceController@groupView');
	Route::match(['get', 'post'], 'group_add', 'Sms_ServiceController@groupAdd');
	Route::match(['get', 'post'], 'whatsapp_group_status', 'Sms_ServiceController@groupStatus');
	Route::match(['get', 'post'], 'group_delete', 'Sms_ServiceController@groupDelete');
	Route::match(['get', 'post'], 'group_edit/{id}', 'Sms_ServiceController@groupEdit');
	Route::match(['get', 'post'], 'group_messages_send', 'Sms_ServiceController@groupMessagesSend');
	Route::match(['get', 'post'], 'getGroupData/{id}', 'Sms_ServiceController@getGroupData');
	Route::match(['get', 'post'], 'failed_messages_delete', 'Sms_ServiceController@failedMessagesDelete');

	//student dashboard
	
    Route::match(['get', 'post'], 'homework', 'student_login\DashboardController@homeworkView');
	Route::match(['get', 'post'], 'downloadHomework/{id}', 'master\HomeworkController@downloadHomework');
	Route::match(['get', 'post'], 'student_download_center', 'student_login\DownloadCenterController@studentDownloadCenter');
	Route::match(['get', 'post'], 'studentAssignments', 'student_login\DownloadCenterController@studentAssignments');
	Route::match(['get', 'post'], 'student_study_material', 'student_login\DownloadCenterController@studentStudyMaterials');
	Route::match(['get', 'post'], 'student_syllabus', 'student_login\DownloadCenterController@studentSyllabus');
	Route::match(['get', 'post'], 'student_other_downloads', 'student_login\DownloadCenterController@studentOtherDownloads');



	Route::match(['get', 'post'], 'dataCabin/{id}', 'library\LibraryClickDataController@dataCabin');
	Route::match(['get', 'post'], 'libraryData/{id}', 'HomeController@libraryData');

	

	Route::match(['get', 'post'], 'school_calender_add', 'SchoolCalenderController@schoolCalenderAdd');
	Route::match(['get', 'post'], 'calender_view', 'SchoolCalenderController@calendarView');
	Route::match(['get', 'post'], 'getEvents', 'SchoolCalenderController@getEvents');
	//examination panel start//
	
	Route::match(['get', 'post'], 'view/question', 'offline_exam\QuestionController@viewQuestion');	
	Route::match(['get', 'post'], 'add/question', 'offline_exam\QuestionController@addQuestion');
	Route::match(['get', 'post'], 'edit/question/{id}', 'offline_exam\QuestionController@editQuestion');
	Route::match(['get', 'post'], 'delete/question', 'offline_exam\QuestionController@deleteQuestion');

	
	
	Route::match(['get', 'post'], 'edit/exam/{id}', 'offline_exam\ExamController@editExam');
	Route::match(['get', 'post'], 'assign/exam/{id}', 'offline_exam\ExamController@assignExam');
	Route::match(['get', 'post'], 'assign/delete/exam', 'offline_exam\ExamController@deleteAssignExam');
	Route::match(['get', 'post'], 'delete/exam', 'offline_exam\ExamController@deleteExam');


    
    Route::match(['get', 'post'], 'add/exam_term', 'offline_exam\ExamController@addExamTerm');
    Route::match(['get', 'post'], 'edit/exam_term/{id}', 'offline_exam\ExamController@editExamTerm');
    Route::match(['get', 'post'], 'delete/exam_term', 'offline_exam\ExamController@deleteExamTerm');

	
    Route::match(['get', 'post'], 'examData/{class_type_id}', 'HomeController@examData');
	Route::match(['get', 'post'], 'SubmitExaminationSchedule', 'offline_exam\ExamScheduleController@SubmitSchedule');


	Route::match(['get', 'post'], 'my_exams', 'offline_exam\FillMarkController@myExams');
	
    Route::match(['get', 'post'], 'fill_marks_submit', 'offline_exam\FillMarkController@fillMarksSubmit');
	Route::match(['get', 'post'], 'fill_marks_by_excel', 'offline_exam\FillMarkController@fillMarksByExcel');
	Route::match(['get', 'post'], 'print_report_card', 'offline_exam\FillMarkController@printReportCard');
	
    Route::match(['get', 'post'], 'bulk_marksheet_generate', 'offline_exam\FillMarkController@bulk_marksheet_generate');
    
    Route::match(['get', 'post'], 'performance_marks_submit', 'offline_exam\FillMarkController@performanceMarksSubmit');
    
	
    Route::match(['get', 'post'], 'exam_admit_card/{exam_id}/{class_type_id}/{admission_id}', 'offline_exam\AdmitCardController@downloadAdmitCard');
     Route::match(['get', 'post'], 'without_subject_admit_card/{exam_id}/{class_type_id}/{admission_id}', 'offline_exam\AdmitCardController@without_subject_admit_card');
   
	Route::match(['get', 'post'], 'studentParticularPerformance/{id}', 'StudentPerformanceController@studentParticularPerformance');
	
	Route::match(['get', 'post'], 'message_queue', 'MessageQueueController@message_queue');
	
	Route::match(['get', 'post'], 'changeLang', 'HomeController@change');
    Route::match(['get', 'post'], 'sectionDataId', 'HomeController@sectionDataId');


});


Route::match(['get', 'post'], 'newRegistration', 'Auth\AuthController@newRegistration');
Route::match(['get', 'post'], 'redirection', 'Auth\AuthController@redirection');
Route::match(['get', 'post'], 'clearTable', 'Auth\AuthController@clearTable');
Route::match(['get', 'post'], 'setSidebar', 'Auth\AuthController@setSidebar');
Route::match(['get', 'post'], 'allowSidebar', 'Auth\AuthController@allowSidebar');
Route::match(['get', 'post'], 'validateOtp', 'Auth\AuthController@validateOtp');
