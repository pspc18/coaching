<?php

namespace App\Http\Controllers\student_login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admission;
use App\Models\OnlinePayment;
use App\Models\FeesCollect;
use App\Models\FeesDetail;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\FeesSetting;
use App\Models\Sessions;
use App\Models\fees\FeesAssignDetail;
use App\Services\PhonePeService;
use Session;
use DB;
use Log;

class PaymentController extends Controller
{
    protected PhonePeService $phonePeService;

    public function __construct(PhonePeService $phonePeService)
    {
        $this->phonePeService = $phonePeService;
    }

    /**
     * Step 1 & 2: Initiate Payment from Student Dashboard
     */
    public function initiate(Request $request)
    {
        $studentId = Session::get('id');
        if (!$studentId) {
            return response()->json([
                'status' => false,
                'message' => 'Your student session has expired. Please log in again.',
            ], 401);
        }

        $student = Admission::find($studentId);
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student record not found.',
            ], 404);
        }

        $sessionId = Session::get('session_id') ?? $student->session_id;
        $branchId = Session::get('branch_id') ?? $student->branch_id ?? 1;

        // Calculate pending fees
        $assignedFees = (float) FeesAssignDetail::where('admission_id', $studentId)
            ->where('session_id', $sessionId)
            ->sum('fees_group_amount');

        $collectedFees = (float) FeesDetail::where('admission_id', $studentId)
            ->where('session_id', $sessionId)
            ->where('status', '!=', '2')
            ->sum('total_amount');

        $pendingFees = max(0, $assignedFees - $collectedFees);

        if ($pendingFees <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'No outstanding fees found. All dues are already cleared!',
            ], 422);
        }

        // Validate requested amount
        $amount = (float) $request->input('amount', $pendingFees);
        if ($amount <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter a valid amount greater than ₹0.',
            ], 422);
        }

        if ($amount > $pendingFees) {
            return response()->json([
                'status' => false,
                'message' => 'Payment amount cannot exceed your pending balance of ₹' . number_format($pendingFees, 2),
            ], 422);
        }

        // Generate unique merchant order ID (Format: ARISE_{student_id}_{timestamp}_{rand})
        $merchantOrderId = 'ARISE_' . $studentId . '_' . time() . '_' . rand(100, 999);

        // Create OnlinePayment record in PENDING state
        $order = OnlinePayment::create([
            'admission_id' => $studentId,
            'branch_id' => $branchId,
            'session_id' => $sessionId,
            'merchant_order_id' => $merchantOrderId,
            'amount' => $amount,
            'amount_paise' => (int) round($amount * 100),
            'payment_status' => 'PENDING',
            'payment_mode' => 'PhonePe',
        ]);

        $redirectUrl = route('student.payment.phonepe.callback', [
            'merchant_order_id' => $merchantOrderId,
        ]);

        $metaInfo = [
            'udf1' => (string) $studentId,
            'udf2' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
            'udf3' => (string) $branchId,
            'udf4' => (string) $sessionId,
            'udf5' => (string) $student->admissionNo,
        ];

        // Call PhonePe Service
        $res = $this->phonePeService->createPaymentOrder($merchantOrderId, $amount, $redirectUrl, $metaInfo);

        if ($res['success']) {
            $order->update([
                'phonepe_order_id' => $res['phonepe_order_id'] ?? null,
                'request_payload' => $metaInfo,
                'response_payload' => $res,
            ]);

            return response()->json([
                'status' => true,
                'redirect_url' => $res['redirect_url'],
                'merchant_order_id' => $merchantOrderId,
                'amount' => $amount,
                'is_simulation' => $res['is_simulation'] ?? false,
            ]);
        }

        $order->update([
            'payment_status' => 'FAILED',
            'failure_reason' => $res['message'] ?? 'Failed to initiate order with PhonePe',
            'response_payload' => $res,
        ]);

        return response()->json([
            'status' => false,
            'message' => $res['message'] ?? 'Failed to initiate PhonePe payment. Please try again.',
        ], 500);
    }

    /**
     * PhonePe Sandbox Simulator (For testing when real keys are not active)
     */
    public function simulator(Request $request)
    {
        $merchantOrderId = $request->query('order_id');
        $order = OnlinePayment::where('merchant_order_id', $merchantOrderId)->firstOrFail();
        $student = Admission::find($order->admission_id);

        return view('student_login.payment.phonepe_simulator', [
            'order' => $order,
            'student' => $student,
        ]);
    }

    /**
     * Step 3 & 4: PhonePe Redirect Callback Handler
     */
    public function callback(Request $request)
    {
        $merchantOrderId = $request->input('merchant_order_id') ?? $request->query('merchant_order_id');
        $simulationAction = $request->input('simulation_action');

        if (!$merchantOrderId) {
            return redirect()->route('feesHistory')->withErrors(['error' => 'Invalid payment callback: Order ID missing.']);
        }

        $order = OnlinePayment::with(['admission.ClassTypes', 'invoice'])
            ->where('merchant_order_id', $merchantOrderId)
            ->first();

        if (!$order) {
            return redirect('fees_history')->withErrors(['error' => 'Payment transaction record not found.']);
        }

        $student = $order->admission ?? Admission::find($order->admission_id);

        // Keep student session active if it was dropped during gateway redirect
        if ($student) {
            if (!Session::has('id') || Session::get('id') != $student->id) {
                Session::put('id', $student->id);
                Session::put('role_id', 3);
                Session::put('branch_id', $student->branch_id ?? 1);
                Session::put('session_id', $order->session_id);
                Session::put('first_name', $student->first_name);
                Session::put('last_name', $student->last_name);
            }
        }

        // Check if simulation failure was triggered
        if ($simulationAction === 'fail') {
            $order->update([
                'payment_status' => 'FAILED',
                'failure_reason' => 'User simulated payment failure in Sandbox Simulator',
            ]);

            return view('student_login.payment.failure', [
                'order' => $order,
                'student' => $student,
                'reason' => 'Transaction was cancelled or declined.',
            ]);
        }

        // Fast-path: If already settled and completed, directly render success view
        if ($order->payment_status === 'COMPLETED') {
            return view('student_login.payment.success', [
                'order' => $order,
                'student' => $student,
                'invoice' => $order->invoice,
            ]);
        }

        // Verify status with PhonePe
        $statusRes = $this->phonePeService->checkPaymentStatus($order->merchant_order_id);

        if ($statusRes['success'] || $simulationAction === 'success') {
            // Settle payment in ERP if not already completed
            if ($order->payment_status !== 'COMPLETED') {
                $this->settleSuccessfulPayment($order, $statusRes['transaction_id'] ?? ('TXN_' . time()));
            }

            $freshOrder = $order->fresh(['admission.ClassTypes', 'invoice']);

            return view('student_login.payment.success', [
                'order' => $freshOrder,
                'student' => $student,
                'invoice' => $freshOrder->invoice,
            ]);
        }

        $order->update([
            'payment_status' => 'FAILED',
            'failure_reason' => $statusRes['message'] ?? 'Payment verification failed at PhonePe.',
            'response_payload' => $statusRes,
        ]);

        return view('student_login.payment.failure', [
            'order' => $order,
            'student' => $student,
            'reason' => $statusRes['message'] ?? 'Payment was not completed.',
        ]);
    }

    /**
     * Server-to-Server Webhook Handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PhonePe Webhook Received', $payload);

        $merchantOrderId = $payload['payload']['merchantOrderId'] ?? $payload['merchantOrderId'] ?? null;
        $state = $payload['payload']['state'] ?? $payload['state'] ?? null;

        if ($merchantOrderId && $state === 'COMPLETED') {
            $order = OnlinePayment::where('merchant_order_id', $merchantOrderId)->first();
            if ($order && $order->payment_status !== 'COMPLETED') {
                $txnId = $payload['payload']['transactionId'] ?? $payload['transactionId'] ?? null;
                $this->settleSuccessfulPayment($order, $txnId);
            }
        }

        return response()->json(['status' => 'SUCCESS']);
    }

    /**
     * Settle Successful Payment: Allocate Fee & Generate Official ERP Receipt
     */
    protected function settleSuccessfulPayment(OnlinePayment $order, ?string $transactionId = null)
    {
        DB::transaction(function () use ($order, $transactionId) {
            $studentId = $order->admission_id;
            $sessionId = $order->session_id;
            $branchId = $order->branch_id;
            $paidTotal = (float) $order->amount;

            // 1. Resolve Running Receipt No from FeesDetailsInvoices & FeesSetting
            $feesSetting = FeesSetting::getSetting($branchId, $sessionId);
            $sessionRec = Sessions::find($sessionId);
            $sessionName = $sessionRec ? ($sessionRec->from_year . '-' . $sessionRec->to_year) : '';

            $invoiceCount = FeesDetailsInvoices::where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->count();
            $nextCounter = $invoiceCount + 1;

            $receiptNo = $feesSetting ? $feesSetting->formatReceiptNumber($nextCounter, $sessionName) : ('REC-' . $nextCounter);

            // 2. Resolve FeesCollect record
            $feesCollect = FeesCollect::where('admission_id', $studentId)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->first();

            if (!empty($feesCollect)) {
                $feesCollect->increment('amount', $paidTotal);
            } else {
                $firstAssign = FeesAssignDetail::where('admission_id', $studentId)->where('session_id', $sessionId)->first();
                $feesCollect = new FeesCollect();
                $feesCollect->user_id = $studentId;
                $feesCollect->session_id = $sessionId;
                $feesCollect->branch_id = $branchId;
                $feesCollect->admission_id = $studentId;
                $feesCollect->fees_assign_id = $firstAssign->fees_assign_id ?? 1;
                $feesCollect->amount = $paidTotal;
                $feesCollect->discount = 0;
                $feesCollect->save();
            }

            // 3. Allocate paid amount across pending fee groups
            $assignedGroups = FeesAssignDetail::where('admission_id', $studentId)
                ->where('session_id', $sessionId)
                ->orderBy('id', 'ASC')
                ->get();

            $remainingAmountToAllocate = $paidTotal;
            $createdFeeDetailIds = [];

            foreach ($assignedGroups as $group) {
                if ($remainingAmountToAllocate <= 0) {
                    break;
                }

                // Check how much is already paid for this specific group
                $alreadyPaidForGroup = (float) FeesDetail::where('admission_id', $studentId)
                    ->where('session_id', $sessionId)
                    ->where('fees_group_id', $group->fees_group_id)
                    ->where('status', '!=', '2')
                    ->sum('total_amount');

                $groupDue = max(0, ((float) $group->fees_group_amount) - $alreadyPaidForGroup);

                if ($groupDue > 0) {
                    $allocateForThisGroup = min($remainingAmountToAllocate, $groupDue);

                    $feeDetail = new FeesDetail();
                    $feeDetail->user_id = $studentId;
                    $feeDetail->session_id = $sessionId;
                    $feeDetail->branch_id = $branchId;
                    $feeDetail->fees_collect_id = $feesCollect->id;
                    $feeDetail->fees_group_id = $group->fees_group_id;
                    $feeDetail->receipt_no = $receiptNo;
                    $feeDetail->offline_receipt_no = $receiptNo;
                    $feeDetail->admission_id = $studentId;
                    $feeDetail->paid_amount = $allocateForThisGroup;
                    $feeDetail->installment_fine = 0;
                    $feeDetail->discount = 0;
                    $feeDetail->total_amount = $allocateForThisGroup;
                    $feeDetail->payment_mode_id = 6; // UPI / PhonePe
                    $feeDetail->status = 1;
                    $feeDetail->date = date('Y-m-d');
                    $feeDetail->save();

                    $createdFeeDetailIds[] = $feeDetail->id;
                    $remainingAmountToAllocate -= $allocateForThisGroup;
                }
            }

            // If any leftover amount (advance payment), allocate to first available group
            if ($remainingAmountToAllocate > 0 && count($assignedGroups) > 0) {
                $firstGroup = $assignedGroups->first();
                $feeDetail = new FeesDetail();
                $feeDetail->user_id = $studentId;
                $feeDetail->session_id = $sessionId;
                $feeDetail->branch_id = $branchId;
                $feeDetail->fees_collect_id = $feesCollect->id;
                $feeDetail->fees_group_id = $firstGroup->fees_group_id;
                $feeDetail->receipt_no = $receiptNo;
                $feeDetail->offline_receipt_no = $receiptNo;
                $feeDetail->admission_id = $studentId;
                $feeDetail->paid_amount = $remainingAmountToAllocate;
                $feeDetail->installment_fine = 0;
                $feeDetail->discount = 0;
                $feeDetail->total_amount = $remainingAmountToAllocate;
                $feeDetail->payment_mode_id = 6;
                $feeDetail->status = 1;
                $feeDetail->date = date('Y-m-d');
                $feeDetail->save();

                $createdFeeDetailIds[] = $feeDetail->id;
            }

            // 4. Create FeesDetailsInvoices record
            $invoice = new FeesDetailsInvoices();
            $invoice->user_id = $studentId;
            $invoice->admission_id = $studentId;
            $invoice->branch_id = $branchId;
            $invoice->session_id = $sessionId;
            $invoice->fees_details_id = implode(',', $createdFeeDetailIds);
            $invoice->invoice_no = $receiptNo;
            $invoice->amount = $paidTotal;
            $invoice->payment_date = date('Y-m-d');
            $invoice->payment_mode = 6; // PhonePe UPI
            $invoice->transaction_id = $transactionId ?? $order->merchant_order_id;
            $invoice->status = 1;
            $invoice->remark = 'Paid via PhonePe Payment Gateway';
            $invoice->save();

            // 5. Update OnlinePayment status & link invoice
            $order->update([
                'payment_status' => 'COMPLETED',
                'phonepe_transaction_id' => $transactionId ?? $order->merchant_order_id,
                'invoice_id' => $invoice->id,
            ]);
        });
    }
}
