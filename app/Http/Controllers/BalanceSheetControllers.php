<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Account;
use App\Models\Master\MessageTemplate;
use App\Models\fees\FeesDetailsInvoices;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\Expense;
use Session;
use Hash;
use Str;
use Redirect;
use File;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class BalanceSheetControllers extends Controller

{
 
    public function view(Request $request)
    {
        $session_id = Session::get('session_id');
        $branch_id  = Session::get('branch_id');

        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $active_tab = $request->get('active_tab', 'dashboard_summary');

        $today = date('Y-m-d');
        $paymentDateColumn = 'created_at';
        $expenseDateColumn = 'created_at';

        // 1. Optimized Period Revenue Aggregations (Single Grouped Query)
        $summaryQuery = FeesDetailsInvoices::where('fees_details_invoices.session_id', $session_id)
            ->where('fees_details_invoices.branch_id', $branch_id)
            ->where('fees_details_invoices.status', '!=', 2);

        if (!empty($from_date)) {
            $summaryQuery->whereDate('fees_details_invoices.' . $paymentDateColumn, '>=', $from_date);
        }

        if (!empty($to_date)) {
            $summaryQuery->whereDate('fees_details_invoices.' . $paymentDateColumn, '<=', $to_date);
        }

        $modeAggregates = (clone $summaryQuery)
            ->selectRaw('payment_mode, SUM(amount) as total_amount, SUM(discount) as total_discount, COUNT(*) as tx_count')
            ->groupBy('payment_mode')
            ->get()
            ->keyBy('payment_mode');

        $cashTotal       = (float) ($modeAggregates->get(1)->total_amount ?? 0);
        $chequeTotal     = (float) ($modeAggregates->get(2)->total_amount ?? 0);
        $netBankingTotal = (float) ($modeAggregates->get(3)->total_amount ?? 0);
        $upiTotal        = (float) ($modeAggregates->get(6)->total_amount ?? 0);

        $cashDiscount       = (float) ($modeAggregates->get(1)->total_discount ?? 0);
        $chequeDiscount     = (float) ($modeAggregates->get(2)->total_discount ?? 0);
        $netBankingDiscount = (float) ($modeAggregates->get(3)->total_discount ?? 0);
        $upiDiscount        = (float) ($modeAggregates->get(6)->total_discount ?? 0);

        $totalRevenue = $modeAggregates->sum('total_amount');
        $totalDiscount = $modeAggregates->sum('total_discount');

        // 2. Period Expense Aggregation & Query
        $expenseQuery = Expense::where('session_id', $session_id)
            ->where('branch_id', $branch_id)
            ->whereNull('deleted_at');

        if (!empty($from_date)) {
            $expenseQuery->whereDate($expenseDateColumn, '>=', $from_date);
        }

        if (!empty($to_date)) {
            $expenseQuery->whereDate($expenseDateColumn, '<=', $to_date);
        }

        $expenseTotal = (float) (clone $expenseQuery)->sum('amount');
        $totalProfit = $totalRevenue - $expenseTotal;

        // 3. Today Summary Aggregations (Single Grouped Query)
        $todaySummaries = FeesDetailsInvoices::where('session_id', $session_id)
            ->where('branch_id', $branch_id)
            ->where('status', '!=', 2)
            ->whereDate($paymentDateColumn, $today)
            ->selectRaw('payment_mode, SUM(amount) as total_amount, COUNT(*) as tx_count')
            ->groupBy('payment_mode')
            ->get()
            ->keyBy('payment_mode');

        $todayCashTotal       = (float) ($todaySummaries->get(1)->total_amount ?? 0);
        $todayChequeTotal     = (float) ($todaySummaries->get(2)->total_amount ?? 0);
        $todayNetBankingTotal = (float) ($todaySummaries->get(3)->total_amount ?? 0);
        $todayUpiTotal        = (float) ($todaySummaries->get(6)->total_amount ?? 0);
        $todayRevenue         = (float) $todaySummaries->sum('total_amount');

        $todayExpenseTotal = (float) Expense::where('session_id', $session_id)
            ->where('branch_id', $branch_id)
            ->whereNull('deleted_at')
            ->whereDate($expenseDateColumn, $today)
            ->sum('amount');

        $todayProfit = $todayRevenue - $todayExpenseTotal;

        // 4. Detailed Table Lists (Optimized selective columns & joins)
        $baseListQuery = FeesDetailsInvoices::select(
                'fees_details_invoices.id',
                'fees_details_invoices.admission_id',
                'fees_details_invoices.invoice_no',
                'fees_details_invoices.offline_receipt_no',
                'fees_details_invoices.payment_mode',
                'fees_details_invoices.payment_date',
                'fees_details_invoices.bank_name',
                'fees_details_invoices.transaction_id',
                'fees_details_invoices.cheque_date',
                'fees_details_invoices.cheque_number',
                'fees_details_invoices.amount',
                'fees_details_invoices.discount',
                'fees_details_invoices.created_at',
                'class_types.name as class_name',
                'admissions.admissionNo',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.father_name',
                'payment_modes.name as payment_mode_name'
            )
            ->leftJoin('admissions', 'admissions.id', '=', 'fees_details_invoices.admission_id')
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
            ->where('fees_details_invoices.session_id', $session_id)
            ->where('fees_details_invoices.branch_id', $branch_id)
            ->where('fees_details_invoices.status', '!=', 2);

        if (!empty($from_date)) {
            $baseListQuery->whereDate('fees_details_invoices.' . $paymentDateColumn, '>=', $from_date);
        }

        if (!empty($to_date)) {
            $baseListQuery->whereDate('fees_details_invoices.' . $paymentDateColumn, '<=', $to_date);
        }

        $allPayments = $baseListQuery->orderBy('fees_details_invoices.id', 'DESC')->get();

        $cash       = $allPayments->where('payment_mode', 1)->values();
        $upi        = $allPayments->where('payment_mode', 6)->values();
        $cheque     = $allPayments->where('payment_mode', 2)->values();
        $netBanking = $allPayments->where('payment_mode', 3)->values();
        $expense    = $expenseQuery->orderBy('id', 'DESC')->get();

        $counts = [
            'dashboard_summary' => 0,
            'all_payments'      => $allPayments->count(),
            'cash_payment'      => $cash->count(),
            'upi_payment'       => $upi->count(),
            'cheque_payment'    => $cheque->count(),
            'net_banking'       => $netBanking->count(),
            'expense'           => $expense->count(),
        ];

        $data = [
            'allPayments' => $allPayments,
            'cash' => $cash,
            'upi' => $upi,
            'cheque' => $cheque,
            'netBanking' => $netBanking,
            'expense' => $expense,

            'cashTotal' => $cashTotal,
            'upiTotal' => $upiTotal,
            'chequeTotal' => $chequeTotal,
            'netBankingTotal' => $netBankingTotal,

            'cashDiscount' => $cashDiscount,
            'chequeDiscount' => $chequeDiscount,
            'netBankingDiscount' => $netBankingDiscount,
            'upiDiscount' => $upiDiscount,
            'totalDiscount' => $totalDiscount,

            'totalRevenue' => $totalRevenue,
            'expenseTotal' => $expenseTotal,
            'totalProfit' => $totalProfit,
            'netBalance' => $totalProfit,

            'todayCashTotal' => $todayCashTotal,
            'todayUpiTotal' => $todayUpiTotal,
            'todayChequeTotal' => $todayChequeTotal,
            'todayNetBankingTotal' => $todayNetBankingTotal,
            'todayRevenue' => $todayRevenue,
            'todayExpenseTotal' => $todayExpenseTotal,
            'todayProfit' => $todayProfit,
            'todayNetBalance' => $todayProfit,

            'counts' => $counts,
            'active_tab' => $active_tab,
        ];

        return view('BalanceSheet.view', compact('data'));
    }

    
  
    
} 




