<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 3.5mm 5mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 8.6px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.25;
        }
        .page-wrap {
            width: 100%;
            margin: 0 auto;
        }

        /* Each Half-Page Receipt Card */
        .receipt-card {
            position: relative;
            width: 100%;
            height: 138.5mm;
            border: 1.5px solid #0b3c68;
            border-radius: 5px;
            background: #ffffff;
            overflow: hidden;
            page-break-inside: avoid;
        }

        /* Watermark */
        .watermark-box {
            position: absolute;
            top: 48mm;
            left: 50%;
            margin-left: -32mm;
            width: 64mm;
            height: 64mm;
            z-index: 0;
            opacity: 0.04;
            text-align: center;
        }
        .watermark-box img {
            max-width: 100%;
            max-height: 100%;
        }

        /* Top Geometric Header Banner */
        .banner-wrap {
            position: relative;
            width: 100%;
            height: 22.5mm;
            background: #0f172a;
            overflow: hidden;
        }
        .banner-svg-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 22.5mm;
            z-index: 1;
        }
        .banner-table {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 21mm;
            border-collapse: collapse;
        }
        .banner-table td {
            vertical-align: middle;
            padding: 0;
        }
        .banner-left {
            width: 34%;
            padding-left: 14px !important;
        }
        .doc-title {
            font-size: 16.5px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.05;
        }
        .doc-sub {
            font-size: 7.2px;
            color: #bae6fd;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .copy-badge-row {
            margin-top: 3.5px;
        }
        .copy-pill {
            display: inline-block;
            background: #0ea5e9;
            color: #ffffff;
            font-size: 7.2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 2px 7px;
            border-radius: 2px;
        }
        .paid-pill {
            display: inline-block;
            background: #10b981;
            color: #ffffff;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 2px 6px;
            border-radius: 2px;
            margin-left: 3px;
        }
        .banner-right {
            width: 66%;
            padding-right: 12px !important;
            padding-left: 34px !important;
        }
        .brand-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-tbl td {
            vertical-align: middle;
            padding: 0;
        }
        .inst-name {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 2px 0;
            line-height: 1.15;
            text-align: right;
        }
        .inst-addr {
            font-size: 7.5px;
            color: #cbd5e1;
            margin: 0;
            line-height: 1.25;
            text-align: right;
        }
        .logo-frame {
            width: 44px;
            height: 44px;
            background: #ffffff;
            border: 1.5px solid #38bdf8;
            border-radius: 5px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            margin-left: auto;
        }
        .logo-frame img {
            max-width: 40px;
            max-height: 40px;
            display: block;
            margin: 1px auto;
        }
        .logo-initial {
            font-size: 17px;
            font-weight: bold;
            color: #035a8c;
            line-height: 40px;
        }

        /* Body Area */
        .receipt-body {
            position: relative;
            z-index: 2;
            padding: 6px 12px 0 12px;
        }

        /* Student & Receipt Info Cards */
        .info-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5.5px;
        }
        .info-grid-table > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .student-info-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #035a8c;
            border-radius: 3px;
            padding: 4.5px 8px;
            height: 19.5mm;
        }
        .billed-tag {
            font-size: 7px;
            font-weight: bold;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 1px;
        }
        .student-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }
        .student-meta-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .student-meta-tbl td {
            padding: 0.8px 0;
            font-size: 7.9px;
            color: #475569;
        }
        .student-meta-tbl b {
            color: #0f172a;
        }

        .invoice-meta-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: 3.5px solid #0ea5e9;
            border-radius: 3px;
            padding: 4.5px 8px;
            height: 19.5mm;
        }
        .meta-kv-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-kv-tbl td {
            padding: 1.1px 0;
            font-size: 8px;
        }
        .meta-lbl {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.2px;
            width: 38%;
        }
        .meta-sep {
            width: 4%;
            text-align: center;
            color: #94a3b8;
            font-weight: bold;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
            text-align: right;
            width: 58%;
        }
        .meta-val-highlight {
            color: #035a8c;
            font-weight: bold;
            text-align: right;
            width: 58%;
        }

        /* Reserved Space Fee Items Table */
        .items-container {
            width: 100%;
            height: 49.5mm;
            overflow: hidden;
            margin-bottom: 5px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #035a8c;
            color: #ffffff;
            font-size: 7.8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4.5px 7px;
            border-bottom: 1.5px solid #0ea5e9;
            border-right: 1px solid #096da8;
        }
        .items-table th:last-child {
            border-right: none;
        }
        .items-table td {
            padding: 3.2px 7px;
            font-size: 8.2px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            height: 5.5mm;
            vertical-align: middle;
        }
        .items-table td:last-child {
            border-right: none;
        }
        .items-table tr.row-even td {
            background: #f1f5f9;
        }
        .items-table tr.row-odd td {
            background: #ffffff;
        }
        .items-table.compact-mode td {
            padding: 1.8px 6px;
            font-size: 7.5px;
            height: 4.2mm;
        }
        .sno-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            font-size: 7.5px;
            padding: 1px 4px;
            border-radius: 2px;
        }
        .sno-empty {
            color: #cbd5e1;
            font-size: 7.5px;
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

        /* Bottom Section: Payment Info & Totals */
        .bottom-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .bottom-grid > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .pay-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 4px 8px;
            margin-bottom: 4px;
        }
        .sec-title {
            font-size: 7.6px;
            font-weight: bold;
            color: #035a8c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1.5px;
        }
        .pay-kv-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .pay-kv-tbl td {
            padding: 1px 0;
            font-size: 7.8px;
        }
        .pay-lbl {
            width: 26%;
            color: #64748b;
            font-weight: bold;
        }
        .pay-val {
            width: 74%;
            color: #0f172a;
            font-weight: bold;
        }
        .words-banner {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 3px solid #0284c7;
            border-radius: 2px;
            padding: 2.5px 6px;
            margin-top: 2.5px;
            font-size: 7.7px;
            color: #0c4a6e;
        }
        .terms-block {
            font-size: 7px;
            color: #64748b;
            line-height: 1.25;
            padding-left: 2px;
        }

        /* Totals Box on Right */
        .totals-wrap {
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 2.5px 7px;
            font-size: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .tot-lbl {
            text-align: left;
            color: #475569;
            font-weight: bold;
            background: #f8fafc;
            width: 52%;
        }
        .tot-val {
            text-align: right;
            color: #0f172a;
            font-weight: bold;
            background: #ffffff;
            width: 48%;
        }
        .grand-total-row td {
            background: #035a8c !important;
            color: #ffffff !important;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 7px;
            border-bottom: none;
        }

        .signature-area {
            text-align: center;
            margin-top: 7px;
        }
        .sign-line {
            border-top: 1px solid #334155;
            width: 130px;
            margin: 12px auto 1px auto;
            padding-top: 2px;
            font-size: 7.8px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .sign-sub {
            font-size: 6.8px;
            color: #64748b;
            font-style: italic;
        }

        /* Bottom Geometric Footer Bar */
        .footer-bar-wrap {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 7.2mm;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .footer-contact-tbl {
            position: relative;
            z-index: 2;
            width: 100%;
            border-collapse: collapse;
            height: 4.2mm;
        }
        .footer-contact-tbl td {
            vertical-align: middle;
            padding: 0 10px;
            font-size: 7.3px;
            color: #334155;
            font-weight: bold;
        }
        .footer-svg-strip {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3mm;
            z-index: 1;
        }

        /* Middle Cut Here Dashed Line */
        .cut-separator {
            width: 100%;
            height: 6mm;
            page-break-inside: avoid;
        }
        .cut-separator-table {
            width: 100%;
            border-collapse: collapse;
            padding-top: 1.2mm;
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
            border: 1px dashed #035a8c;
            color: #035a8c;
            font-size: 7.2px;
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
    $feeDate = !empty($rawDate) ? date('d M, Y', strtotime($rawDate)) : '-';
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

    // Prepare reserved rows
    $rawItems = $detailsList->values();
    $itemCount = $rawItems->count();
    $isCompactTable = $itemCount > 5;
    $targetRowSlots = 5;

    // SVG data URIs for crisp geometric invoice header & footer
    $headerSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 92" preserveAspectRatio="none">'
        . '<rect width="800" height="86" fill="#0f172a"/>'
        . '<polygon points="0,0 262,0 302,86 0,86" fill="#035a8c"/>'
        . '<polygon points="270,0 276,0 316,86 310,86" fill="#ffffff"/>'
        . '<polygon points="282,0 308,0 348,86 322,86" fill="#0ea5e9"/>'
        . '<polygon points="315,72 338,72 345,86 322,86" fill="#0284c7"/>'
        . '<rect x="0" y="86" width="310" height="6" fill="#0284c7"/>'
        . '<rect x="310" y="86" width="490" height="6" fill="#0ea5e9"/>'
        . '</svg>';
    $headerSvgUri = 'data:image/svg+xml;base64,' . base64_encode($headerSvg);

    $footerSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 14" preserveAspectRatio="none">'
        . '<rect width="800" height="14" fill="#0f172a"/>'
        . '<polygon points="460,14 472,0 800,0 800,14" fill="#035a8c"/>'
        . '<polygon points="444,14 456,0 463,0 451,14" fill="#ffffff"/>'
        . '<polygon points="428,14 440,0 447,0 435,14" fill="#0ea5e9"/>'
        . '</svg>';
    $footerSvgUri = 'data:image/svg+xml;base64,' . base64_encode($footerSvg);

    $copies = ['OFFICE COPY', 'STUDENT COPY'];
@endphp
<body>
    <div class="page-wrap">
        @foreach($copies as $copyIdx => $copyTitle)
            <div class="receipt-card">
                @if($logoBase64)
                    <div class="watermark-box">
                        <img src="{{ $logoBase64 }}" alt="">
                    </div>
                @endif

                {{-- Top Geometric Header Banner --}}
                <div class="banner-wrap">
                    <img src="{{ $headerSvgUri }}" class="banner-svg-bg" alt="">
                    <table class="banner-table">
                        <tr>
                            <td class="banner-left">
                                <div class="doc-title">FEE RECEIPT</div>
                                <div class="doc-sub">Official Payment Voucher</div>
                                <div class="copy-badge-row">
                                    <span class="copy-pill">{{ $copyTitle }}</span>
                                    <span class="paid-pill">&#10003; PAID</span>
                                </div>
                            </td>
                            <td class="banner-right">
                                <table class="brand-tbl">
                                    <tr>
                                        <td style="padding-right: 10px;">
                                            <div class="inst-name">{{ $getSetting['name'] ?? 'Educational Institute' }}</div>
                                            <div class="inst-addr">{{ Str::limit($getSetting['address'] ?? '', 95) }}</div>
                                        </td>
                                        <td style="width: 46px;">
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
                    {{-- Student Info Card (Left) & Receipt Meta Card (Right) --}}
                    <table class="info-grid-table">
                        <tr>
                            <td style="width: 54%; padding-right: 6px;">
                                <div class="student-info-card">
                                    <div class="billed-tag">STUDENT DETAILS :</div>
                                    <div class="student-title">{{ $studentName }}</div>
                                    <table class="student-meta-tbl">
                                        <tr>
                                            <td style="width: 55%;"><b>Father's Name:</b> {{ Str::limit($fatherName, 24) }}</td>
                                            <td style="width: 45%;"><b>Phone:</b> {{ $mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>Class / Course:</b> {{ $classDisplay ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td style="width: 46%; padding-left: 4px;">
                                <div class="invoice-meta-card">
                                    <table class="meta-kv-tbl">
                                        <tr>
                                            <td class="meta-lbl">Receipt No</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-val-highlight">{{ $receiptNo }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-lbl">Receipt Date</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-val">{{ $feeDate }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-lbl">Admission No</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-val">{{ $admissionNo }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-lbl">Academic Session</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-val">{{ $sessionLabel }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>

                    {{-- Reserved Space Fee Items Table --}}
                    <div class="items-container">
                        <table class="items-table {{ $isCompactTable ? 'compact-mode' : '' }}">
                            <thead>
                                <tr>
                                    <th style="width: 9%;" class="text-center">S.NO</th>
                                    <th style="width: 43%;" class="text-left">PARTICULARS / FEE HEAD DESCRIPTION</th>
                                    <th style="width: 16%;" class="text-right">FEE AMOUNT</th>
                                    <th style="width: 15%;" class="text-right">DISC / FINE</th>
                                    <th style="width: 17%;" class="text-right">NET PAID</th>
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
                                        $discFineText = '₹0.00';
                                        if ($itemDisc > 0 && $itemFine > 0) {
                                            $discFineText = '-₹' . number_format($itemDisc, 0) . ' / +₹' . number_format($itemFine, 0);
                                        } elseif ($itemDisc > 0) {
                                            $discFineText = '-₹' . number_format($itemDisc, 2);
                                        } elseif ($itemFine > 0) {
                                            $discFineText = '+₹' . number_format($itemFine, 2);
                                        }
                                    @endphp
                                    <tr class="{{ $idx % 2 === 0 ? 'row-even' : 'row-odd' }}">
                                        <td class="text-center"><span class="sno-badge">{{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</span></td>
                                        <td class="fw-bold">{{ Str::limit($headName, 45) }}</td>
                                        <td class="text-right">₹{{ number_format($itemBase, 2) }}</td>
                                        <td class="text-right">{{ $discFineText }}</td>
                                        <td class="text-right fw-bold" style="color: #035a8c;">₹{{ number_format($itemPaid, 2) }}</td>
                                    </tr>
                                @empty
                                    @php $renderedRows = 1; @endphp
                                    <tr class="row-even">
                                        <td class="text-center"><span class="sno-badge">01</span></td>
                                        <td class="fw-bold">Academic / Course Fee ({{ $feeMonth }})</td>
                                        <td class="text-right">₹{{ number_format($subTotal, 2) }}</td>
                                        <td class="text-right">{{ $totalDiscount > 0 ? '-₹' . number_format($totalDiscount, 2) : '₹0.00' }}</td>
                                        <td class="text-right fw-bold" style="color: #035a8c;">₹{{ number_format($totalPaid, 2) }}</td>
                                    </tr>
                                @endforelse

                                {{-- Fill remaining reserved slots so table height stays fixed & balanced --}}
                                @for($slot = $renderedRows; $slot < $targetRowSlots; $slot++)
                                    <tr class="{{ $slot % 2 === 0 ? 'row-even' : 'row-odd' }}">
                                        <td class="text-center"><span class="sno-empty">{{ str_pad((string) ($slot + 1), 2, '0', STR_PAD_LEFT) }}</span></td>
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
                            <td style="width: 58%; padding-right: 10px;">
                                <div class="pay-card">
                                    <div class="sec-title">Payment Information</div>
                                    <table class="pay-kv-tbl">
                                        <tr>
                                            <td class="pay-lbl">Payment Mode</td>
                                            <td class="pay-val">: {{ strtoupper($paymentMode) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pay-lbl">Trans. Ref / ID</td>
                                            <td class="pay-val">: {{ !empty($transactionId) ? $transactionId : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                    <div class="words-banner">
                                        <b>Amount in Words:</b> {{ $amountInWords }}
                                    </div>
                                </div>

                                <div class="terms-block">
                                    <b style="color: #334155; text-transform: uppercase;">Terms &amp; Conditions:</b>
                                    1. Fees once paid are neither refundable nor transferable.
                                    2. Subject to realization of cheque / electronic payment.
                                    @if(!empty($invoiceObj->remark))
                                        <br><b style="color: #035a8c;">Remark:</b> {{ Str::limit($invoiceObj->remark, 68) }}
                                    @endif
                                </div>
                            </td>
                            <td style="width: 42%;">
                                <div class="totals-wrap">
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
                                            <td class="tot-lbl" style="background: #035a8c; color: #ffffff;">Grand Total</td>
                                            <td class="tot-val" style="background: #035a8c; color: #ffffff;">₹{{ number_format($totalPaid, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="signature-area">
                                    <div class="sign-line">Authorized Signatory</div>
                                    <div class="sign-sub">Computer Generated Official Receipt</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Bottom Geometric Footer Bar --}}
                <div class="footer-bar-wrap">
                    <table class="footer-contact-tbl">
                        <tr>
                            <td style="width: 55%;">
                                &#9742; {{ $getSetting['mobile'] ?? '-' }}
                                @if(!empty($getSetting['gmail']))
                                    &nbsp;&nbsp;|&nbsp;&nbsp; &#9993; {{ $getSetting['gmail'] }}
                                @endif
                            </td>
                            <td style="width: 45%; text-align: right; color: #ffffff;">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
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
