@php
    use App\Helpers\helper;
    use App\Models\FeesSetting;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    $getSetting = Helper::getSetting();

    // Standardize $data as an iterable collection
    $dataItems = is_iterable($data ?? null) ? $data : (isset($data) ? collect([$data]) : collect());
    $firstItem = $dataItems->first();

    // Resolve Admission details
    $admissionId = $invoice_data->admission_id ?? $firstItem->admission_id ?? ($firstItem->Admission->id ?? null);
    $admission = null;
    if ($admissionId) {
        $admission = DB::table('admissions')
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->where('admissions.id', $admissionId)
            ->select('admissions.*', 'class_types.name as class_name')
            ->first();
    }

    $branchId = $invoice_data->branch_id ?? $firstItem->branch_id ?? Session::get('branch_id') ?? 1;
    $sessionId = $invoice_data->session_id ?? $firstItem->session_id ?? Session::get('session_id') ?? 1;

    // Load Fees Setting
    $feesSetting = null;
    try {
        $feesSetting = FeesSetting::getSetting($branchId, $sessionId);
    } catch (\Throwable $e) {
        $feesSetting = null;
    }

    $receiptLayout = $feesSetting->receipt_layout ?? 'dual_copy'; // 'single_a4', 'dual_copy', 'thermal'
    $headerTitle = !empty($feesSetting->receipt_header_title) ? $feesSetting->receipt_header_title : 'FEE RECEIPT';
    $showLogo = $feesSetting->show_school_logo ?? 1;
    $showWatermark = $feesSetting->show_watermark ?? 1;
    $showSignature = $feesSetting->show_signature_box ?? 1;
    $showPaymentDetails = $feesSetting->show_payment_mode_details ?? 1;
    $termsConditions = $feesSetting->receipt_terms_conditions ?? "1. Fees once deposited is strictly non-refundable and non-transferable under any circumstances.\n2. In case of payment by Cheque/DD, receipt is valid subject to realization.\n3. Please preserve this receipt carefully for all future academic references.";

    // Session name
    $sessionRow = DB::table('sessions')->whereNull('deleted_at')->where('id', $sessionId)->first();
    $sessionName = $sessionRow ? ($sessionRow->from_year . '-' . $sessionRow->to_year) : '';

    // Receipt / Invoice Number
    $receiptNo = $invoice_data->invoice_no ?? $firstItem->receipt_no ?? ($firstItem->offline_receipt_no ?? 'REC-' . str_pad($firstItem->id ?? 1, 4, '0', STR_PAD_LEFT));
    
    // Dates
    $paymentDateRaw = $invoice_data->payment_date ?? $firstItem->date ?? ($firstItem->created_at ?? date('Y-m-d'));
    $paymentDate = date('d-M-Y', strtotime($paymentDateRaw));

    // Student Info
    $studentName = trim(($invoice_data->first_name ?? $admission->first_name ?? $firstItem->first_name ?? '') . ' ' . ($invoice_data->last_name ?? $admission->last_name ?? $firstItem->last_name ?? ''));
    $fatherName = $invoice_data->father_name ?? $admission->father_name ?? $firstItem->father_name ?? '-';
    $admissionNo = $invoice_data->admissionNo ?? $admission->admissionNo ?? $firstItem->admissionNo ?? '-';
    $className = $invoice_data->class_name ?? $admission->class_name ?? ($firstItem->ClassTypes->name ?? '-');
    $mobile = $admission->mobile ?? $firstItem->Admission->mobile ?? $firstItem->mobile ?? '-';

    // Payment Mode
    $paymentModeName = $invoice_data->payment_mode ?? $firstItem->payment_mode ?? '-';
    if ($paymentModeName === '-' && !empty($firstItem->payment_mode_id)) {
        $pm = DB::table('payment_modes')->where('id', $firstItem->payment_mode_id)->first();
        if ($pm) $paymentModeName = $pm->name;
    }
    $txnId = $invoice_data->transaction_id ?? $firstItem->transition_id ?? ($firstItem->transaction_id ?? '-');
    $bankName = $invoice_data->bank_name ?? $firstItem->bank_name ?? '-';
    $chequeNumber = $invoice_data->cheque_number ?? $firstItem->cheque_number ?? '-';
    $remarks = $invoice_data->remark ?? $firstItem->remark ?? '';

    // Logo helper
    if (!function_exists('getReceiptLogoBase64')) {
        function getReceiptLogoBase64($getSetting) {
            $logoName = $getSetting['left_logo'] ?? '';
            $uploadPath = rtrim(env('IMAGE_UPLOAD_PATH', ''), '/\\');
            $showPath = rtrim(env('IMAGE_SHOW_PATH', ''), '/\\');
            
            $localFile = $uploadPath . '/setting/left_logo/' . $logoName;
            if (!empty($logoName) && file_exists($localFile)) {
                $data = @file_get_contents($localFile);
                if ($data) {
                    return 'data:image/' . pathinfo($localFile, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
                }
            }
            $url = $showPath . '/setting/left_logo/' . $logoName;
            if (!empty($logoName)) {
                $data = @file_get_contents($url);
                if ($data) {
                    return 'data:image/' . pathinfo($url, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
                }
            }
            return null;
        }
    }
    $logoBase64 = getReceiptLogoBase64($getSetting);

    // Number to words
    if (!function_exists('numToWordsINR')) {
        function numToWordsINR($num) {
            $num = (float) $num;
            if (class_exists('NumberFormatter')) {
                try {
                    $formatter = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
                    $words = $formatter->format($num);
                    return ucwords($words) . ' Rupees Only';
                } catch (\Throwable $e) {}
            }
            return 'INR ' . number_format($num, 2) . ' Only';
        }
    }

    $copies = ($receiptLayout === 'dual_copy') ? ['STUDENT COPY', 'OFFICE COPY'] : ['ORIGINAL COPY'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $receiptNo }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 11.5px;
            line-height: 1.4;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Screen Toolbar */
        .no-print-toolbar {
            max-width: 820px;
            margin: 15px auto 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 18px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .toolbar-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        /* Receipt Outer Page */
        .page-sheet {
            max-width: {{ $receiptLayout === 'thermal' ? '320px' : '820px' }};
            margin: 0 auto 30px auto;
            background: #ffffff;
            padding: {{ $receiptLayout === 'thermal' ? '8px' : ($receiptLayout === 'dual_copy' ? '12px 18px' : '22px 28px') }};
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-radius: 8px;
        }

        /* Single Receipt Card */
        .receipt-card {
            border: 1.5px solid #0f172a;
            border-radius: 6px;
            padding: {{ $receiptLayout === 'dual_copy' ? '10px 14px' : '16px 20px' }};
            position: relative;
            background: #ffffff;
            page-break-inside: avoid;
        }

        /* Perforated Cut Divider */
        .cut-divider {
            position: relative;
            margin: 14px 0;
            text-align: center;
            border-top: 1.5px dashed #94a3b8;
            height: 1px;
        }
        .cut-divider-badge {
            position: absolute;
            top: -9px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            padding: 0 12px;
            font-size: 9.5px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* Watermark */
        .watermark-wrap {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            pointer-events: none;
            opacity: 0.08;
            z-index: 1;
            font-size: 80px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 12px;
            text-transform: uppercase;
            user-select: none;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-logo-cell {
            width: 85px;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo-img {
            max-width: 80px;
            max-height: 70px;
            object-fit: contain;
        }
        .header-center-cell {
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }
        .institute-name {
            font-size: {{ $receiptLayout === 'dual_copy' ? '18px' : '22px' }};
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .institute-meta {
            font-size: 10.5px;
            color: #334155;
            margin-top: 3px;
            line-height: 1.35;
        }
        .header-badge-cell {
            width: 130px;
            vertical-align: middle;
            text-align: right;
        }
        .receipt-title-tag {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .copy-type-pill {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #c7d2fe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Student & Receipt Meta Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .meta-table td {
            padding: 4.5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }
        .meta-lbl {
            font-weight: 600;
            color: #475569;
            width: 14%;
        }
        .meta-val {
            font-weight: 700;
            color: #0f172a;
            width: 36%;
        }
        .receipt-highlight {
            color: #dc2626;
            font-weight: 800;
            font-size: 12px;
        }

        /* Ledger Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
        }
        .ledger-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .ledger-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #1e293b;
        }
        .ledger-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .ledger-table .text-right {
            text-align: right;
        }
        .ledger-table .text-center {
            text-align: center;
        }

        /* Financial Summary Box */
        .summary-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 12px;
        }
        .payment-info-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 10.5px;
            line-height: 1.45;
        }
        .payment-info-title {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-size: 10px;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .total-amount-card {
            min-width: 220px;
            border: 1.5px solid #0f172a;
            border-radius: 4px;
            background: #f8fafc;
            overflow: hidden;
            text-align: right;
        }
        .total-amount-row {
            padding: 5px 10px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .total-amount-row.grand-total {
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            font-size: 13px;
            border-bottom: none;
        }

        /* Words Bar */
        .words-bar {
            background: #eff6ff;
            border-left: 3px solid #2563eb;
            padding: 5px 10px;
            border-radius: 0 4px 4px 0;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .words-lbl {
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            font-size: 10px;
            margin-right: 4px;
        }
        .words-val {
            font-weight: 700;
            color: #0f172a;
        }

        /* Footer & Signatures */
        .receipt-footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .terms-cell {
            vertical-align: top;
            width: 60%;
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.35;
            padding-right: 15px;
        }
        .terms-cell strong {
            color: #334155;
        }
        .signatures-cell {
            vertical-align: bottom;
            width: 40%;
            text-align: right;
        }
        .sig-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding-top: 25px;
        }
        .sig-block {
            text-align: center;
            flex: 1;
        }
        .sig-line {
            border-top: 1px solid #475569;
            margin-bottom: 4px;
        }
        .sig-lbl {
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Print Media Styles */
        @media print {
            .no-print, .no-print-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 6mm;
            }
            .receipt-card {
                border: 1.5px solid #0f172a !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar -->
    <div class="no-print-toolbar">
        <div class="toolbar-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Receipt Preview &mdash; <strong>{{ $receiptNo }}</strong></span>
        </div>
        <div class="toolbar-actions">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Receipt
            </button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>

    <!-- Sheet Wrapper -->
    <div class="page-sheet">
        @foreach($copies as $copyIndex => $copyLabel)
            @if($copyIndex > 0)
                <!-- Cut Perforation -->
                <div class="cut-divider">
                    <span class="cut-divider-badge">✂ &nbsp; Cut Here &mdash; Student / Office Copy Separation &nbsp; ✂</span>
                </div>
            @endif

            <div class="receipt-card">
                @if($showWatermark)
                    <div class="watermark-wrap">PAID</div>
                @endif

                <!-- Header -->
                <table class="header-table">
                    <tr>
                        @if($showLogo && $logoBase64)
                            <td class="header-logo-cell">
                                <img src="{{ $logoBase64 }}" alt="Logo" class="header-logo-img">
                            </td>
                        @endif
                        <td class="header-center-cell">
                            <div class="institute-name">{{ $getSetting['name'] ?? 'INSTITUTE FEE RECEIPT' }}</div>
                            <div class="institute-meta">
                                @if(!empty($getSetting['address']))
                                    <span>{{ $getSetting['address'] }}</span> &bull; 
                                @endif
                                @if(!empty($getSetting['mobile']))
                                    <span><strong>Tel:</strong> {{ $getSetting['mobile'] }}</span>
                                @endif
                                @if(!empty($getSetting['gmail']))
                                    &bull; <span><strong>Email:</strong> {{ $getSetting['gmail'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="header-badge-cell">
                            <span class="receipt-title-tag">{{ $headerTitle }}</span><br>
                            <span class="copy-type-pill">{{ $copyLabel }}</span>
                        </td>
                    </tr>
                </table>

                <!-- Student & Metadata Grid -->
                <table class="meta-table">
                    <tr>
                        <td class="meta-lbl">Receipt No</td>
                        <td class="meta-val"><span class="receipt-highlight">{{ $receiptNo }}</span></td>
                        <td class="meta-lbl">Payment Date</td>
                        <td class="meta-val">{{ $paymentDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Student Name</td>
                        <td class="meta-val">{{ $studentName }}</td>
                        <td class="meta-lbl">Scholar / Adm No</td>
                        <td class="meta-val">{{ $admissionNo }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Father's Name</td>
                        <td class="meta-val">{{ $fatherName }}</td>
                        <td class="meta-lbl">Class / Course</td>
                        <td class="meta-val">{{ $className }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Contact No</td>
                        <td class="meta-val">{{ $mobile }}</td>
                        <td class="meta-lbl">Academic Session</td>
                        <td class="meta-val">{{ $sessionName ?: '-' }}</td>
                    </tr>
                </table>

                <!-- Ledger Breakdown Table -->
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th style="width: 35%;">Fee Particulars / Head</th>
                            <th class="text-center" style="width: 15%;">Due Date</th>
                            <th class="text-right" style="width: 15%;">Fee Amount (₹)</th>
                            <th class="text-right" style="width: 15%;">Fine (₹)</th>
                            <th class="text-right" style="width: 15%;">Paid Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $calcTotalFee = 0;
                            $calcTotalFine = 0;
                            $calcTotalPaid = 0;
                            $sno = 1;
                        @endphp
                        @foreach($dataItems as $item)
                            @php
                                $fgId = $item['fees_group_id'] ?? ($item->fees_group_id ?? 0);
                                $itemAdmId = $item['admission_id'] ?? ($item->admission_id ?? $admissionId);
                                $itemSessId = $item['session_id'] ?? ($item->session_id ?? $sessionId);
                                $itemBranchId = $item['branch_id'] ?? ($item->branch_id ?? $branchId);

                                $feesGroup = null;
                                if ($fgId) {
                                    $feesGroup = DB::table('fees_group')->whereNull('deleted_at')->where('id', $fgId)->first();
                                }
                                $feesAssign = null;
                                if ($fgId && $itemAdmId) {
                                    $feesAssign = DB::table('fees_assign_details')
                                        ->whereNull('deleted_at')
                                        ->where('admission_id', $itemAdmId)
                                        ->where('fees_group_id', $fgId)
                                        ->where('session_id', $itemSessId)
                                        ->where('branch_id', $itemBranchId)
                                        ->first();
                                }

                                $headName = $item['fees_group_name'] ?? ($item->fees_group_name ?? ($feesGroup->name ?? 'Fee Head'));
                                $dueDateStr = '';
                                if (!empty($feesAssign->installment_due_date) && strtotime($feesAssign->installment_due_date) !== false) {
                                    $dueDateStr = date('d-M-Y', strtotime($feesAssign->installment_due_date));
                                }

                                $scheduledAmt = (float) ($feesAssign->fees_group_amount ?? ($item['paid_amount'] ?? ($item->paid_amount ?? 0)));
                                $fineAmt = (float) ($item['installment_fine'] ?? ($item->installment_fine ?? 0));
                                $paidAmt = (float) ($item['total_amount'] ?? ($item->total_amount ?? ($scheduledAmt + $fineAmt)));

                                $calcTotalFee += $scheduledAmt;
                                $calcTotalFine += $fineAmt;
                                $calcTotalPaid += $paidAmt;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $sno++ }}</td>
                                <td><strong>{{ $headName }}</strong></td>
                                <td class="text-center">{{ $dueDateStr ?: '-' }}</td>
                                <td class="text-right">{{ number_format($scheduledAmt, 2) }}</td>
                                <td class="text-right">{{ $fineAmt > 0 ? number_format($fineAmt, 2) : '-' }}</td>
                                <td class="text-right"><strong>{{ number_format($paidAmt, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Words Bar -->
                <div class="words-bar">
                    <span class="words-lbl">Amount in Words:</span>
                    <span class="words-val">{{ numToWordsINR($calcTotalPaid) }}</span>
                </div>

                <!-- Financial Summary & Payment Mode Breakdown -->
                <div class="summary-box">
                    @if($showPaymentDetails)
                        <div class="payment-info-card">
                            <div class="payment-info-title">Transaction & Payment Details</div>
                            <div><strong>Payment Mode:</strong> {{ $paymentModeName }}</div>
                            @if(!empty($txnId) && $txnId !== '-')
                                <div><strong>Txn / Ref ID:</strong> {{ $txnId }}</div>
                            @endif
                            @if(!empty($bankName) && $bankName !== '-')
                                <div><strong>Bank Name:</strong> {{ $bankName }}</div>
                            @endif
                            @if(!empty($chequeNumber) && $chequeNumber !== '-')
                                <div><strong>Cheque / DD No:</strong> {{ $chequeNumber }}</div>
                            @endif
                            @if(!empty($remarks))
                                <div><strong>Remarks:</strong> {{ $remarks }}</div>
                            @endif
                        </div>
                    @else
                        <div></div>
                    @endif

                    <div class="total-amount-card">
                        @if($calcTotalFine > 0)
                            <div class="total-amount-row">
                                <span>Late Fine:</span>
                                <span>₹ {{ number_format($calcTotalFine, 2) }}</span>
                            </div>
                        @endif
                        <div class="total-amount-row grand-total">
                            <span>Total Paid:</span>
                            <span>₹ {{ number_format($calcTotalPaid, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer, Terms & Signature -->
                <table class="receipt-footer-table">
                    <tr>
                        <td class="terms-cell">
                            <strong>Terms & Instructions:</strong><br>
                            {!! nl2br(e($termsConditions)) !!}
                        </td>
                        @if($showSignature)
                            <td class="signatures-cell">
                                <div class="sig-container">
                                    <div class="sig-block">
                                        <div class="sig-line"></div>
                                        <div class="sig-lbl">Depositor Signature</div>
                                    </div>
                                    <div class="sig-block">
                                        <div class="sig-line"></div>
                                        <div class="sig-lbl">Authorized Signatory</div>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

</body>
</html>