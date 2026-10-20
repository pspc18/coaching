<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 4mm 6mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 8.8px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.25;
        }
        .page-wrap {
            width: 100%;
            margin: 0 auto;
        }

        /* Each half-page receipt card */
        .receipt-card {
            position: relative;
            width: 100%;
            height: 139.5mm;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            overflow: hidden;
            page-break-inside: avoid;
        }

        /* Top Geometric Banner */
        .banner-wrap {
            position: relative;
            width: 100%;
            height: 21mm;
            background: #1a2433;
            overflow: hidden;
            border-bottom: 2.5px solid #0ea5e9;
        }
        .banner-svg-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 21mm;
            z-index: 1;
        }
        .banner-content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 21mm;
            border-collapse: collapse;
        }
        .banner-content td {
            vertical-align: middle;
            padding: 0;
        }
        .banner-left {
            width: 35%;
            padding-left: 14px !important;
        }
        .doc-main-title {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.05;
        }
        .copy-pill {
            display: inline-block;
            margin-top: 3px;
            background: #0ea5e9;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 2px 7px;
            border-radius: 2px;
        }
        .banner-right {
            width: 65%;
            padding-right: 12px !important;
            padding-left: 38px !important;
            text-align: right;
        }
        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-table td {
            vertical-align: middle;
            padding: 0;
        }
        .inst-title {
            font-size: 12.5px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 2px 0;
            line-height: 1.15;
        }
        .inst-tagline {
            font-size: 7.6px;
            color: #cbd5e1;
            margin: 0;
            line-height: 1.25;
        }
        .logo-frame {
            width: 42px;
            height: 42px;
            background: #ffffff;
            border: 1.5px solid #0ea5e9;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            margin-left: auto;
        }
        .logo-frame img {
            max-width: 38px;
            max-height: 38px;
            display: block;
            margin: 1px auto;
        }
        .logo-initial {
            font-size: 16px;
            font-weight: bold;
            color: #035a8c;
            line-height: 38px;
        }

        /* Body Container */
        .receipt-body {
            padding: 6px 12px 0 12px;
        }

        /* Student & Invoice Meta Row */
        .info-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .info-section td {
            vertical-align: top;
            padding: 0;
        }
        .receipt-to-tag {
            font-size: 7.5px;
            font-weight: bold;
            color: #035a8c;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 1px;
        }
        .student-big-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }
        .student-sub-line {
            font-size: 8.2px;
            color: #475569;
            line-height: 1.35;
        }
        .student-sub-line b {
            color: #1e293b;
        }
        .meta-kv-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-kv-table td {
            padding: 1.2px 0;
            font-size: 8.2px;
        }
        .meta-kv-lbl {
            color: #475569;
            font-weight: bold;
            text-align: left;
            width: 42%;
        }
        .meta-kv-colon {
            width: 5%;
            text-align: center;
            color: #64748b;
            font-weight: bold;
        }
        .meta-kv-val {
            color: #0f172a;
            font-weight: bold;
            text-align: right;
            width: 53%;
        }

        /* Reserved Space Fee Items Table */
        .items-container {
            width: 100%;
            height: 51mm;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #035a8c;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4.5px 6px;
            border: 1px solid #035a8c;
        }
        .items-table td {
            padding: 3.5px 6px;
            font-size: 8.3px;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            height: 5.8mm;
            vertical-align: middle;
        }
        .items-table tr.row-even td {
            background: #f1f5f9;
        }
        .items-table tr.row-odd td {
            background: #ffffff;
        }
        .items-table.compact-mode td {
            padding: 2px 5px;
            font-size: 7.6px;
            height: 4.4mm;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .text-left {
            text-align: left !important;
        }
        .fw-bold {
            font-weight: bold;
        }

        /* Lower Summary & Payment Info Section */
        .bottom-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .bottom-grid td {
            vertical-align: top;
            padding: 0;
        }
        .section-heading {
            font-size: 8.3px;
            font-weight: bold;
            color: #035a8c;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .pay-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .pay-info-table td {
            padding: 1px 0;
            font-size: 8px;
        }
        .pay-lbl {
            width: 28%;
            color: #475569;
            font-weight: bold;
        }
        .pay-val {
            width: 72%;
            color: #0f172a;
            font-weight: bold;
        }
        .terms-box {
            font-size: 7.2px;
            color: #64748b;
            line-height: 1.25;
            margin-top: 2px;
        }

        /* Totals Stack on Right */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .totals-table td {
            padding: 2.2px 6px;
            font-size: 8.3px;
        }
        .tot-lbl {
            text-align: right;
            color: #334155;
            font-weight: bold;
            width: 58%;
        }
        .tot-val {
            text-align: right;
            color: #0f172a;
            font-weight: bold;
            width: 42%;
        }
        .grand-total-row td {
            background: #035a8c;
            color: #ffffff !important;
            font-size: 9.2px;
            font-weight: bold;
            padding: 4px 6px;
        }

        .signature-block {
            text-align: center;
            margin-top: 8px;
            padding-left: 20px;
        }
        .sign-line {
            border-top: 1px solid #475569;
            width: 125px;
            margin: 14px auto 2px auto;
            padding-top: 2px;
            font-size: 8px;
            font-weight: bold;
            color: #1e293b;
        }
        .thank-you-note {
            font-size: 7.2px;
            color: #64748b;
            font-style: italic;
        }

        /* Bottom Geometric Footer Bar */
        .footer-bar-wrap {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 7.5mm;
            background: #ffffff;
            overflow: hidden;
        }
        .footer-contact-bar {
            position: relative;
            z-index: 2;
            padding: 1px 12px 0 12px;
            font-size: 7.5px;
            color: #334155;
            font-weight: bold;
        }
        .footer-svg-strip {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3.5mm;
            z-index: 1;
        }

        /* Middle Cut Here Dashed Line */
        .cut-separator {
            width: 100%;
            height: 7.5mm;
            page-break-inside: avoid;
        }
        .cut-separator-table {
            width: 100%;
            border-collapse: collapse;
            padding-top: 1.8mm;
        }
        .cut-separator-table td {
            vertical-align: middle;
            padding: 0;
        }
        .dashed-rule {
            border-bottom: 1.2px dashed #64748b;
            height: 1px;
        }
        .cut-pill {
            display: inline-block;
            background: #f1f5f9;
            border: 1px dashed #64748b;
            color: #334155;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1px 9px;
            border-radius: 10px;
            white-space: nowrap;
        }
    </style>
</head>
@php
    $getSetting = Helper::getSetting();
    $account = Helper::getQRCode($getSetting->account_id ?? null);

    $isCollection = $data instanceof \Illuminate\Support\Collection;
    $detailsList = $isCollection ? $data : collect(is_object($data) ? [$data] : (is_array($data) ? [$data] : []));
    $firstDetail = $detailsList->first();
    $dataArr = is_array($data) ? $data : [];
    $invoiceObj = isset($invoice_data) && is_object($invoice_data) ? $invoice_data : null;
    $admissionObj = ($firstDetail && isset($firstDetail->Admission) && is_object($firstDetail->Admission)) ? $firstDetail->Admission : null;

    $receiptNo = ($invoiceObj->invoice_no ?? null)
        ?: ($invoiceObj->offline_receipt_no ?? null)
        ?: ($firstDetail->receipt_no ?? null)
        ?: ($dataArr['receipt_no'] ?? '-');

    $rawDate = ($invoiceObj->payment_date ?? null)
        ?: ($invoiceObj->date ?? null)
        ?: ($firstDetail->date ?? null)
        ?: ($dataArr['date'] ?? null);
    $feeDate = !empty($rawDate) ? date('d/m/Y', strtotime($rawDate)) : '-';
    $feeMonth = !empty($rawDate) ? \Carbon\Carbon::parse($rawDate)->format('F Y') : '-';

    $firstName = ($invoiceObj->first_name ?? null)
        ?: ($admissionObj->first_name ?? null)
        ?: ($firstDetail->first_name ?? null)
        ?: ($dataArr['Admission']['first_name'] ?? ($dataArr['first_name'] ?? ''));

    $lastName = ($invoiceObj->last_name ?? null)
        ?: ($admissionObj->last_name ?? null)
        ?: ($firstDetail->last_name ?? null)
        ?: ($dataArr['Admission']['last_name'] ?? ($dataArr['last_name'] ?? ''));

    $studentName = trim($firstName . ' ' . $lastName) ?: '-';

    $fatherName = ($invoiceObj->father_name ?? null)
        ?: ($admissionObj->father_name ?? null)
        ?: ($firstDetail->father_name ?? null)
        ?: ($dataArr['Admission']['father_name'] ?? ($dataArr['father_name'] ?? '-'));

    $admissionNo = ($invoiceObj->admissionNo ?? null)
        ?: ($admissionObj->admissionNo ?? null)
        ?: ($firstDetail->admissionNo ?? null)
        ?: ($dataArr['Admission']['admissionNo'] ?? '-');

    $className = ($invoiceObj->class_name ?? null)
        ?: (isset($admissionObj->ClassTypes) ? ($admissionObj->ClassTypes->name ?? null) : null)
        ?: ($dataArr['Admission']['ClassTypes']['name'] ?? '-');

    $sectionName = ($invoiceObj->section_name ?? null)
        ?: (isset($admissionObj->Section) ? ($admissionObj->Section->name ?? null) : null)
        ?: ($dataArr['Admission']['Section']['name'] ?? '');

    $classDisplay = trim($className . (!empty($sectionName) ? ' (' . $sectionName . ')' : ''));

    $mobile = ($invoiceObj->mobile ?? null)
        ?: ($admissionObj->mobile ?? null)
        ?: ($firstDetail->mobile ?? null)
        ?: ($dataArr['Admission']['mobile'] ?? ($dataArr['mobile'] ?? '-'));

    $paymentMode = ($invoiceObj->payment_mode ?? null)
        ?: ($firstDetail->payment_mode ?? null)
        ?: ($dataArr['payment_mode'] ?? 'Cash');

    $transactionId = ($invoiceObj->transaction_id ?? null)
        ?: ($firstDetail->transition_id ?? null)
        ?: '';

    $sessionFrom = $invoiceObj->from_year ?? null;
    $sessionTo = $invoiceObj->to_year ?? null;
    if (empty($sessionFrom) && !empty($invoiceObj->session_id)) {
        $sessRec = \App\Models\Sessions::find($invoiceObj->session_id);
        if ($sessRec) {
            $sessionFrom = $sessRec->from_year;
            $sessionTo = $sessRec->to_year;
        }
    }
    $sessionLabel = !empty($sessionFrom) ? ($sessionFrom . (!empty($sessionTo) ? '-' . $sessionTo : '')) : date('Y') . '-' . (date('y') + 1);

    $totalPaid = (float) (($invoiceObj->amount ?? null)
        ?? ($invoiceObj->total_amount ?? null)
        ?? ($isCollection ? $data->sum('paid_amount') ?: $data->sum('total_amount') : ($firstDetail->paid_amount ?? $firstDetail->total_amount ?? ($dataArr['total_amount'] ?? 0))));

    $totalDiscount = (float) (($invoiceObj->discount ?? null)
        ?? ($invoiceObj->discount_given ?? null)
        ?? ($isCollection ? $data->sum('discount') : ($firstDetail->discount ?? ($dataArr['discount'] ?? 0))));

    $totalFine = (float) (($invoiceObj->total_fine ?? null)
        ?? ($isCollection ? $data->sum('installment_fine') : ($firstDetail->installment_fine ?? 0)));

    $subTotal = $totalPaid + $totalDiscount - $totalFine;
    if ($subTotal < $totalPaid) {
        $subTotal = $totalPaid;
    }

    // Resolve Logo as Base64 if local file exists
    $logoBase64 = null;
    if (!empty($getSetting['left_logo'])) {
        $possibleLogoPaths = [
            public_path('schoolimage/setting/left_logo/' . $getSetting['left_logo']),
            rtrim((string) env('IMAGE_UPLOAD_PATH'), '/') . '/setting/left_logo/' . $getSetting['left_logo'],
        ];
        foreach ($possibleLogoPaths as $lPath) {
            if ($lPath && @is_file($lPath) && @is_readable($lPath)) {
                $ext = strtolower(pathinfo($lPath, PATHINFO_EXTENSION) ?: 'png');
                $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png';
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($lPath));
                break;
            }
        }
    }

    // Number to Indian Words Helper
    if (!function_exists('convertNumberToIndianWords')) {
        function convertNumberToIndianWords($number) {
            $no = (int) round($number);
            if ($no === 0) {
                return 'Zero Rupees Only';
            }
            $words = [
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
                6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
                11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
                15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
                50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
            ];
            $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
            $i = 0;
            $str = [];
            $digits_length = strlen((string) $no);
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? '' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str[] = ($number < 21)
                        ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred
                        : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                } else {
                    $str[] = null;
                }
            }
            $Rupees = implode('', array_reverse($str));
            return trim(preg_replace('/\s+/', ' ', $Rupees)) . ' Rupees Only';
        }
    }

    $amountInWords = convertNumberToIndianWords($totalPaid);

    // Prepare reserved rows (max 6 visible rows in reserved space; if <= 6 pad to 5 rows for uniform table height)
    $rawItems = $detailsList->values();
    $itemCount = $rawItems->count();
    $isCompactTable = $itemCount > 5;
    $targetRowSlots = 5;

    // SVG data URIs for crisp geometric invoice header & footer
    $headerSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 90" preserveAspectRatio="none">'
        . '<rect width="800" height="90" fill="#1a2433"/>'
        . '<polygon points="0,0 265,0 305,90 0,90" fill="#035a8c"/>'
        . '<polygon points="273,0 280,0 320,90 313,90" fill="#ffffff"/>'
        . '<polygon points="287,0 315,0 355,90 327,90" fill="#0ea5e9"/>'
        . '<rect x="318" y="82" width="482" height="8" fill="#0ea5e9"/>'
        . '</svg>';
    $headerSvgUri = 'data:image/svg+xml;base64,' . base64_encode($headerSvg);

    $footerSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 16" preserveAspectRatio="none">'
        . '<rect width="800" height="16" fill="#1a2433"/>'
        . '<polygon points="460,16 474,0 800,0 800,16" fill="#035a8c"/>'
        . '<polygon points="442,16 456,0 464,0 450,16" fill="#ffffff"/>'
        . '<polygon points="424,16 438,0 446,0 432,16" fill="#0ea5e9"/>'
        . '</svg>';
    $footerSvgUri = 'data:image/svg+xml;base64,' . base64_encode($footerSvg);

    $copies = ['OFFICE COPY', 'STUDENT COPY'];
@endphp
<body>
    <div class="page-wrap">
        @foreach($copies as $copyIdx => $copyTitle)
            <div class="receipt-card">
                {{-- Top Geometric Header Banner --}}
                <div class="banner-wrap">
                    <img src="{{ $headerSvgUri }}" class="banner-svg-bg" alt="">
                    <table class="banner-content">
                        <tr>
                            <td class="banner-left">
                                <div class="doc-main-title">FEE RECEIPT</div>
                                <span class="copy-pill">{{ $copyTitle }}</span>
                            </td>
                            <td class="banner-right">
                                <table class="brand-table">
                                    <tr>
                                        <td style="text-align: right; padding-right: 9px;">
                                            <div class="inst-title">{{ $getSetting['name'] ?? 'Educational Institute' }}</div>
                                            <div class="inst-tagline">{{ Str::limit($getSetting['address'] ?? '', 95) }}</div>
                                        </td>
                                        <td style="width: 44px;">
                                            <div class="logo-frame">
                                                @if($logoBase64)
                                                    <img src="{{ $logoBase64 }}" alt="Logo">
                                                @else
                                                    <div class="logo-initial">{{ strtoupper(substr($getSetting['name'] ?? 'A', 0, 1)) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Main Body --}}
                <div class="receipt-body">
                    {{-- Student Info (Left) & Receipt Meta (Right) --}}
                    <table class="info-section">
                        <tr>
                            <td style="width: 56%; padding-right: 10px;">
                                <div class="receipt-to-tag">RECEIPT TO :</div>
                                <div class="student-big-name">{{ $studentName }}</div>
                                <div class="student-sub-line">
                                    <b>Father's Name:</b> {{ $fatherName }}<br>
                                    <b>Class / Course:</b> {{ $classDisplay ?: '-' }} &nbsp;|&nbsp; <b>Phone:</b> {{ $mobile }}
                                </div>
                            </td>
                            <td style="width: 44%;">
                                <table class="meta-kv-table">
                                    <tr>
                                        <td class="meta-kv-lbl">Receipt No</td>
                                        <td class="meta-kv-colon">:</td>
                                        <td class="meta-kv-val">{{ $receiptNo }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-kv-lbl">Receipt Date</td>
                                        <td class="meta-kv-colon">:</td>
                                        <td class="meta-kv-val">{{ $feeDate }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-kv-lbl">Admission No</td>
                                        <td class="meta-kv-colon">:</td>
                                        <td class="meta-kv-val">{{ $admissionNo }}</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-kv-lbl">Academic Session</td>
                                        <td class="meta-kv-colon">:</td>
                                        <td class="meta-kv-val">{{ $sessionLabel }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- Reserved Space Fee Items Table --}}
                    <div class="items-container">
                        <table class="items-table {{ $isCompactTable ? 'compact-mode' : '' }}">
                            <thead>
                                <tr>
                                    <th style="width: 8%;" class="text-center">NO</th>
                                    <th style="width: 44%;" class="text-left">DESCRIPTION / FEE HEAD</th>
                                    <th style="width: 16%;" class="text-right">FEE AMT</th>
                                    <th style="width: 15%;" class="text-right">DISC / FINE</th>
                                    <th style="width: 17%;" class="text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $renderedRows = 0; @endphp
                                @forelse($rawItems->take(8) as $idx => $item)
                                    @php
                                        $renderedRows++;
                                        $headName = is_object($item)
                                            ? ($item->fees_group_name ?? ($item->FeesCollect->name ?? 'Tuition / Academic Fee'))
                                            : ($item['fees_group_name'] ?? 'Tuition / Academic Fee');
                                        $itemDisc = (float) (is_object($item) ? ($item->discount ?? 0) : ($item['discount'] ?? 0));
                                        $itemFine = (float) (is_object($item) ? ($item->installment_fine ?? 0) : ($item['installment_fine'] ?? 0));
                                        $itemPaid = (float) (is_object($item) ? ($item->paid_amount ?? $item->total_amount ?? 0) : ($item['paid_amount'] ?? $item['total_amount'] ?? 0));
                                        $itemBase = $itemPaid + $itemDisc - $itemFine;
                                        if ($itemBase < $itemPaid) {
                                            $itemBase = $itemPaid;
                                        }
                                        $discFineText = '0.00';
                                        if ($itemDisc > 0 && $itemFine > 0) {
                                            $discFineText = '-' . number_format($itemDisc, 0) . ' / +' . number_format($itemFine, 0);
                                        } elseif ($itemDisc > 0) {
                                            $discFineText = '-' . number_format($itemDisc, 2);
                                        } elseif ($itemFine > 0) {
                                            $discFineText = '+' . number_format($itemFine, 2);
                                        }
                                    @endphp
                                    <tr class="{{ $idx % 2 === 0 ? 'row-even' : 'row-odd' }}">
                                        <td class="text-center fw-bold">{{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}.</td>
                                        <td class="fw-bold">{{ Str::limit($headName, 45) }}</td>
                                        <td class="text-right">₹{{ number_format($itemBase, 2) }}</td>
                                        <td class="text-right">{{ $discFineText }}</td>
                                        <td class="text-right fw-bold">₹{{ number_format($itemPaid, 2) }}</td>
                                    </tr>
                                @empty
                                    @php $renderedRows = 1; @endphp
                                    <tr class="row-even">
                                        <td class="text-center fw-bold">01.</td>
                                        <td class="fw-bold">Academic / Course Fee ({{ $feeMonth }})</td>
                                        <td class="text-right">₹{{ number_format($subTotal, 2) }}</td>
                                        <td class="text-right">{{ $totalDiscount > 0 ? '-' . number_format($totalDiscount, 2) : '0.00' }}</td>
                                        <td class="text-right fw-bold">₹{{ number_format($totalPaid, 2) }}</td>
                                    </tr>
                                @endforelse

                                {{-- Fill remaining reserved slots so table height stays fixed & balanced --}}
                                @for($slot = $renderedRows; $slot < $targetRowSlots; $slot++)
                                    <tr class="{{ $slot % 2 === 0 ? 'row-even' : 'row-odd' }}">
                                        <td class="text-center" style="color: #cbd5e1;">{{ str_pad((string) ($slot + 1), 2, '0', STR_PAD_LEFT) }}.</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    {{-- Bottom Section: Payment Info + Terms (Left) | SubTotal + Grand Total + Signature (Right) --}}
                    <table class="bottom-grid">
                        <tr>
                            <td style="width: 58%; padding-right: 12px;">
                                <div class="section-heading">Payment Info :</div>
                                <table class="pay-info-table">
                                    <tr>
                                        <td class="pay-lbl">Payment Mode</td>
                                        <td class="pay-val">: {{ strtoupper($paymentMode) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pay-lbl">Trans. Ref / ID</td>
                                        <td class="pay-val">: {{ !empty($transactionId) ? $transactionId : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pay-lbl">In Words</td>
                                        <td class="pay-val">: {{ $amountInWords }}</td>
                                    </tr>
                                </table>

                                <div class="section-heading" style="margin-top: 3px;">Terms &amp; Conditions :</div>
                                <div class="terms-box">
                                    1. Fees once paid are neither refundable nor transferable.<br>
                                    2. Subject to realization of cheque / online transaction settlement.
                                    @if(!empty($invoiceObj->remark))
                                        <br><b>Remark:</b> {{ Str::limit($invoiceObj->remark, 65) }}
                                    @endif
                                </div>
                            </td>
                            <td style="width: 42%;">
                                <table class="totals-table">
                                    <tr>
                                        <td class="tot-lbl">Sub Total</td>
                                        <td class="tot-val">₹{{ number_format($subTotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="tot-lbl">Discount / Fine</td>
                                        <td class="tot-val">
                                            @if($totalDiscount > 0)
                                                -₹{{ number_format($totalDiscount, 2) }}
                                            @elseif($totalFine > 0)
                                                +₹{{ number_format($totalFine, 2) }}
                                            @else
                                                ₹0.00
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="grand-total-row">
                                        <td class="tot-lbl" style="color: #ffffff;">Grand Total</td>
                                        <td class="tot-val" style="color: #ffffff;">₹{{ number_format($totalPaid, 2) }}</td>
                                    </tr>
                                </table>

                                <div class="signature-block">
                                    <div class="sign-line">Authorized Signature</div>
                                    <div class="thank-you-note">Thank you for your payment</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Bottom Geometric Footer Bar --}}
                <div class="footer-bar-wrap">
                    <div class="footer-contact-bar">
                        <span>&#9742; {{ $getSetting['mobile'] ?? '-' }}</span>
                        @if(!empty($getSetting['gmail']))
                            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <span>&#9993; {{ $getSetting['gmail'] }}</span>
                        @endif
                    </div>
                    <img src="{{ $footerSvgUri }}" class="footer-svg-strip" alt="">
                </div>
            </div>

            @if($copyIdx === 0)
                <div class="cut-separator">
                    <table class="cut-separator-table">
                        <tr>
                            <td style="width: 39%;"><div class="dashed-rule"></div></td>
                            <td style="width: 22%;" class="text-center">
                                <span class="cut-pill">&#9986; Cut From Here</span>
                            </td>
                            <td style="width: 39%;"><div class="dashed-rule"></div></td>
                        </tr>
                    </table>
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>
