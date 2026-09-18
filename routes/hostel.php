<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'islogin'], function () {

//HostelController
	Route::match(['get', 'post'], 'hostel_dashboard', 'hostel\HostelController@dashboard');
	Route::match(['get', 'post'], 'hostel_add', 'hostel\HostelController@hostelAdd');
	Route::match(['get', 'post'], 'hostel_edit/{id}', 'hostel\HostelController@hostelEdit');
	Route::match(['get', 'post'], 'hostel_delete', 'hostel\HostelController@hostelDelete');
	Route::match(['get', 'post'], 'building_add', 'hostel\HostelController@buildingAdd');
	Route::match(['get', 'post'], 'building_edit/{id}', 'hostel\HostelController@buildingEdit');
	Route::match(['get', 'post'], 'building_delete', 'hostel\HostelController@buildingDelete');
	Route::match(['get', 'post'], 'floor_add', 'hostel\HostelController@floorAdd');
	Route::match(['get', 'post'], 'floor_edit/{id}', 'hostel\HostelController@floorEdit');
	Route::match(['get', 'post'], 'floor_delete', 'hostel\HostelController@floorDelete');
	Route::match(['get', 'post'], 'room_add', 'hostel\HostelController@roomAdd');
	Route::match(['get', 'post'], 'room_view', 'hostel\HostelController@roomView');
	Route::match(['get', 'post'], 'room_edit/{id}', 'hostel\HostelController@roomEdit');
	Route::match(['get', 'post'], 'room_delete', 'hostel\HostelController@roomDelete');
	Route::match(['get', 'post'], 'bed_add', 'hostel\HostelController@bedAdd');
	Route::match(['get', 'post'], 'bed_view', 'hostel\HostelController@bedView');
	Route::match(['get', 'post'], 'bed_edit/{id}', 'hostel\HostelController@bedEdit');
	Route::match(['get', 'post'], 'bed_delete', 'hostel\HostelController@bedDelete');
	Route::match(['get', 'post'], 'floor_search_data', 'hostel\HostelController@floorSearchData');
	Route::match(['get', 'post'], 'room_search_data', 'hostel\HostelController@roomSearchData');
	Route::match(['get', 'post'], 'bed_search_data', 'hostel\HostelController@bedSearchData');
	Route::match(['get', 'post'], 'hostel_unassign', 'hostel\HostelController@hostelUnassign');
	Route::match(['get', 'post'], 'change_assign_status', 'hostel\HostelController@changeAssignStatus');
	Route::match(['get', 'post'], 'stu_bed_detail', 'hostel\HostelController@stuBedDetail');
	Route::match(['get', 'post'], 'school_student_search', 'hostel\HostelController@schoolStudentSearch');
	Route::match(['get', 'post'], 'meter_unit_update', 'hostel\HostelController@meter_unit_update');
	Route::match(['get', 'post'], 'meter_unit_update_room', 'hostel\HostelController@meter_unit_update_room');
	Route::match(['get', 'post'], 'hostel_monthily_amount', 'hostel\HostelController@hostelMonthilyAmount');
	Route::match(['get', 'post'], 'hostel_student_print/{id}', 'hostel\HostelController@hostelStudentPrint');
	
//HostelAssignController
	Route::match(['get', 'post'], 'hostel_assign', 'hostel\HostelAssignController@hostelAssign');
	Route::match(['get', 'post'], 'assign_student_view', 'hostel\HostelAssignController@assignStudentView');
	Route::match(['get', 'post'], 'hostel_student_edit/{id}', 'hostel\HostelAssignController@hostelStudentEdit');
	Route::match(['get', 'post'], 'hostel_student_delete', 'hostel\HostelAssignController@hostelStudentDelete');
	Route::match(['get', 'post'], 'hostel_student_search', 'hostel\HostelAssignController@hostelStudentSearch');
	
	Route::match(['get', 'post'], 'hostel_informaiton', 'hostel\HostelAssignController@hostelInformation');
    
    Route::match(['get', 'post'], 'hostel_assign_dashboard', 'hostel\HostelAssignController@hostelAssignDashboard');
    Route::match(['get', 'post'], 'getHostelAssignDashboard/{hostel_id}', 'hostel\HostelAssignController@getHostelAssignDashboard');
//HeadController
	Route::match(['get', 'post'], 'hostelExpensesHeadeAdd', 'hostel\HeadController@hostelExpensesHeadeAdd');
	Route::match(['get', 'post'], 'hostelExpensesHeadeEdit/{id}', 'hostel\HeadController@hostelExpensesHeadeEdit');
	Route::match(['get', 'post'], 'hostelExpensesHeadeDelete', 'hostel\HeadController@hostelExpensesHeadeDelete');
//hostelExpensesAdd	
	Route::match(['get', 'post'], 'hostelExpensesAdd', 'hostel\HeadController@hostelExpensesAdd');
	Route::match(['get', 'post'], 'hostelExpensesView', 'hostel\HeadController@hostelExpensesView');
	Route::match(['get', 'post'], 'hostelExpensesEdit/{id}', 'hostel\HeadController@hostelExpensesEdit');
	Route::match(['get', 'post'], 'hostelExpensesDelete', 'hostel\HeadController@hostelExpensesDelete');
	Route::match(['get', 'post'], 'hostelExpensesPrint/{id}', 'hostel\HeadController@hostelExpensesPrint');

//HostelMessExpanceController
	Route::match(['get', 'post'], 'messExpanceAdd', 'hostel\HostelMessExpanceController@messExpanceAdd');
	Route::match(['get', 'post'], 'messExpanceEdit/{id}', 'hostel\HostelMessExpanceController@messExpanceEdit');
	Route::match(['get', 'post'], 'messExpanceDelete', 'hostel\HostelMessExpanceController@messExpanceDelete');

//HostelMessFoodCategoryController
	Route::match(['get', 'post'], 'messFoodCategoryAdd', 'hostel\HostelMessFoodCategoryController@messFoodCategoryAdd');
	Route::match(['get', 'post'], 'messFoodCategoryEdit/{id}', 'hostel\HostelMessFoodCategoryController@messFoodCategoryEdit');
	Route::match(['get', 'post'], 'messFoodCategoryDelete', 'hostel\HostelMessFoodCategoryController@messFoodCategoryDelete');

//HostelMessFoodRoutineController//
	Route::match(['get', 'post'], 'messFoodRoutineAdd', 'hostel\HostelMessFoodRoutineController@messFoodRoutineAdd');
	Route::match(['get', 'post'], 'messFoodRoutineEdit/{id}', 'hostel\HostelMessFoodRoutineController@messFoodRoutineEdit');
	Route::match(['get', 'post'], 'messFoodRoutineDelete', 'hostel\HostelMessFoodRoutineController@messFoodRoutineDelete');

//HostelMessFoodMenuController//
	Route::match(['get', 'post'], 'messFoodMenuAdd', 'hostel\HostelMessFoodMenuController@messFoodMenuAdd');
	Route::match(['get', 'post'], 'messFoodMenuEdit/{id}', 'hostel\HostelMessFoodMenuController@messFoodMenuEdit');
	Route::match(['get', 'post'], 'messFoodMenuDelete', 'hostel\HostelMessFoodMenuController@messFoodMenuDelete');

//HostelMessFeesStrucherController//
	Route::match(['get', 'post'], 'messFeesStrucher', 'hostel\HostelMessFeesStrucherController@messFeesStrucher');

//HostelFeesController
	Route::match(['get', 'post'], 'hostel/collect/fees', 'hostel\HostelFeesController@collectFees');
	Route::match(['get', 'post'], 'hostel/fees/view', 'hostel\HostelFeesController@viewFees');
	Route::match(['get', 'post'], 'hostel/fees/pay', 'hostel\HostelFeesController@hostelPaySubmit');
	Route::match(['get', 'post'], 'hostel_fees_onclick', 'hostel\HostelFeesController@hostelFeesOnclick');
	Route::match(['get', 'post'], 'hostel_fees_print/{id}', 'hostel\HostelFeesController@hostelFeesPrint');
	Route::match(['get', 'post'], 'hostel/fees/ledger/view', 'hostel\HostelFeesController@viewFeesLedger');
	Route::match(['get', 'post'], 'hostel/student/report', 'hostel\HostelFeesController@hostelStudentReport');
	Route::match(['get', 'post'], 'ledger_fees_print/{id}', 'hostel\HostelFeesController@ledgerFeesPrint');
	Route::match(['get', 'post'], 'hostel_ledger_print/{id}', 'hostel\HostelFeesController@hostelLedgerPrint');
	Route::match(['get', 'post'], 'assign_hostal_fees/{id}', 'hostel\HostelFeesController@assignHostalFees');
	Route::match(['get', 'post'], 'hostel_fees_electricity_view', 'hostel\HostelElectricityBillController@hostelElectricityPayView');
	Route::match(['get', 'post'], 'electricity_bill_payment_add', 'hostel\HostelElectricityBillController@electricityBillPayment');
	Route::match(['get', 'post'], 'hostel/fees/electricity/pay', 'hostel\HostelElectricityBillController@hostelElectricityPaySubmit');
	Route::match(['get', 'post'], 'unitData/{month_id}/{admission_id}', 'hostel\HostelElectricityBillController@unitData');
// 	Route::match(['get', 'post'], 'electricity_bill_payment_add', 'hostel\HostelFeesController@electricityBillPayment');
// 	Route::match(['get', 'post'], 'hostel/fees/electricity/pay', 'hostel\HostelFeesController@hostelElectricityPaySubmit');
// 	Route::match(['get', 'post'], 'hostel/fees/electricity/view/{id}', 'hostel\HostelFeesController@hostelElectricityPayView');
	/*Route::match(['get', 'post'], 'hostel/fees/security_deposite', 'hostel\HostelFeesController@viewSecurityDeposite');
    Route::match(['get', 'post'], 'hostel/fees/security_deposite_add', 'hostel\HostelFeesController@addSecurityDeposite');
    Route::match(['get', 'post'], 'hostel/fees/security_deposite_refund', 'hostel\HostelFeesController@refundSecurityDeposite'); */
    Route::match(['get', 'post'], 'hostel/fees/security_deposite', 'hostel\HostelFeesController@viewSecurityDeposite');
    Route::match(['get', 'post'], 'hostel/fees/security_deposite_add', 'hostel\HostelFeesController@addSecurityDeposite');
    Route::match(['get', 'post'], 'hostel/fees/security_deposite_delete', 'hostel\HostelFeesController@deleteSecurityDeposite');
    Route::match(['get', 'post'], 'hostel/fees/security_deposite_refund', 'hostel\HostelFeesController@refundSecurityDeposite');
    Route::match(['get', 'post'], 'securityDepositPrint/{id}', 'hostel\HostelFeesController@securityDepositPrint');
	Route::match(['get', 'post'], 'hostel/fees_reminder', 'hostel\HostelFeesController@hostelFeesReminder');
	Route::match(['get', 'post'], 'hostel_due_amount_reminder', 'hostel\HostelFeesController@hostel_due_amount_reminder');
	Route::match(['get', 'post'], 'hostel_due_amount_pay/{id}', 'hostel\HostelFeesController@hostel_due_amount_pay');
	Route::match(['get', 'post'], 'hostel_invoice/{invoice_no}/{admission_id}', 'hostel\HostelFeesController@hostel_invoice');

//HostelClickDataController
	Route::match(['get', 'post'], 'dataBuilding/{id}', 'hostel\HostelClickDataController@dataBuilding');
	Route::match(['get', 'post'], 'datafloor/{id}', 'hostel\HostelClickDataController@dataFloor');
	Route::match(['get', 'post'], 'dataroom/{id}', 'hostel\HostelClickDataController@dataRoom');
	Route::match(['get', 'post'], 'databed/{id}', 'hostel\HostelClickDataController@dataBed');
	Route::match(['get', 'post'], 'hostelData/{id}', 'hostel\HostelClickDataController@hostelData');
	Route::match(['get', 'post'], 'hostelDataSearch/{id}', 'hostel\HostelClickDataController@hostelDataSearch');
	Route::match(['get', 'post'], 'FloorData/{id}', 'hostel\HostelClickDataController@floorData');
	Route::match(['get', 'post'], 'BuildingData/{id}', 'hostel\HostelClickDataController@buildingData');
	Route::match(['get', 'post'], 'RoomData/{id}', 'hostel\HostelClickDataController@roomData');
	Route::match(['get', 'post'], 'StudentsData/{id}', 'hostel\HostelClickDataController@studentsData');
	
	//7 sept 24
	Route::match(['get', 'post'], 'getBuilding/{id}', 'hostel\HostelClickDataController@getBuilding');
	Route::match(['get', 'post'], 'getFloor/{id}', 'hostel\HostelClickDataController@getFloor');
    Route::match(['get', 'post'], 'getbed/{id}', 'hostel\HostelClickDataController@getbed');

//StudentExpenseController
	Route::match(['get', 'post'], 'student_expenses', 'hostel\StudentExpenseController@view');
	Route::match(['get', 'post'], 'student_expenses_add', 'hostel\StudentExpenseController@add');
	Route::match(['get', 'post'], 'student_expenses_edit/{id}', 'hostel\StudentExpenseController@edit');
	Route::match(['get', 'post'], 'expenses_all_pay', 'hostel\StudentExpenseController@expenses_all_pay');
	Route::match(['get', 'post'], 'change_expense_status', 'hostel\StudentExpenseController@change_expense_status');
	Route::match(['get', 'post'], 'delete_expence', 'hostel\StudentExpenseController@delete_expence');
	
	Route::match(['get', 'post'], 'hostel_due_amount', 'hostel\HostelFeesController@hostel_due_amount');
	Route::match(['get', 'post'], 'due_amount_reminder', 'hostel\HostelFeesController@due_amount_reminder');
	Route::match(['get', 'post'], 'invoice/{receipt_no}/{admission_id}', 'hostel\HostelFeesController@invoice');
	Route::match(['get', 'post'], 'library_due_amount_pay/{fees_detail_id}', 'hostel\HostelFeesController@library_due_amount_pay');
	Route::match(['get', 'post'], 'library/fees/view', 'hostel\HostelFeesController@viewFees');
	Route::match(['get', 'post'], 'library/student/report', 'hostel\HostelFeesController@feesStudentReport');
});