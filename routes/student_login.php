<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'islogin'], function () {

    Route::match(['get', 'post'], 'AttendanceView_student', 'student_login\AttendanceController@view');
    Route::match(['get', 'post'], 'getAttendanceDatesStudent', 'student_login\AttendanceController@getAttendanceDates');
    Route::match(['get', 'post'], 'fees_history', 'student_login\FeesController@feesHistory');
    Route::get('fees_receipt/{invoice}/view', 'student_login\FeesController@viewReceipt')
        ->where('invoice', '[0-9]+')
        ->name('student.fees.receipt.view');
    Route::get('fees_receipt/{invoice}/download', 'student_login\FeesController@downloadReceipt')
        ->where('invoice', '[0-9]+')
        ->name('student.fees.receipt.download');
    Route::match(['get','post'],'student_homework','student_login\HomeworkController@view'); 
    Route::match(['get','post'],'homework/details_student/{id}','student_login\HomeworkController@homeworkDetails');
    Route::match(['get','post'],'particular/hw/details_student','student_login\HomeworkController@particularHomeworkDetails');
    Route::match(['get', 'post'], 'school_desk_view_student', 'student_login\HomeController@schoolDeskView');
    Route::match(['get', 'post'], 'my_teachers', 'student_login\HomeController@myteachers');
    Route::match(['get', 'post'], 'timetable', 'student_login\HomeController@timetableView');
    Route::match(['get','post'],'gallery_view_student', 'student_login\HomeController@galleryView');
    Route::match(['get', 'post'], 'prayer_student', 'student_login\HomeController@prayerView');
    Route::match(['get','post'],'student_subject_view', 'student_login\HomeController@subjectView');
    Route::match(['get', 'post'], 'rule_view', 'student_login\HomeController@ruleView');
    Route::match(['get', 'post'], 'student_gate_pass_view', 'student_login\HomeController@gatePassView');
    Route::match(['get', 'post'], 'student_uniform_view', 'student_login\HomeController@uniformView');
    Route::match(['get', 'post'], 'books_uniform_view_student', 'student_login\HomeController@booksView');
    //Download Center
    Route::match(['get', 'post'], 'download_center_student', 'student_login\DownloadCenterController@studentDownloadCenter');
    Route::match(['get', 'post'], 'studentAssignments', 'student_login\DownloadCenterController@assignments');
    Route::match(['get', 'post'], 'student_syllabus', 'student_login\DownloadCenterController@syllabus');
    Route::match(['get', 'post'], 'student_study_material', 'student_login\DownloadCenterController@studyMaterials');
    Route::match(['get', 'post'], 'student_other_downloads', 'student_login\DownloadCenterController@otherDownloads');
    Route::match(['get', 'post'], 'student_download_center', 'student_login\DownloadCenterController@student_download_center');
    Route::match(['get','post'],'notice_board_student/{id}', 'student_login\NoticeBoardController@view');
    Route::get('student-notices', 'student_login\StudentNoticeController@index')
        ->name('student.notices.index');
    
	Route::match(['get', 'post'], 'complaintEditStudent/{id}', 'student_login\ComplaintController@edit');
	Route::match(['get', 'post'], 'complaintAddStudent', 'student_login\ComplaintController@add');
	Route::match(['get', 'post'], 'complaintDeleteStudent', 'student_login\ComplaintController@delete');
    Route::get('student-complaints', 'student_login\SupportComplaintController@index');
    Route::get('student-complaints/create', 'student_login\SupportComplaintController@create');
    Route::post('student-complaints', 'student_login\SupportComplaintController@store');
    Route::get('student-complaints/{id}', 'student_login\SupportComplaintController@show')->where('id', '[0-9]+');
    Route::post('student-complaints/{id}/reply', 'student_login\SupportComplaintController@reply')->where('id', '[0-9]+');
       
    Route::match(['get','post'],'applyLeaveStudent', 'student_login\StuLeaveController@leaveAdd');
    Route::match(['get','post'],'deleteLeaveStudent', 'student_login\StuLeaveController@leaveDelete');
    Route::match(['get','post'],'updateLeaveStudent/{id}', 'student_login\StuLeaveController@leaveEdit');
      
      //Teachers Controller 
    Route::match(['get', 'post'], 'teachers_student/index', 'student_login\TeacherController@index');
    Route::match(['get', 'post'], '/student/result-card', 'student_login\ExamsController@resultCard');
    Route::match(['get','post'],'my_id_card', 'student_login\HomeController@my_id_card');
    
    Route::match(['get', 'post'], 'profileStudent', 'student_login\ProfileController@profileEdit');
    Route::post('profileStudent/change-password', 'student_login\ProfileController@changePassword')
        ->name('student.profile.change-password');
    
    Route::match(['get', 'post'], 'notificationFatchStudent', 'student_login\NotificationController@notificationFatch');
    Route::post('notificationFatchStudent/{id}/mark-read', 'student_login\NotificationController@markAttendanceRead')
        ->where('id', '[0-9]+')
        ->name('student.attendance-notifications.mark-read');
    Route::post('notificationFatchStudent/mark-all-read', 'student_login\NotificationController@markAllAttendanceRead')
        ->name('student.attendance-notifications.mark-all-read');
    Route::delete('notificationFatchStudent/clear-all', 'student_login\NotificationController@clearAllAttendanceNotifications')
        ->name('student.attendance-notifications.clear-all');
    Route::match(['get', 'post'], 'notification_detail_stu/{id}', 'student_login\NotificationController@notificationDetailStu');
    
    
      Route::get('/hard-refresh', function () {
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('optimize:clear');
    
        return response()->json(['status' => 'success']);
    });


}); 
