<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'islogin'], function () {

//Library Dashboard
	Route::match(['get', 'post'], 'library_dashboard', 'library\LibraryController@dashboard');

// Time Slot
	Route::match(['get', 'post'], 'library/time_slot', 'library\TimeSlotController@time_slot');
	Route::match(['get', 'post'], 'not_assign_time_slots', 'library\TimeSlotController@notAssignTimeSlots');
	Route::match(['get', 'post'], 'allotSeatInSlot', 'library\LibraryClickDataController@allotSeatInSlot');
	Route::match(['get', 'post'], 'allotLocker', 'library\LibraryClickDataController@allotLocker');
	Route::match(['get', 'post'], 'get_unassigned_users', 'library\LibraryClickDataController@get_unassigned_users');

//library cerate
	Route::match(['get', 'post'], 'library_add', 'library\LibraryController@libraryAdd');
	Route::match(['get', 'post'], 'library_edit/{id}', 'library\LibraryController@libraryEdit');
	Route::match(['get', 'post'], 'library_delete', 'library\LibraryController@libraryDelete');
	
//library Locker
	Route::match(['get', 'post'], 'library/locker', 'library\LockerController@locker');
	Route::match(['get', 'post'], 'library/lockerEdit/{id}', 'library\LockerController@lockerEdit');
	Route::match(['get', 'post'], 'library/lockerDelete', 'library\LockerController@lockerDelete');
	Route::match(['get', 'post'], 'book_locker', 'library\LockerController@bookLocker');
	Route::match(['get', 'post'], 'locker_details', 'library\LockerController@lockerDetails');
	
//library Cabin
    Route::match(['get', 'post'], 'cabin_add', 'library\LibraryController@cabinAdd');
    Route::match(['get', 'post'], 'manage_seat_map', 'library\LibraryController@manageSeatMap');
	Route::match(['get', 'post'], 'cabin_edit/{id}', 'library\LibraryController@cabinEdit');
	Route::match(['get', 'post'], 'cabin_delete', 'library\LibraryController@cabinDelete');
	
//library Assign
	Route::match(['get', 'post'], 'library_assign', 'library\LibraryController@libraryAssign');
	Route::match(['get', 'post'], 'blank_seat_assign', 'library\LibraryController@blankSeatAssign');
	Route::match(['get', 'post'], 'seat_unassign', 'library\LibraryController@seat_unassign');
	Route::match(['get', 'post'], 'library_student_view', 'library\LibraryController@libraryStudentView');
	Route::match(['get', 'post'], 'library_student_renew', 'library\LibraryController@libraryStudentRenew');
	Route::match(['get', 'post'], 'get_last_plans', 'library\LibraryController@getLastPlans');
	Route::match(['get', 'post'], 'library_student_plans_manage/{id}', 'library\LibraryController@library_student_plans_manage');
	Route::match(['get', 'post'], 'student_plan_delete', 'library\LibraryController@student_plan_delete');
	Route::match(['get', 'post'], 'change_user_plan/{plan_id}/{admission_id}', 'library\LibraryController@change_user_plan');
	Route::match(['get', 'post'], 'get_locker_details', 'library\LibraryController@getLockerDetails');
	Route::match(['get', 'post'], 'library_student_detail/{id}', 'library\LibraryController@library_student_detail');
	
//library Fees Collect
	Route::match(['get', 'post'], 'library_due_amount', 'library\LibraryFeesController@library_due_amount');
	Route::match(['get', 'post'], 'due_amount_reminder', 'library\LibraryFeesController@due_amount_reminder');
	Route::match(['get', 'post'], 'invoice/{receipt_no}/{admission_id}', 'library\LibraryFeesController@invoice');
	Route::match(['get', 'post'], 'library_due_amount_pay/{fees_detail_id}', 'library\LibraryFeesController@library_due_amount_pay');
	Route::match(['get', 'post'], 'library/fees/view', 'library\LibraryFeesController@viewFees');
	Route::match(['get', 'post'], 'library/student/report', 'library\LibraryFeesController@feesStudentReport');




























	

	

	Route::match(['get', 'post'], 'library_student_edit/{id}', 'library\LibraryController@libraryStudentEdit');
	Route::match(['get', 'post'], 'library_student_delete', 'library\LibraryController@libraryStudentDelete');
	Route::match(['get', 'post'], 'search_school_student', 'library\LibraryController@searchSchoolStudent');
	Route::match(['get', 'post'], 'library_student_status', 'library\LibraryController@libraryStudentStatus');
    Route::match(['get', 'post'], 'stu_cabin_detail', 'library\LibraryController@stuCabinDetail');
//library Fees Collect
	Route::match(['get', 'post'], 'searchStudentlibrary', 'library\AssignBookController@searchStudentlibrary');
	Route::match(['get', 'post'], 'library/fees/pay', 'library\LibraryFeesController@libraryPaySubmit');
	Route::match(['get', 'post'], 'library/collect/fees', 'library\LibraryFeesController@collectFees');
	Route::match(['get', 'post'], 'library_fees_onclick', 'library\LibraryFeesController@libraryFeesOnclick');
	Route::match(['get', 'post'], 'library_fees_data', 'library\LibraryFeesController@libraryFeesData');
	Route::match(['get', 'post'], 'library_fees_print/{id}', 'library\LibraryFeesController@libraryFeesPrint');
	Route::match(['get', 'post'], 'library/ladger_print/{id}', 'library\LibraryFeesController@ladgerLadgerPrint');

//library ID card
	Route::match(['get', 'post'], 'library_id_card/{id}', 'library\LibraryController@libraryPrint');
	
//library book Category
	Route::match(['get', 'post'], 'book_category_add ', 'library\LibraryBookController@bookCategoryAdd');
	Route::match(['get', 'post'], 'book_category_view', 'library\LibraryBookController@bookCategoryView');
	Route::match(['get', 'post'], 'book_category_edit/{id}', 'library\LibraryBookController@bookCategoryEdit');
	Route::match(['get', 'post'], 'book_category_delete', 'library\LibraryBookController@bookCategoryDelete');

//library book
	Route::match(['get', 'post'], 'library_book_add', 'library\LibraryBookController@libraryBookAdd');
	Route::match(['get', 'post'], 'library_book_view', 'library\LibraryBookController@libraryBookView');
	Route::match(['get', 'post'], 'library_book_edit/{id}', 'library\LibraryBookController@bookLibraryEdit');
	Route::match(['get', 'post'], 'library_book_delete', 'library\LibraryBookController@libraryBookDelete');
	Route::match(['get', 'post'], 'book_add_excel', 'library\LibraryBookController@bookAddExcel');

	Route::match(['get', 'post'], 'return_invoice_book', 'library\AssignBookController@returnInvoiceBook');

//Assign Book
	Route::match(['get', 'post'], 'book_assign', 'library\AssignBookController@bookAssign');
	Route::match(['get', 'post'], 'book_assign_view', 'library\AssignBookController@bookAssignView');
	Route::match(['get', 'post'], 'search_student_assign_book', 'library\AssignBookController@searchStudentAssignBook');
	Route::match(['get', 'post'], 'assign_book', 'library\AssignBookController@assignBook');
	Route::match(['get', 'post'], 'retrun_book_invoice', 'library\AssignBookController@RetrunBookInvoice');



});