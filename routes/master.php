<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'islogin'], function () {
    
    
    
    
    
    
    


//check Route
Route::match(['get'],'master_dashboard', 'master\DashboardController@dashboard');



//Subject
 Route::match(['get','post'],'create_subject', 'master\SubjectController@createSubjects');
 Route::match(['get','post'],'add_subject', 'master\SubjectController@add');
 Route::match(['get','post'],'multi_edit_subject', 'master\SubjectController@multiEditSubjects');

//Book uniform controller
Route::match(['get','post'],'books_uniform_view', 'master\BooksUniformController@view');
 Route::match(['get','post'],'books_uniform_edit/{id}', 'master\BooksUniformController@edit');
  Route::match(['get','post'],'books_uniform_delete', 'master\BooksUniformController@delete');
  Route::match(['get','post'],'books_uniform_add', 'master\BooksUniformController@add');

//Branch
Route::match(['get','post'],'viewBranch', 'master\BranchController@viewBranch');
 Route::match(['get','post'],'editBranch/{id}', 'master\BranchController@editBranch');
 Route::match(['get','post'],'addBranch', 'master\BranchController@addBranch');
 
 //Class
 Route::match(['get','post'],'add_class', 'master\ClassController@add');
Route::match(['get','post'],'edit_class/{id}', 'master\ClassController@edit'); 
Route::match(['get','post'],'class_delete', 'master\ClassController@delete');
Route::match(['get','post'],'save-selected-classes', 'master\ClassController@saveSelectedClasses');

//TeacherSubjectController
//TeacherSubjectController
Route::match(['get','post'],'teacher_subject_add', 'master\TeacherSubjectController@teacherSubjectAdd');
Route::match(['get','post'],'printTimeTable', 'master\TeacherSubjectController@printTimeTable');

//ComplaintController
 Route::match(['get', 'post'], 'complaint_view', 'master\ComplaintsController@view');
  Route::match(['get', 'post'], 'sendConversation', 'master\ComplaintsController@sendConversation');
	Route::match(['get', 'post'], 'complaint_edit/{id}', 'master\ComplaintsController@edit');
	Route::match(['get', 'post'], 'complaint_add', 'master\ComplaintsController@add');
	Route::match(['get', 'post'], 'delete_complaint', 'master\ComplaintsController@delete');
Route::match(['get', 'post'], 'complaint_action', 'master\ComplaintsController@complaintAction');
Route::get('complaints-management', 'master\SupportComplaintController@index');
Route::get('complaints-management/{id}', 'master\SupportComplaintController@show')->where('id', '[0-9]+');
Route::post('complaints-management/{id}/reply', 'master\SupportComplaintController@reply')->where('id', '[0-9]+');
Route::post('complaints-management/{id}/status', 'master\SupportComplaintController@status')->where('id', '[0-9]+');
Route::get('support-complaint-attachment/{replyId}', 'master\SupportComplaintController@attachment')->where('replyId', '[0-9]+');
//Enquiry Status Reference + Other Types Start
Route::match(['get','post'],'enquiry_status_add', 'master\EnquiryStatusController@add');
Route::match(['get','post'],'enquiry_status_edit/{id}', 'master\EnquiryStatusController@edit');
Route::match(['get','post'],'enquiry_status_delete', 'master\EnquiryStatusController@delete');

Route::match(['get','post'],'response_add', 'master\EnquiryStatusController@add');
Route::match(['get','post'],'response_edit/{id}', 'master\EnquiryStatusController@edit');
Route::match(['get','post'],'response_delete', 'master\EnquiryStatusController@delete');

Route::match(['get','post'],'calling_purpose_add', 'master\EnquiryStatusController@add');
Route::match(['get','post'],'calling_purpose_edit/{id}', 'master\EnquiryStatusController@edit');
Route::match(['get','post'],'calling_purpose_delete', 'master\EnquiryStatusController@delete');

Route::match(['get','post'],'visiting_purpose_add', 'master\EnquiryStatusController@add');
Route::match(['get','post'],'visiting_purpose_edit/{id}', 'master\EnquiryStatusController@edit');
Route::match(['get','post'],'visiting_purpose_delete', 'master\EnquiryStatusController@delete');

Route::match(['get','post'],'complaint_type_add', 'master\EnquiryStatusController@add');
Route::match(['get','post'],'complaint_type_edit/{id}', 'master\EnquiryStatusController@edit');
Route::match(['get','post'],'complaint_type_delete', 'master\EnquiryStatusController@delete');
//Enquiry Status Reference + Other Types End

//Gallery
Route::match(['get','post'],'gallery_view', 'master\GalleryController@view');
Route::match(['get','post'],'gallery_add', 'master\GalleryController@add');

//GatePassController
Route::match(['get','post'],'gate_pass_view', 'master\GatePassController@view');
Route::match(['get','post'],'gate_pass_add', 'master\GatePassController@add');
Route::match(['get','post'],'gate_pass_delete', 'master\GatePassController@delete');
Route::match(['get','post'],'gate_pass_edit/{id}', 'master\GatePassController@edit');

//leave
Route::match(['get','post'],'leave', 'master\LeaveController@add');
 
//MessageTemplate
Route::match(['get','post'],'messageDashboard', 'master\message\MessageTemplateController@messageDashboard');
Route::match(['get','post'],'messageType', 'master\message\MessageTemplateController@messageTypeAdd');
Route::match(['get','post'],'messageTypeStatus', 'master\message\MessageTemplateController@messageTypeStatus');
Route::match(['get','post'],'messageTypeEdit/{id}', 'master\message\MessageTemplateController@messageTypeEdit');
Route::match(['get','post'],'messageTypeDelete', 'master\message\MessageTemplateController@messageTypeDelete');
Route::match(['get','post'],'messageTemplate', 'master\message\MessageTemplateController@messageTemplateAdd');
Route::match(['get','post'],'messageTemplateEdit/{id}', 'master\message\MessageTemplateController@messageTemplateEdit');
Route::match(['get','post'],'messageTemplateDelete', 'master\message\MessageTemplateController@messageTemplateDelete');


//NoticeBoardController
 Route::match(['get','post'],'notice_board/view', 'master\NoticeBoardController@view');
 Route::match(['get','post'],'notice_board/viewid/{id?}', 'master\NoticeBoardController@viewid');
 Route::match(['get','post'],'notice_board/add', 'master\NoticeBoardController@add');
 Route::match(['get','post'],'notice_board/delete', 'master\NoticeBoardController@delete');
 Route::match(['get','post'],'notice_board/edit/{id}', 'master\NoticeBoardController@edit');

// Notice Management (recipient targeting + admin approval)
 Route::get('notice-management', 'master\NoticeManagementController@index');
 Route::get('notice-management/create', 'master\NoticeManagementController@create');
 Route::post('notice-management', 'master\NoticeManagementController@store');
 Route::post('notice-management/{id}/review', 'master\NoticeManagementController@review');
 Route::get('notice-management/{id}/attachment', 'master\NoticeManagementController@attachment');
 Route::match(['get', 'post', 'delete'], 'notice-management/{id}/delete', 'master\NoticeManagementController@destroy');
 Route::delete('notice-management/{id}', 'master\NoticeManagementController@destroy');


//PrayerController
Route::match(['get','post'],'prayer_view', 'master\PrayerController@view');
Route::match(['get','post'],'prayer_edit/{id}', 'master\PrayerController@edit');
Route::match(['get','post'],'prayer_delete', 'master\PrayerController@delete');
Route::match(['get','post'],'prayer_add', 'master\PrayerController@add');


//Role
Route::match(['get','post'],'role_add', 'master\RoleController@add');
Route::match(['get','post'],'role_Edit/{id}', 'master\RoleController@edit');
Route::match(['get','post'],'role_delete', 'master\RoleController@delete');
Route::match(['get','post'],'role/permission/{role_id}', 'master\RoleController@role_permission');
Route::match(['get','post'],'role_permission/{role_id}', 'master\RoleController@role_permission');
Route::match(['get','post'],'role/user_permission/{user_id}', 'master\RoleController@user_permission_data');
Route::match(['get','post'],'role/user_permission_save/{user_id}', 'master\RoleController@user_permission_save');
Route::match(['get','post'],'user/permissions/{id}', 'UserController@user_permission');
Route::match(['get','post'],'user/module_status', 'UserController@module_status');

//Rule Controller
 Route::match(['get','post'],'rules_add', 'master\RuleController@add');
 Route::match(['get','post'],'rules_edit/{id}', 'master\RuleController@edit');
Route::match(['get','post'],'rules_delete', 'master\RuleController@delete');
Route::match(['get','post'],'school_desk', 'master\RuleController@schoolDeskEdit');


//Session Start
Route::match(['get','post'],'session_add', 'master\SessionsController@add');
 Route::match(['get','post'],'sessions_edit/{id}', 'master\SessionsController@edit');
Route::match(['get','post'],'sessions_delete', 'master\SessionsController@delete');


//Subject Controller
Route::match(['get','post'],'time_periods', 'master\SubjectController@timePeriods');
Route::match(['get','post'],'edit_periods/{id}', 'master\SubjectController@editTimePeriods');
    Route::match(['get','post'],'delete_periods', 'master\SubjectController@deletePeriods');

//UniformController
Route::match(['get','post'],'uniform_add', 'master\UniformController@add');
Route::match(['get','post'],'uniform_edit/{id}', 'master\UniformController@edit');
Route::match(['get','post'],'uniform_delete', 'master\UniformController@delete');


//WhatsappController
Route::match(['get','post'],'whatsapp_setting', 'WhatsappController@whatsappSetting');


//Homework
Route::match(['get','post'],'homework/index','master\HomeworkController@index');
Route::match(['get','post'],'homework/add','master\HomeworkController@add');
Route::match(['get','post'],'homework/edit/{id}','master\HomeworkController@edit');
Route::match(['get','post'],'homework/details/{id}','master\HomeworkController@homeworkDetails');
Route::match(['get','post'],'homework/delete', 'master\HomeworkController@delete');

//Bus
Route::match(['get','post'],'busDashboard', 'master\BusController@busDashboard');
Route::match(['get','post'],'busRouteAdd', 'master\BusController@busRouteAdd');
 Route::match(['get','post'],'busRouteEdit/{id}', 'master\BusController@busRouteEdit');
Route::match(['get','post'],'busRouteDelete', 'master\BusController@busRouteDelete'); 
Route::match(['get','post'],'busAdd', 'master\BusController@busAdd');
 Route::match(['get','post'],'busView', 'master\BusController@busView');
Route::match(['get','post'],'busEdit/{id}', 'master\BusController@busEdit');
Route::match(['get','post'],'busDelete', 'master\BusController@busDelete');
Route::match(['get','post'],'assignBusRoute', 'master\BusController@assignBusRoute'); 
Route::match(['get','post'],'assignBusRouteEdit/{id}', 'master\BusController@assignBusRouteEdit');   
Route::match(['get','post'],'assignBusRouteDelete', 'master\BusController@assignBusRouteDelete');
Route::match(['get','post'],'assignBus/{id}', 'master\BusController@assignBus');
Route::match(['get','post'],'studentBusView', 'master\BusController@studentBusView');
Route::match(['get','post'],'busAssignEdit/{id}', 'master\BusController@busAssignEdit');
    Route::match(['get','post'],'busLateMessage', 'master\BusController@busLateMessage');





































































//Branch
    
    
   
    
   
    Route::match(['get','post'],'deleteBranch', 'master\BranchController@deleteBranch');
    Route::match(['get','post'],'changeBranch', 'master\BranchController@changeBranch');
//Branch end..

//Bus
       
     
    Route::match(['get','post'],'student_bus_assign_view', 'master\BusController@studentBusAssignView');
    
    
    
   
    
    
      
    
     
      
    
    
//Branch end..

//Message Template
    
    
    
    
    
    
   


//PrayerController
    
    
    
   

//UniformController
    
    
    
    
//BooksUniformController    
    
    
   
   

//RuleController
   
   
    
    
    Route::match(['get', 'post'], 'school_desk_view', 'master\RuleController@schoolDeskView');
    

//GatePassController
    
    
    
    Route::match(['get','post'],'search_gate_pass', 'master\GatePassController@searchGetpassStudent');
    Route::match(['get','post'],'getpass_add_click', 'master\GatePassController@getpass_add_click');
    Route::match(['get','post'],'gate_pass_print/{id}', 'master\GatePassController@gatePassPrint');
    Route::match(['get','post'],'gate_pass_otp', 'master\GatePassController@gate_pass_otp');

//SubjectController
    
    

    
//Sessions Start
   // Route::match(['get','post'],'session_all', 'master\SessionsController@session_all');


//Weekend Calendar 
    Route::match(['get','post'],'print_weekend/{id}', 'master\WeekendcalendarController@weekendPrint');
    Route::match(['get','post'],'academic_calendar', 'master\WeekendcalendarController@academicCalendar');
    Route::match(['get','post'],'weekend_delete', 'master\WeekendcalendarController@delete');
    // Keep the old multi-row form available without overriding the newer
    // Academic Calendar route registered at /add_weekend in routes/web.php.
    Route::match(['get','post'],'legacy/add_weekend', 'master\WeekendcalendarController@add');
    Route::redirect('view_weekend', 'add_weekend');

//subject
    
    Route::match(['get','post'],'select_class', 'master\SubjectController@selectClass');
   
    Route::match(['get','post'],'edit_subject/{id}', 'master\SubjectController@edit');
    Route::match(['get','post'],'delete_subject', 'master\SubjectController@delete');
    Route::match(['get','post'],'delete_create_subject', 'master\SubjectController@deleteCreateSubject');
    
    Route::match(['get','post'],'subjectOrderBy', 'master\SubjectController@subjectOrderBy');

//enquirystatus
    
    
    

    
//role
    Route::match(['get','post'],'role_add', 'master\RoleController@add');
    
    


    
    
    Route::match(['get','post'],'gallery_edit/{id}', 'master\GalleryController@edit');
    Route::match(['get','post'],'gallery_delete', 'master\GalleryController@delete');

//Leave
    
    Route::match(['get','post'],'leaveStatus', 'master\LeaveController@leaveStatus');

//NoticeBoardController
  
  
   

//Recycle Bin
   Route::match(['get','post'],'recycle_bin/add', 'master\RecycleBinController@add');
   Route::match(['get','post'],'recycle_bin/view', 'master\RecycleBinController@view');
   Route::match(['get','post'],'recycle_bin/delete', 'master\RecycleBinController@delete');
   Route::match(['get','post'],'recycle_bin/edit/{id}', 'master\RecycleBinController@editRecycleBin');

//Student Leave 
    Route::match(['get','post'],'leave_management', 'student_login\StuLeaveController@leaveManagement');
    Route::match(['get','post'],'leaveAdd', 'student_login\StuLeaveController@leaveAdd');
    Route::match(['get','post'],'leave_delete', 'student_login\StuLeaveController@leaveDelete');
    Route::match(['get','post'],'leaveUpdate/{id}', 'student_login\StuLeaveController@leaveEdit');
 
// //Fees Controller
// Route::match(['get', 'post'], 'fees_history', 'student_login\FeesController@feesHistory');
// Route::match(['get', 'post'], 'fees_check', 'student_login\FeesController@fees_check');





























Route::match(['get','post'],'homework/detail/{id}','master\HomeworkController@hwDetailWithoutLogin');
Route::match(['get','post'],'exam_result_email', 'ExaminationController@viewExamResultEmail');

//Penalty
Route::match(['get','post'],'penalty/add ', 'master\PenaltyController@add');
Route::match(['get','post'],'penalty/index ', 'master\PenaltyController@index');
Route::match(['get','post'],'penalty/edit/{id} ', 'master\PenaltyController@edit');
Route::match(['get','post'],'penalty/delete', 'master\PenaltyController@delete');


//Home_work 
Route::match(['get','post'],'home_work  ', 'master\Home_WorkController@add');


//Stork
Route::match(['get','post'],'stork', 'master\StorkController@add');
Route::match(['get','post'],'stork_view', 'master\StorkController@view');
Route::match(['get','post'],'stork_edit/{id}', 'master\StorkController@edit');
Route::match(['get','post'],'stork_delete', 'master\StorkController@delete');

//Utilities  
Route::match(['get','post'],'utilities', 'master\UtilitiesController@add');



//Holiday 
  Route::match(['get','post'],'holiday/add', 'master\HolidayController@add');
  Route::match(['get','post'],'holiday/edit', 'master\HolidayController@edit');
  Route::match(['get','post'],'holiday/view', 'master\HolidayController@view');
//Paper_Setter 
  Route::match(['get','post'],'paper_setter', 'master\PaperSetterController@add');
//Sports 
  Route::match(['get','post'],'sports', 'master\SportsController@add');
  Route::match(['get','post'],'sports_view', 'master\SportsController@view');
  Route::match(['get','post'],'sports_edit/{id}', 'master\SportsController@edit');

//Personal Details 
   Route::match(['get','post'],'personal_details', 'master\PersonalDetailsController@add');
//School Calendar 
  Route::match(['get','post'],'calendar', 'master\CalendarController@calendar');

//Bus

Route::match(['get','post'],'assign_bus_search_data', 'master\BusController@assignBusSearchData'); 
Route::match(['get','post'],'bus_assign_delete', 'master\BusController@assignBusDelete');
Route::match(['get','post'],'busData/{id}','HomeController@busData');


//Homework
Route::match(['get','post'],'homework/dashboard','master\HomeworkController@dashboard'); 
 
 


Route::match(['get','post'],'upload/homework','master\HomeworkController@uploadHomework'); 
Route::match(['get','post'],'evaluate/homework','master\HomeworkController@evaluateHomework'); 
Route::match(['get','post'],'download_homework/{id}','master\HomeworkController@downloadHomework'); 
Route::match(['get','post'],'download_assignment/{id}','master\HomeworkController@downloadAssignment'); 

Route::match(['get','post'],'particular/hw/details','master\HomeworkController@particularHomeworkDetails');
Route::match(['get','post'],'upload/homework/resend','master\HomeworkController@resendUploadHomework');
// Route::match(['get','post'],'student_homework','master\HomeworkController@studentview'); 

//Hourly Homework
Route::match(['get','post'],'hourly/hw/add','master\HomeworkController@hwAdd');
Route::match(['get','post'],'hourly/hw/view','master\HomeworkController@hourlyHomeworkView');
Route::match(['get','post'],'find/student','master\HomeworkController@findStudent');
Route::match(['get','post'],'upload/hourly/homework','master\HomeworkController@uploadHourlyHomework'); 
Route::match(['get','post'],'download_hourly_homework/{id}','master\HomeworkController@downloadHourlyHomework'); 
Route::match(['get','post'],'hourly/homework/details/{id}','master\HomeworkController@hourlyHomeworkDetails');
Route::match(['get','post'],'particular/hourly/hw/details','master\HomeworkController@particularHourlyHomeworkDetails');
Route::match(['get','post'],'evaluate/hourly/homework','master\HomeworkController@evaluateHourlyHomework'); 


//Email-Tamplate Update
Route::match(['get','post' ],'EmailTamplate', 'master\EmailTamplateController@EmailTamplate');
Route::match(['get','post' ],'EmailTamplateEdit/{id}', 'master\EmailTamplateController@EmailTamplateEdit');


//Email Records
Route::match(['get','post'],'email_records/view/{id}', 'master\EmailRecordsController@view');


}); 
