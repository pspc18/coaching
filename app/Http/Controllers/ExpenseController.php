<?php

namespace App\Http\Controllers;

use App\Models\BillCounter;
use App\Models\Expense;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class ExpenseController extends Controller
{
    private function categories(): array
    {
        return [
            1 => 'Faculty & Staff Welfare',
            2 => 'Rent & Premises',
            3 => 'Electricity & Utilities',
            4 => 'Internet, Phone & Software',
            5 => 'Printing & Stationery',
            6 => 'Study Material & Books',
            7 => 'Marketing & Advertising',
            8 => 'Repairs & Maintenance',
            9 => 'Furniture & Equipment',
            10 => 'Computers & Electronics',
            11 => 'Cleaning & Housekeeping',
            12 => 'Transport & Fuel',
            13 => 'Events, Tests & Seminars',
            14 => 'Professional & Legal Fees',
            15 => 'Bank Charges & Taxes',
            16 => 'Refreshments & Hospitality',
            17 => 'Security & Safety',
            18 => 'Other Operating Expense',
        ];
    }

    private function paymentModes()
    {
        return DB::table('payment_modes')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
    }

    public function expenseAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id' => 'nullable|array',
                'id.*' => 'nullable|integer|exists:expenses,id',
                'date' => 'required|date',
                'category' => 'required|array|min:1',
                'category.*' => 'required|integer|in:'.implode(',', array_keys($this->categories())),
                'name' => 'required|array|min:1',
                'name.*' => 'required|string|max:255',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|numeric|min:0.01|max:999999',
                'rate' => 'required|array|min:1',
                'rate.*' => 'required|numeric|min:0|max:99999999',
                'payment_mode_id' => 'required|integer|exists:payment_modes,id',
                'payee_name' => 'required|string|max:255',
                'bill_no' => 'nullable|string|max:100',
                'payment_reference' => 'nullable|string|max:150',
                'expense_type' => 'required|in:one_time,recurring',
                'recurring_frequency' => 'nullable|required_if:expense_type,recurring|in:monthly,quarterly,half_yearly,yearly',
                'payment_status' => 'required|in:paid,pending',
                'description' => 'nullable|string|max:1100',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            ]);

            $branchId = Session::get('branch_id');
            $sessionId = Session::get('session_id');
            $ids = array_filter($request->input('id', []));
            $existing = !empty($ids)
                ? Expense::whereIn('id', $ids)->where('branch_id', $branchId)->get()->keyBy('id')
                : collect();
            $invoiceNo = optional($existing->first())->invoice_no;

            if (!$invoiceNo) {
                $counter = BillCounter::firstOrCreate(
                    ['type' => 'expenses', 'branch_id' => $branchId],
                    ['session_id' => $sessionId, 'counter' => 0]
                );
                $counter->increment('counter');
                $invoiceNo = 'EXP-'.str_pad($counter->fresh()->counter, 5, '0', STR_PAD_LEFT);
            }

            $attachment = optional($existing->first())->attachment;
            if ($request->hasFile('attachment')) {
                if ($attachment && File::exists(env('IMAGE_UPLOAD_PATH').'expense/'.$attachment)) {
                    File::delete(env('IMAGE_UPLOAD_PATH').'expense/'.$attachment);
                }
                $file = $request->file('attachment');
                $attachment = time().'_'.uniqid().'_'.$file->getClientOriginalName();
                $file->move(env('IMAGE_UPLOAD_PATH').'expense', $attachment);
            }

            DB::transaction(function () use ($request, $validated, $existing, $invoiceNo, $branchId, $sessionId, $attachment) {
                $savedIds = [];
                foreach ($validated['name'] as $index => $name) {
                    $id = $request->input("id.$index");
                    $expense = $id && $existing->has((int) $id) ? $existing->get((int) $id) : new Expense();
                    $quantity = (float) $validated['quantity'][$index];
                    $rate = (float) $validated['rate'][$index];

                    $expense->fill([
                        'session_id' => $sessionId,
                        'branch_id' => $branchId,
                        'user_id' => null,
                        'created_by' => Session::get('id'),
                        'invoice_no' => $invoiceNo,
                        'category_id' => $validated['category'][$index],
                        'name' => $name,
                        'date' => $validated['date'],
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'amount' => round($quantity * $rate, 2),
                        'payment_mode_id' => $validated['payment_mode_id'],
                        'payee_name' => $validated['payee_name'],
                        'bill_no' => $validated['bill_no'] ?? null,
                        'payment_reference' => $validated['payment_reference'] ?? null,
                        'expense_type' => $validated['expense_type'],
                        'recurring_frequency' => $validated['expense_type'] === 'recurring' ? ($validated['recurring_frequency'] ?? null) : null,
                        'payment_status' => $validated['payment_status'],
                        'description' => $validated['description'] ?? null,
                        'attachment' => $attachment,
                    ]);
                    $expense->save();
                    $savedIds[] = $expense->id;
                }

                if ($existing->isNotEmpty()) {
                    Expense::where('invoice_no', $invoiceNo)
                        ->where('branch_id', $branchId)
                        ->whereNotIn('id', $savedIds)
                        ->delete();
                }

                Expense::where('invoice_no', $invoiceNo)
                    ->where('branch_id', $branchId)
                    ->update(['total_amt' => Expense::where('invoice_no', $invoiceNo)->where('branch_id', $branchId)->sum('amount')]);
            });

            return redirect('expenseView')->with('message', $existing->isNotEmpty() ? 'Expense voucher updated successfully.' : 'Expense voucher added successfully.');
        }

        return view('expense.add', [
            'categories' => $this->categories(),
            'paymentModes' => $this->paymentModes(),
            'data' => collect(),
            'editMode' => false,
        ]);
    }

    public function expenseView(Request $request)
    {
        $search = $request->only([
            'category', 'from_date', 'to_date', 'keyword', 'payment_status', 
            'expense_type', 'payment_mode_id', 'payee', 'name', 'voucher', 'date', 'receipt'
        ]);
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $query = Expense::query()
            ->leftJoin('payment_modes as pm', 'pm.id', '=', 'expenses.payment_mode_id')
            ->select('expenses.*', 'pm.name as payment_mode_name')
            ->where('expenses.session_id', $sessionId)
            ->where('expenses.branch_id', $branchId);

        if ($request->filled('category')) $query->where('expenses.category_id', $request->category);
        if ($request->filled('voucher')) $query->where('expenses.invoice_no', 'like', "%{$request->voucher}%");
        if ($request->filled('name')) $query->where('expenses.name', 'like', "%{$request->name}%");
        if ($request->filled('payee')) {
            $payee = $request->payee;
            $query->where(function($q) use ($payee) {
                $q->where('expenses.payee_name', 'like', "%{$payee}%")
                  ->orWhere('expenses.bill_no', 'like', "%{$payee}%");
            });
        }
        if ($request->filled('date')) $query->whereDate('expenses.date', $request->date);
        if ($request->filled('from_date')) $query->whereDate('expenses.date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('expenses.date', '<=', $request->to_date);
        if ($request->filled('payment_status')) $query->where('expenses.payment_status', $request->payment_status);
        if ($request->filled('expense_type')) $query->where('expenses.expense_type', $request->expense_type);
        if ($request->filled('payment_mode_id')) $query->where('expenses.payment_mode_id', $request->payment_mode_id);
        if ($request->filled('receipt')) {
            if ($request->receipt === 'with') {
                $query->whereNotNull('expenses.attachment')->where('expenses.attachment', '!=', '');
            } elseif ($request->receipt === 'without') {
                $query->where(function($q) {
                    $q->whereNull('expenses.attachment')->orWhere('expenses.attachment', '');
                });
            }
        }
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('expenses.name', 'like', "%{$keyword}%")
                    ->orWhere('expenses.payee_name', 'like', "%{$keyword}%")
                    ->orWhere('expenses.invoice_no', 'like', "%{$keyword}%")
                    ->orWhere('expenses.bill_no', 'like', "%{$keyword}%")
                    ->orWhere('expenses.payment_reference', 'like', "%{$keyword}%")
                    ->orWhere('expenses.description', 'like', "%{$keyword}%");
            });
        }

        $allData = (clone $query)->orderByDesc('expenses.date')->orderByDesc('expenses.id')->get();

        $stats = [
            'total_amount' => (float) $allData->sum('amount'),
            'total_count' => $allData->count(),
            'paid_amount' => (float) $allData->where('payment_status', 'paid')->sum('amount'),
            'paid_count' => $allData->where('payment_status', 'paid')->count(),
            'pending_amount' => (float) $allData->where('payment_status', 'pending')->sum('amount'),
            'pending_count' => $allData->where('payment_status', 'pending')->count(),
            'recurring_count' => $allData->where('expense_type', 'recurring')->count(),
            'recurring_amount' => (float) $allData->where('expense_type', 'recurring')->sum('amount'),
            'one_time_count' => $allData->where('expense_type', 'one_time')->count(),
            'one_time_amount' => (float) $allData->where('expense_type', 'one_time')->sum('amount'),
        ];

        $perPage = $request->get('per_page', 25);
        $page = (int) $request->get('page', 1);

        if ($perPage === 'all' || $perPage == -1) {
            $pagedData = $allData;
            $totalRecords = $allData->count();
            $lastPage = 1;
            $from = $totalRecords > 0 ? 1 : 0;
            $to = $totalRecords;
        } else {
            $perPage = (int) $perPage;
            if ($perPage <= 0) $perPage = 25;
            $totalRecords = $allData->count();
            $lastPage = max(1, (int) ceil($totalRecords / $perPage));
            $page = min(max(1, $page), $lastPage);
            $pagedData = $allData->slice(($page - 1) * $perPage, $perPage)->values();
            $from = $totalRecords > 0 ? (($page - 1) * $perPage + 1) : 0;
            $to = min($page * $perPage, $totalRecords);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $categories = $this->categories();
            $html = view('expense.table_rows', [
                'data' => $pagedData,
                'startIndex' => ($page - 1) * ($perPage === 'all' ? $totalRecords : (int)$perPage),
                'categories' => $categories,
            ])->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'total' => $totalRecords,
                'from' => $from,
                'to' => $to,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'stats' => $stats,
            ]);
        }

        return view('expense.view', [
            'data' => $pagedData,
            'allData' => $allData,
            'totalCount' => $totalRecords,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'perPage' => $perPage,
            'startIndex' => $from > 0 ? ($from - 1) : 0,
            'search' => $search,
            'stats' => $stats,
            'categories' => $this->categories(),
            'paymentModes' => $this->paymentModes(),
        ]);
    }

    public function expenseEdit($invoiceNo)
    {
        $data = Expense::where('invoice_no', $invoiceNo)
            ->where('branch_id', Session::get('branch_id'))
            ->orderBy('id')
            ->get();
        abort_if($data->isEmpty(), 404);

        return view('expense.add', [
            'categories' => $this->categories(),
            'paymentModes' => $this->paymentModes(),
            'data' => $data,
            'editMode' => true,
        ]);
    }

    public function expenseDelete(Request $request)
    {
        $expense = Expense::where('id', $request->delete_id)
            ->where('branch_id', Session::get('branch_id'))
            ->firstOrFail();
        $expense->delete();
        return redirect('expenseView')->with('message', 'Expense entry deleted successfully.');
    }

    public function expensePrint($invoiceNo)
    {
        $data = Expense::where('invoice_no', $invoiceNo)
            ->where('branch_id', Session::get('branch_id'))
            ->orderBy('id')
            ->get();
        abort_if($data->isEmpty(), 404);
        $categories = $this->categories();
        $paymentMode = DB::table('payment_modes')->where('id', $data->first()->payment_mode_id)->value('name');
        return view('print_file.expense.expense_print', compact('data', 'categories', 'paymentMode'));
    }
}
