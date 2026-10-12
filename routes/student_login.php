<?php
use Illuminate\Support\Facades\Route;

// Proactively load PaymentController, PhonePeService, and OnlinePayment for guaranteed cross-platform resolution
if (!class_exists(\App\Http\Controllers\student_login\PaymentController::class, false)) {
    $payCtrlFile = app_path('Http/Controllers/student_login/PaymentController.php');
    if (file_exists($payCtrlFile)) {
        require_once $payCtrlFile;
    }
}
if (!class_exists(\App\Services\PhonePeService::class, false)) {
    $serviceFile = app_path('Services/PhonePeService.php');
    if (file_exists($serviceFile)) {
        require_once $serviceFile;
    }
}
if (!class_exists(\App\Models\OnlinePayment::class, false)) {
    $modelFile = app_path('Models/OnlinePayment.php');
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
}

// Container aliases for both casing conventions
if (class_exists(\App\Http\Controllers\student_login\PaymentController::class, false)) {
    if (!class_exists('app\Http\Controllers\student_login\PaymentController', false)) {
        class_alias(\App\Http\Controllers\student_login\PaymentController::class, 'app\Http\Controllers\student_login\PaymentController');
    }
    app()->bind('app\Http\Controllers\student_login\PaymentController', function ($app) {
        return $app->make(\App\Http\Controllers\student_login\PaymentController::class);
    });
}

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
    Route::match(['get','post'],'upload_homework_student','student_login\HomeworkController@uploadHomework')->name('student.homework.upload');
    Route::match(['get','post'],'homework/details_student/{id}','student_login\HomeworkController@homeworkDetails');
    Route::match(['get','post'],'particular/hw/details_student','student_login\HomeworkController@particularHomeworkDetails');
    //Download Center
    Route::match(['get', 'post'], 'download_center_student', 'student_login\DownloadCenterController@studentDownloadCenter');
    Route::match(['get', 'post'], 'studentAssignments', 'student_login\DownloadCenterController@assignments');
    Route::match(['get', 'post'], 'student_syllabus', 'student_login\DownloadCenterController@syllabus');
    Route::match(['get', 'post'], 'student_study_material', 'student_login\DownloadCenterController@studyMaterials');
    Route::match(['get', 'post'], 'student_other_downloads', 'student_login\DownloadCenterController@otherDownloads');
    Route::match(['get', 'post'], 'student_download_center', 'student_login\DownloadCenterController@student_download_center');
    Route::get('student-notices', 'student_login\StudentNoticeController@index')
        ->name('student.notices.index');
    Route::get('student-complaints', 'student_login\SupportComplaintController@index');
    Route::get('student-complaints/create', 'student_login\SupportComplaintController@create');
    Route::post('student-complaints', 'student_login\SupportComplaintController@store');
    Route::get('student-complaints/{id}', 'student_login\SupportComplaintController@show')->where('id', '[0-9]+');
    Route::post('student-complaints/{id}/reply', 'student_login\SupportComplaintController@reply')->where('id', '[0-9]+');
      
      //Teachers Controller 

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
    
    
      Route::get('/hard-refresh', function () {
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('optimize:clear');
    
        return response()->json(['status' => 'success']);
    });

    // PhonePe Payment Gateway Routes (Initiate & Simulator inside student auth)
    Route::post('student/payment/initiate', 'student_login\PaymentController@initiate')->name('student.payment.initiate');
    Route::get('student/payment/phonepe-simulator', 'student_login\PaymentController@simulator')->name('student.payment.phonepe.simulator');

}); 

// PhonePe Callback & Webhook (Publicly accessible by PhonePe redirect / server webhook, validated via Order ID & PhonePe verification API)
Route::match(['get', 'post'], 'student/payment/phonepe-callback', 'student_login\PaymentController@callback')->name('student.payment.phonepe.callback');
Route::post('student/payment/phonepe-webhook', 'student_login\PaymentController@webhook')->name('student.payment.phonepe.webhook'); 
