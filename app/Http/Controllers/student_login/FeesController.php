<?php

namespace App\Http\Controllers\student_login;
use App\Models\User;
use App\Models\State;
use App\Models\fees\FeesAssign;
use App\Models\FeesCollect;
use App\Models\Admission;
use App\Models\Master\Branch;
use App\Models\Master\Homework;
use Illuminate\Validation\Validator; 
use App\Models\Master\PaymentMode;
use App\Models\FeesDetail;
use App\Models\FeesSetting;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\fees\FeesAssignDetail;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Detection\MobileDetect;
use PDF;
use Helper;


class FeesController extends Controller
{
 
  
    public function feesHistory(Request $request)
    {
        $currentAdmission = Admission::find(Session::get('id'));

        if (!$currentAdmission) {
            return back()->withErrors(['error' => 'Admission record not found.']);
        }

        $activeSessionId = $request->session_id ?? Session::get('session_id');

        $feeData = Helper::getStudentFeeSummary(
            $currentAdmission->id,
            $activeSessionId,
            $currentAdmission->branch_id
        );

        // Retrieve all sessions this student has been associated with
        $sessionIds = collect([$currentAdmission->session_id]);
        if (!empty($currentAdmission->unique_system_id)) {
            $otherSessionIds = Admission::where('unique_system_id', $currentAdmission->unique_system_id)
                ->where('branch_id', $currentAdmission->branch_id)
                ->pluck('session_id');
            $sessionIds = $sessionIds->merge($otherSessionIds)->unique();
        }
        $availableSessions = \DB::table('sessions')
            ->whereIn('id', $sessionIds)
            ->orderByDesc('from_year')
            ->get(['id', 'from_year', 'to_year']);

        return view('student_login.fees_history', [
            'getFees' => $feeData['getFees'],
            'feeHeadLedger' => $feeData['feeHeadLedger'],
            'feeReceipts' => $feeData['feeReceipts'],
            'summary' => $feeData,
            'activeSessionId' => $activeSessionId,
            'availableSessions' => $availableSessions,
            'student' => $currentAdmission,
        ]);
    }

    public function getReceiptData($invoiceId): array
    {
        $roleId = (int) Session::get('role_id');
        $sessionBranchId = Session::get('branch_id');

        $query = FeesDetailsInvoices::select(
                'fees_details_invoices.*',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.mobile',
                'admissions.image as student_img',
                'admissions.category',
                'class_types.name as class_name',
                'class_types.id as class_type_id',
                'gender.name as gender_name',
                'admissions.father_name',
                'admissions.admissionNo',
                'payment_modes.name as payment_mode',
                'payment_modes.id as payment_mode_id',
                'sessions.from_year',
                'sessions.to_year'
            )
            ->leftJoin('admissions', 'admissions.id', '=', 'fees_details_invoices.admission_id')
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->leftJoin('gender', 'gender.id', '=', 'admissions.gender_id')
            ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
            ->leftJoin('sessions', 'sessions.id', '=', 'fees_details_invoices.session_id')
            ->where(function ($q) use ($invoiceId) {
                $q->where('fees_details_invoices.id', $invoiceId)
                  ->orWhere('fees_details_invoices.invoice_no', (string) $invoiceId)
                  ->orWhere('fees_details_invoices.offline_receipt_no', (string) $invoiceId);
                if (is_numeric($invoiceId)) {
                    $unpadded = (string) (int) $invoiceId;
                    $q->orWhere('fees_details_invoices.invoice_no', $unpadded)
                      ->orWhere('fees_details_invoices.offline_receipt_no', $unpadded);
                }
            })
            ->whereIn('fees_details_invoices.status', [0, 1]);

        // Student Role (role_id == 3): Strictly scope to logged-in student & siblings
        if ($roleId === 3) {
            $currentAdmission = Admission::find(Session::get('id'));
            abort_unless($currentAdmission, 404);

            $admissionIds = collect([$currentAdmission->id]);
            if (!empty($currentAdmission->unique_system_id)) {
                $admissionIds = Admission::where('unique_system_id', $currentAdmission->unique_system_id)
                    ->where('branch_id', $currentAdmission->branch_id)
                    ->pluck('id');
            }

            $query->where('fees_details_invoices.branch_id', $currentAdmission->branch_id)
                  ->whereIn('fees_details_invoices.admission_id', $admissionIds);
        } else {
            // Admin or Staff Role: Scope by branch if staff and not superadmin
            if (!empty($sessionBranchId) && $roleId > 1) {
                $query->where('fees_details_invoices.branch_id', $sessionBranchId);
            }
        }

        $invoice = $query->first();
        abort_unless($invoice, 404);
        $invoice->date = $invoice->payment_date;

        $detailIds = collect(explode(',', (string) $invoice->fees_details_id))
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            });
        $details = FeesDetail::select(
                'fees_detail.*',
                'payment_modes.name as payment_mode',
                'fees_group.name as fees_group_name'
            )
            ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_detail.payment_mode_id')
            ->leftJoin('fees_group', 'fees_group.id', '=', 'fees_detail.fees_group_id')
            ->where('fees_detail.admission_id', $invoice->admission_id)
            ->whereIn('fees_detail.id', $detailIds)
            ->get();

        return [$invoice, $details];
    }

    private function getStudentReceiptData($invoiceId): array
    {
        return $this->getReceiptData($invoiceId);
    }

    public function viewReceipt($invoice)
    {
        [$invoiceData, $details] = $this->getReceiptData($invoice);
        $printPreview = Helper::printPreview('Fees Collect');
        $fileName = 'fees-receipt-'.($invoiceData->invoice_no ?: $invoiceData->id).'.pdf';
        $feesSetting = FeesSetting::getSetting(
            $invoiceData->branch_id ?? Session::get('branch_id'),
            $invoiceData->session_id ?? Session::get('session_id')
        );

        return PDF::loadView($printPreview, [
            'data' => $details,
            'invoice_data' => $invoiceData,
            'feesSetting' => $feesSetting,
        ])->setPaper('A4', 'portrait')->stream($fileName);
    }

    public function downloadReceipt($invoice)
    {
        [$invoiceData, $details] = $this->getReceiptData($invoice);
        $printPreview = Helper::printPreview('Fees Collect');
        $fileName = 'fees-receipt-'.($invoiceData->invoice_no ?: $invoiceData->id).'.pdf';
        $feesSetting = FeesSetting::getSetting(
            $invoiceData->branch_id ?? Session::get('branch_id'),
            $invoiceData->session_id ?? Session::get('session_id')
        );

        return PDF::loadView($printPreview, [
            'data' => $details,
            'invoice_data' => $invoiceData,
            'feesSetting' => $feesSetting,
        ])->setPaper('A4', 'portrait')->download($fileName);
    }



        
                 
                 


    
}
