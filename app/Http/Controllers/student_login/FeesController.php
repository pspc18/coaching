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
        $filteredAdmission = null;
        if (!empty($currentAdmission->unique_system_id)) {
            $filteredAdmission = Admission::where('unique_system_id', $currentAdmission->unique_system_id)
                ->where('session_id', $activeSessionId)
                ->where('branch_id', $currentAdmission->branch_id)
                ->first(['id', 'session_id', 'branch_id']);
        } elseif ((int) $currentAdmission->session_id === (int) $activeSessionId) {
            $filteredAdmission = $currentAdmission;
        }

        $getFees = collect();
        $feeHeadLedger = collect();

        if ($filteredAdmission) {
            $getFees = FeesAssignDetail::select(
                    'fees_assign_details.*',
                    'fees_group.name as group_name'
                )
                ->join('fees_group', 'fees_group.id', '=', 'fees_assign_details.fees_group_id')
                ->where('fees_assign_details.admission_id', $filteredAdmission->id)
                ->where('fees_assign_details.session_id', $activeSessionId)
                ->where('fees_assign_details.branch_id', $currentAdmission->branch_id)
                ->get();

            $feePaymentDetails = FeesDetail::select(
                    'fees_detail.*',
                    'payment_modes.name as payment_mode',
                    'fees_group.name as fee_head_name'
                )
                ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_detail.payment_mode_id')
                ->leftJoin('fees_group', 'fees_group.id', '=', 'fees_detail.fees_group_id')
                ->where('fees_detail.admission_id', $filteredAdmission->id)
                ->where('fees_detail.session_id', $activeSessionId)
                ->where('fees_detail.branch_id', $currentAdmission->branch_id)
                ->where(function ($query) {
                    $query->where('fees_detail.fees_type', 0)
                        ->orWhereNull('fees_detail.fees_type');
                })
                ->whereIn('fees_detail.status', [0, 1])
                ->orderByDesc('fees_detail.date')
                ->orderByDesc('fees_detail.id')
                ->get();
            $paymentsByHead = $feePaymentDetails->groupBy('fees_group_id');

            $feeHeadLedger = $getFees
                ->groupBy('fees_group_id')
                ->map(function ($assignments, $feeGroupId) use ($paymentsByHead) {
                    $payments = $paymentsByHead->get($feeGroupId, collect())->values();
                    $receivedPayments = $payments->where('status', 0);
                    $assignedAmount = round((float) $assignments->sum('fees_group_amount'), 2);
                    $assignmentDiscount = round((float) $assignments->sum('discount'), 2);
                    $paidAmount = round((float) $receivedPayments->sum('paid_amount'), 2);
                    $paymentDiscount = round((float) $receivedPayments->sum('discount'), 2);
                    $fineAmount = round((float) $receivedPayments->sum('installment_fine'), 2);
                    $dueAmount = round(max(
                        0,
                        $assignedAmount - $assignmentDiscount - $paidAmount - $paymentDiscount
                    ), 2);

                    return (object) [
                        'fees_group_id' => $feeGroupId,
                        'name' => $assignments->first()->group_name ?: 'Fee Head',
                        'due_date' => $assignments->first()->installment_due_date,
                        'assigned_amount' => $assignedAmount,
                        'discount' => $assignmentDiscount + $paymentDiscount,
                        'paid_amount' => $paidAmount,
                        'fine_amount' => $fineAmount,
                        'due_amount' => $dueAmount,
                        'payments' => $payments,
                    ];
                })
                ->values();

            $paymentDetailsById = $feePaymentDetails->keyBy('id');
            $feeReceipts = FeesDetailsInvoices::select(
                    'fees_details_invoices.*',
                    'payment_modes.name as payment_mode'
                )
                ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
                ->where('fees_details_invoices.admission_id', $filteredAdmission->id)
                ->where('fees_details_invoices.session_id', $activeSessionId)
                ->where('fees_details_invoices.branch_id', $currentAdmission->branch_id)
                ->whereIn('fees_details_invoices.status', [0, 1])
                ->orderByDesc('fees_details_invoices.payment_date')
                ->orderByDesc('fees_details_invoices.id')
                ->get()
                ->map(function ($receipt) use ($paymentDetailsById) {
                    $detailIds = collect(explode(',', (string) $receipt->fees_details_id))
                        ->filter()
                        ->map(function ($id) {
                            return (int) $id;
                        });
                    $receipt->fee_head_names = $detailIds
                        ->map(function ($id) use ($paymentDetailsById) {
                            return optional($paymentDetailsById->get($id))->fee_head_name;
                        })
                        ->filter()
                        ->unique()
                        ->implode(', ');
                    return $receipt;
                });
        } else {
            $feeReceipts = collect();
        }

        $summary = [
            'totalFees' => round((float) $feeHeadLedger->sum('assigned_amount'), 2),
            'paidFees' => round((float) $feeHeadLedger->sum('paid_amount'), 2),
            'discount' => round((float) $feeHeadLedger->sum('discount'), 2),
            'currentSessionDue' => round((float) $feeHeadLedger->sum('due_amount'), 2),
            'finePaid' => round((float) $feeHeadLedger->sum('fine_amount'), 2),
        ];

        return view('student_login.fees_history', [
            'getFees' => $getFees,
            'feeHeadLedger' => $feeHeadLedger,
            'feeReceipts' => $feeReceipts,
            'summary' => $summary,
            'activeSessionId' => $activeSessionId,
        ]);
    }

    private function getStudentReceiptData($invoiceId): array
    {
        $currentAdmission = Admission::find(Session::get('id'));
        abort_unless($currentAdmission, 404);

        $admissionIds = collect([$currentAdmission->id]);
        if (!empty($currentAdmission->unique_system_id)) {
            $admissionIds = Admission::where('unique_system_id', $currentAdmission->unique_system_id)
                ->where('branch_id', $currentAdmission->branch_id)
                ->pluck('id');
        }

        $invoice = FeesDetailsInvoices::select(
                'fees_details_invoices.*',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.category',
                'class_types.name as class_name',
                'class_types.id as class_type_id',
                'gender.name as gender_name',
                'admissions.father_name',
                'admissions.admissionNo',
                'payment_modes.name as payment_mode',
                'payment_modes.id as payment_mode_id'
            )
            ->leftJoin('admissions', 'admissions.id', '=', 'fees_details_invoices.admission_id')
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->leftJoin('gender', 'gender.id', '=', 'admissions.gender_id')
            ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
            ->where('fees_details_invoices.id', $invoiceId)
            ->where('fees_details_invoices.branch_id', $currentAdmission->branch_id)
            ->whereIn('fees_details_invoices.admission_id', $admissionIds)
            ->whereIn('fees_details_invoices.status', [0, 1])
            ->first();

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

    public function viewReceipt($invoice)
    {
        [$invoiceData, $details] = $this->getStudentReceiptData($invoice);
        $printPreview = Helper::printPreview('Fees Collect');

        return view($printPreview, [
            'data' => $details,
            'invoice_data' => $invoiceData,
        ]);
    }

    public function downloadReceipt($invoice)
    {
        [$invoiceData, $details] = $this->getStudentReceiptData($invoice);
        $printPreview = Helper::printPreview('Fees Collect');
        $fileName = 'fees-receipt-'.($invoiceData->invoice_no ?: $invoiceData->id).'.pdf';

        return PDF::loadView($printPreview, [
            'data' => $details,
            'invoice_data' => $invoiceData,
        ])->download($fileName);
    }



        
                 
                 


    
}
