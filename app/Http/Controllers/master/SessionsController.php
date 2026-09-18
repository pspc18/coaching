<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admin;
use App\Models\Account;
use App\Models\Admission;
use App\Models\Admit;
use App\Models\AdmitCardNote;
use App\Models\exam\AssignExam;
use App\Models\exam\Exam;
use App\Models\exam\AssignQuestion;
use App\Models\exam\ExamResult;
use App\Models\exam\ExamResultDetail;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\Question;
use App\Models\examoffline\AssignOfflineExam;
use App\Models\examoffline\ExamOffline;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesCounter;
use App\Models\fees\FeesAssignDetail;
use App\Models\hostel\ElectricityBillPayment;
use App\Models\hostel\FoodMenuList;
use App\Models\hostel\Head;
use App\Models\hostel\Hostel;
use App\Models\hostel\HostelAssign;
use App\Models\hostel\HostelBed;
use App\Models\hostel\HostelBuilding;
use App\Models\hostel\HostelExpences;
use App\Models\hostel\HostelFloor;
use App\Models\hostel\HostelMeterUnit;
use App\Models\hostel\HostelRoom;
use App\Models\hostel\MessFeesStrucher;
use App\Models\hostel\MessFoodCategory;
use App\Models\hostel\MessFoodRoutine;
use App\Models\hostel\SecurityDeposit;
use App\Models\hostel\StudentExpense;
use App\Models\hostel\StudentExpenseDetail;
use App\Models\library\AssignBook;
use App\Models\library\BookInvoice;
use App\Models\library\Library;
use App\Models\library\LibraryAssign;
use App\Models\library\LibraryBook;
use App\Models\library\LibraryCabin;
use App\Models\library\LibraryCategory;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryPlan;
use App\Models\library\LibraryTimeSlot;
use App\Models\library\RetrunBook;
use App\Models\Setting;
use App\Models\Master\Branch;
use App\Models\Master\Complaint;
use App\Models\Master\BooksUniformShop;
use App\Models\Master\Bus;
use App\Models\Master\BusAssign;
use App\Models\Master\BusRoute;
use App\Models\Master\BusRouteAssign;
use App\Models\Master\EmailRecords;
use App\Models\Master\EmailTamplate;
use App\Models\Master\EnquiryStatus;
use App\Models\Master\EventManagement;
use App\Models\Master\Gallery;
use App\Models\Master\GatePass;
use App\Models\Master\Holidays;
use App\Models\Master\Homework;
use App\Models\Master\HomeworkDocuments;
use App\Models\Master\HomeworkReview;
use App\Models\Master\HourlyHomework;
use App\Models\Master\InvantoryBooking;
use App\Models\Master\InvantoryDresh;
use App\Models\Master\LeaveManagement;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\NoticeBoard;
use App\Models\Master\Penalty;
use App\Models\Master\Prayer;
use App\Models\Master\RecycleBin;
use App\Models\Master\RegistrationTerms;
use App\Models\Master\Sport;
use App\Models\Master\Sports;
use App\Models\Master\Stork;
use App\Models\Master\TeacherSubject;
use App\Models\Master\Time_Table;
use App\Models\Master\TimePeriods;
use App\Models\Master\Uniform;
use App\Models\Master\UploadHomework;
use App\Models\Master\Sessions;
use App\Models\BillCounter;
use App\Models\PermissionManagement;
use App\Models\ClassType;
use App\Models\DownloadCenter;
use App\Models\EventeCertificate;
use App\Models\ExaminationAdmitCard;
use App\Models\ExaminationSchedule;
use App\Models\ExaminationScheduleDetail;
use App\Models\Invantory;
use App\Models\InvantoryItem;
use App\Models\getExamType;
use App\Models\FeesCollect;
use App\Models\FeesDetail;
use App\Models\SchoolCalender;
use App\Models\SportCertificate;
use App\Models\StudentMarksDetails;
use App\Models\StudentsMarks;
use App\Models\StaffSalary;
use App\Models\StaffSalaryDetail;
use App\Models\StudentAttendance;
use App\Models\StudentAction;
use App\Models\TcCertificate;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\TeacherDocuments;
use App\Models\TeachersAccounts;
use App\Models\ToDoList;
use App\Models\TotalDays;
use App\Models\Subject;
use App\Models\Remark;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Enquiry;
use App\Models\CcForm;
use App\Models\Chat;
use App\Models\City;

use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'from_year' => 'required',
                'to_year'   => 'required',
            ]);

            $class = new Sessions;
            $class->user_id   = Session::get('id');
            $class->branch_id = Session::get('branch_id');
            $class->from_year = trim($request->from_year);
            $class->to_year   = trim($request->to_year);
            $class->save();

            $sessions_id = $class->id;
            $data = BillCounter::where('branch_id', Session::get('branch_id'))->get();
            foreach ($data as $val) {
                $add = Branch::all();
                foreach ($add as $val1) {
                    $bill = new BillCounter;
                    $bill->user_id   = Session::get('id');
                    $bill->branch_id = $val1->id;
                    $bill->session_id = $sessions_id;
                    $bill->type      = $val->type;
                    $bill->counter   = 0;
                    $bill->save();
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Academic Session added successfully.',
                ]);
            }
            return redirect('session_add')->with('message', 'Sessions add Successfully.');
        }

        $allSessions = Sessions::orderBy('id', 'DESC')->get();

        $search     = trim((string) ($request->search ?? ''));
        $searchId   = trim((string) ($request->search_id ?? ''));
        $searchFrom = trim((string) ($request->search_from ?? ''));
        $searchTo   = trim((string) ($request->search_to ?? ''));
        $page       = max(1, (int) ($request->page ?? 1));
        $perPage    = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allSessions->filter(function ($item) use ($search, $searchId, $searchFrom, $searchTo) {
            if ($searchId !== '' && stripos((string)$item->id, $searchId) === false) {
                return false;
            }
            if ($searchFrom !== '' && stripos((string)$item->from_year, $searchFrom) === false) {
                return false;
            }
            if ($searchTo !== '' && stripos((string)$item->to_year, $searchTo) === false) {
                return false;
            }
            if ($search !== '') {
                $idMatch    = stripos((string)$item->id, $search) !== false;
                $fromMatch  = stripos((string)$item->from_year, $search) !== false;
                $toMatch    = stripos((string)$item->to_year, $search) !== false;
                $comboMatch = stripos($item->from_year . '-' . $item->to_year, $search) !== false;
                if (!$idMatch && !$fromMatch && !$toMatch && !$comboMatch) {
                    return false;
                }
            }
            return true;
        })->values();

        $totalFiltered = $filteredRows->count();
        $totalPages    = ($perPage === -1 || $totalFiltered === 0) ? 1 : (int) ceil($totalFiltered / $perPage);
        $page          = min($page, max(1, $totalPages));
        $pageRows      = ($perPage === -1) ? $filteredRows : $filteredRows->slice(($page - 1) * $perPage, $perPage)->values();
        $fromRecord    = $totalFiltered > 0 ? (($page - 1) * ($perPage === -1 ? $totalFiltered : $perPage) + 1) : 0;
        $toRecord      = $totalFiltered > 0 ? min($page * ($perPage === -1 ? $totalFiltered : $perPage), $totalFiltered) : 0;

        $pagination = [
            'current_page'  => $page,
            'per_page'      => $perPage,
            'total_records' => $totalFiltered,
            'total_pages'   => $totalPages,
            'from'          => $fromRecord,
            'to'            => $toRecord,
        ];

        if ($request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'html'       => view('master.Sessions.session_rows', ['rows' => $pageRows])->render(),
                'pagination' => $pagination,
            ]);
        }

        return view('master.Sessions.add', [
            'rows'       => $pageRows,
            'data'       => $allSessions,
            'sessions'   => $allSessions,
            'pagination' => $pagination,
            'search'     => $search,
            'searchId'   => $searchId,
            'searchFrom' => $searchFrom,
            'searchTo'   => $searchTo,
            'perPage'    => $perPage,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $data = Sessions::find($id);
        if (!$data) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Session not found.'], 404);
            }
            return redirect('session_add')->with('error', 'Session not found.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'from_year' => 'required',
                'to_year'   => 'required',
            ]);

            $data->user_id   = Session::get('id');
            $data->branch_id = Session::get('branch_id');
            $data->from_year = trim($request->from_year);
            $data->to_year   = trim($request->to_year);
            $data->save();

            if ($request->ajax()) {
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Academic Session updated successfully.',
                    'redirect' => url('session_add')
                ]);
            }
            return redirect('session_add')->with('message', 'Sessions Updated Successfully.');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'        => $data->id,
                    'from_year' => $data->from_year,
                    'to_year'   => $data->to_year,
                ]
            ]);
        }

        $dataview = Sessions::all();
        return view('master.Sessions.edit', ['data' => $data, 'dataview' => $dataview]);
    }

    public function delete(Request $request)
    {
        $deleteId = $request->delete_id;
        if (!empty($deleteId)) {
            AssignBook::where('session_id', $deleteId)->delete();
            Account::where('session_id', $deleteId)->delete();
            Admission::where('session_id', $deleteId)->delete();
            Admit::where('session_id', $deleteId)->delete();
            AssignExam::where('session_id', $deleteId)->delete();
            BillCounter::where('session_id', $deleteId)->delete();
            Setting::where('session_id', $deleteId)->delete();
            Complaint::where('session_id', $deleteId)->delete();
            CcForm::where('session_id', $deleteId)->delete();
            Chat::where('session_id', $deleteId)->delete();
            DownloadCenter::where('session_id', $deleteId)->delete();
            Enquiry::where('session_id', $deleteId)->delete();
            EventeCertificate::where('session_id', $deleteId)->delete();
            Expense::where('session_id', $deleteId)->delete();
            FeesCollect::where('session_id', $deleteId)->delete();
            FeesDetail::where('session_id', $deleteId)->delete();
            Invantory::where('session_id', $deleteId)->delete();
            InvantoryItem::where('session_id', $deleteId)->delete();
            Invoice::where('session_id', $deleteId)->delete();
            Remark::where('session_id', $deleteId)->delete();
            SchoolCalender::where('session_id', $deleteId)->delete();
            SportCertificate::where('session_id', $deleteId)->delete();
            StaffSalary::where('session_id', $deleteId)->delete();
            StaffSalaryDetail::where('session_id', $deleteId)->delete();
            StudentAction::where('session_id', $deleteId)->delete();
            StudentAttendance::where('session_id', $deleteId)->delete();
            StudentsMarks::where('session_id', $deleteId)->delete();
            Subject::where('session_id', $deleteId)->delete();
            TcCertificate::where('session_id', $deleteId)->delete();
            Teacher::where('session_id', $deleteId)->delete();
            TeacherAttendance::where('session_id', $deleteId)->delete();
            TeacherDocuments::where('session_id', $deleteId)->delete();
            TeachersAccounts::where('session_id', $deleteId)->delete();
            ToDoList::where('session_id', $deleteId)->delete();
            AssignQuestion::where('session_id', $deleteId)->delete();
            Exam::where('session_id', $deleteId)->delete();
            ExamResult::where('session_id', $deleteId)->delete();
            ExamResultDetail::where('session_id', $deleteId)->delete();
            FillMarks::where('session_id', $deleteId)->delete();
            FillMinMaxMarks::where('session_id', $deleteId)->delete();
            Question::where('session_id', $deleteId)->delete();
            AssignOfflineExam::where('session_id', $deleteId)->delete();
            ExamOffline::where('session_id', $deleteId)->delete();
            FeesAssign::where('session_id', $deleteId)->delete();
            FeesAssignDetail::where('session_id', $deleteId)->delete();
            ElectricityBillPayment::where('session_id', $deleteId)->delete();
            FoodMenuList::where('session_id', $deleteId)->delete();
            Head::where('session_id', $deleteId)->delete();
            Hostel::where('session_id', $deleteId)->delete();
            HostelAssign::where('session_id', $deleteId)->delete();
            HostelBed::where('session_id', $deleteId)->delete();
            HostelBuilding::where('session_id', $deleteId)->delete();
            HostelExpences::where('session_id', $deleteId)->delete();
            HostelFloor::where('session_id', $deleteId)->delete();
            HostelMeterUnit::where('session_id', $deleteId)->delete();
            HostelRoom::where('session_id', $deleteId)->delete();
            MessFeesStrucher::where('session_id', $deleteId)->delete();
            MessFoodCategory::where('session_id', $deleteId)->delete();
            MessFoodRoutine::where('session_id', $deleteId)->delete();
            SecurityDeposit::where('session_id', $deleteId)->delete();
            StudentExpense::where('session_id', $deleteId)->delete();
            StudentExpenseDetail::where('session_id', $deleteId)->delete();
            BookInvoice::where('session_id', $deleteId)->delete();
            Library::where('session_id', $deleteId)->delete();
            LibraryAssign::where('session_id', $deleteId)->delete();
            LibraryBook::where('session_id', $deleteId)->delete();
            LibraryCabin::where('session_id', $deleteId)->delete();
            LibraryCategory::where('session_id', $deleteId)->delete();
            LibraryLocker::where('session_id', $deleteId)->delete();
            LibraryPlan::where('session_id', $deleteId)->delete();
            LibraryTimeSlot::where('session_id', $deleteId)->delete();
            BooksUniformShop::where('session_id', $deleteId)->delete();
            Bus::where('session_id', $deleteId)->delete();
            BusAssign::where('session_id', $deleteId)->delete();
            BusRoute::where('session_id', $deleteId)->delete();
            EventManagement::where('session_id', $deleteId)->delete();
            Gallery::where('session_id', $deleteId)->delete();
            GatePass::where('session_id', $deleteId)->delete();
            Holidays::where('session_id', $deleteId)->delete();
            Homework::where('session_id', $deleteId)->delete();
            HomeworkDocuments::where('session_id', $deleteId)->delete();
            HomeworkReview::where('session_id', $deleteId)->delete();
            HourlyHomework::where('session_id', $deleteId)->delete();
            LeaveManagement::where('session_id', $deleteId)->delete();
            NoticeBoard::where('session_id', $deleteId)->delete();
            Penalty::where('session_id', $deleteId)->delete();
            Prayer::where('session_id', $deleteId)->delete();
            RecycleBin::where('session_id', $deleteId)->delete();
            RegistrationTerms::where('session_id', $deleteId)->delete();
            Sport::where('session_id', $deleteId)->delete();
            Sports::where('session_id', $deleteId)->delete();
            Stork::where('session_id', $deleteId)->delete();
            TeacherSubject::where('session_id', $deleteId)->delete();
            Time_Table::where('session_id', $deleteId)->delete();
            TimePeriods::where('session_id', $deleteId)->delete();
            Uniform::where('session_id', $deleteId)->delete();
            UploadHomework::where('session_id', $deleteId)->delete();
            Sessions::find($deleteId)?->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Academic Session deleted successfully.'
            ]);
        }

        return redirect('session_add')->with('message', 'Sessions Deleted Successfully.');
    }

    public function session_all(Request $request)
    {
        $Counter[] = 'Teacher';
        $Counter[] = 'StudentRegistration';
        $Counter[] = 'StudentAdmission';
        $Counter[] = 'FeesSlip';
        $Counter[] = 'LibraryId';
        $Counter[] = 'HostelFees';
        $Counter[] = 'Hostel';

        $add = Branch::all();
        foreach ($add as $val1) {
            $data = Sessions::all();
            foreach ($data as $val) {
                foreach ($Counter as $Counter1) {
                    $bill = new BillCounter;
                    $bill->user_id   = Session::get('id');
                    $bill->branch_id = $val1->id;
                    $bill->session_id = $val->id;
                    $bill->type      = $Counter1;
                    $bill->counter   = 0;
                    $bill->save();
                }
            }
        }

        return redirect('session_add');
    }
}