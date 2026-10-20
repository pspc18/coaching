<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm 8mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9.8px;
            color: #111827;
            background: #ffffff;
            line-height: 1.25;
        }
        .page-wrap {
            width: 100%;
            margin: 0 auto;
        }

        /* Half-Page Receipt Block - Fixed height so 2 copies + cut line always stay on 1 A4 page */
        .receipt-half {
            position: relative;
            width: 100%;
            height: 136mm;
            padding: 1.5mm 1mm 1mm 1mm;
            page-break-inside: avoid;
            overflow: hidden;
        }

        /* Full-Page A4 Receipt Block - Utilizes full A4 portrait page on Page 1 without page-break overflow */
        .receipt-full {
            position: relative;
            width: 100%;
            height: 266mm;
            padding: 3mm 2mm 2mm 2mm;
            page-break-before: avoid;
            page-break-after: avoid;
            overflow: hidden;
        }
        .receipt-full .org-header-table {
            border-bottom: 2.2px solid #8b2635;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .receipt-full .org-logo-cell {
            width: 66px;
            padding-right: 12px !important;
        }
        .receipt-full .org-logo-box {
            width: 58px;
            height: 58px;
        }
        .receipt-full .org-logo-box img {
            max-width: 54px;
            max-height: 54px;
        }
        .receipt-full .org-logo-initial {
            font-size: 24px;
            line-height: 56px;
        }
        .receipt-full .org-title {
            font-size: 20px;
            margin-bottom: 3px;
        }
        .receipt-full .org-address {
            font-size: 9.8px;
            margin-bottom: 2px;
        }
        .receipt-full .org-contact {
            font-size: 9.2px;
        }
        .receipt-full .copy-box {
            padding: 4px 14px;
            font-size: 10.5px;
        }
        .receipt-full .session-sub {
            font-size: 8.8px;
            margin-top: 4px;
        }
        .receipt-full .meta-lines-table {
            margin-bottom: 10px;
        }
        .receipt-full .meta-lines-table td {
            padding: 5.5px 0;
            font-size: 10.8px;
        }
        .receipt-full .receipt-watermark {
            top: 98mm;
            opacity: 0.11;
        }
        .receipt-full .receipt-watermark img {
            max-width: 135mm;
            max-height: 58mm;
        }
        .receipt-full .fee-table th {
            font-size: 9.8px;
            padding: 6px 9px;
        }
        .receipt-full .fee-table tbody td {
            padding: 3.8px 9px;
            font-size: 10.2px;
            height: 6.0mm;
        }
        .receipt-full .fee-table.compact-table tbody td {
            padding: 2.8px 8px;
            font-size: 9.4px;
            height: 4.8mm;
        }
        .receipt-full .fee-table tfoot td {
            padding: 6px 9px;
            font-size: 11.2px;
        }
        .receipt-full .words-inline {
            font-size: 9.5px;
        }
        .receipt-full .payment-meta-row {
            margin-top: 6px;
            font-size: 9.8px;
        }
        .receipt-full .terms-box {
            margin-top: 8px;
            font-size: 8.8px;
            line-height: 1.35;
        }
        .receipt-full .sign-footer-wrap {
            bottom: 3mm;
        }
        .receipt-full .sign-line-box {
            width: 190px;
            font-size: 10.2px;
            padding-top: 4.5px;
        }
        .receipt-full .sign-stamp-box {
            width: 190px;
        }
        .receipt-full .seal-sign-img {
            max-width: 135px;
            max-height: 18mm;
        }

        /* Organization Header */
        .org-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.8px solid #8b2635;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .org-header-table td {
            vertical-align: middle;
            padding: 0 0 5px 0;
        }
        .org-logo-cell {
            width: 52px;
            padding-right: 10px !important;
        }
        .org-logo-box {
            width: 46px;
            height: 46px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            background: #ffffff;
        }
        .org-logo-box img {
            max-width: 42px;
            max-height: 42px;
            display: block;
            margin: 1px auto;
        }
        .org-logo-initial {
            font-size: 19px;
            font-weight: bold;
            color: #8b2635;
            line-height: 44px;
        }
        .org-title {
            font-size: 16.5px;
            font-weight: bold;
            color: #8b2635;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 2px 0;
            line-height: 1.15;
        }
        .org-address {
            font-size: 8.5px;
            color: #374151;
            margin: 0 0 1px 0;
            line-height: 1.2;
        }
        .org-contact {
            font-size: 8.2px;
            color: #4b5563;
            margin: 0;
        }
        .copy-badge-cell {
            width: 130px;
            text-align: right;
            vertical-align: top !important;
        }
        .copy-box {
            display: inline-block;
            border: 1.3px solid #1f2937;
            padding: 3px 11px;
            font-size: 9px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: #ffffff;
        }
        .session-sub {
            font-size: 7.8px;
            color: #4b5563;
            font-weight: bold;
            margin-top: 3px;
        }

        /* Dotted Student Info Lines */
        .meta-lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .meta-lines-table td {
            padding: 3.5px 0;
            vertical-align: bottom;
            font-size: 9.5px;
        }
        .field-wrap {
            width: 100%;
            border-collapse: collapse;
        }
        .field-wrap td {
            padding: 0;
            vertical-align: bottom;
        }
        .field-label {
            white-space: nowrap;
            color: #111827;
            padding-right: 6px !important;
            width: 1%;
            font-weight: normal;
        }
        .field-value {
            border-bottom: 1.2px dotted #374151;
            color: #111827;
            font-weight: bold;
            padding: 0 4px 1px 4px !important;
            width: 99%;
        }

        /* Receipt Center Watermark */
        .receipt-watermark {
            position: absolute;
            top: 55mm;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 0;
            opacity: 0.11;
        }
        .receipt-watermark img {
            max-width: 95mm;
            max-height: 36mm;
        }

        /* Dynamic Tabular Fee Heads Section with Reserved Rows */
        .fee-table {
            position: relative;
            z-index: 2;
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 5px;
        }
        .fee-table th {
            background: #f3f4f6;
            color: #111827;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4px 7px;
            border: 1px solid #374151;
        }
        .fee-table tbody td {
            padding: 2.6px 7px;
            font-size: 9px;
            color: #111827;
            border-left: 1px solid #374151;
            border-right: 1px solid #374151;
            border-top: none;
            border-bottom: none;
            height: 4.4mm;
            vertical-align: middle;
        }
        .fee-table.compact-table tbody td {
            padding: 1.8px 6px;
            font-size: 8.2px;
            height: 3.6mm;
        }
        .fee-table tfoot td {
            background: #f9fafb;
            border: 1px solid #374151;
            padding: 4px 7px;
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            height: auto;
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
        .words-inline {
            font-size: 8.2px;
            font-weight: normal;
            color: #4b5563;
            margin-left: 6px;
            font-style: italic;
        }

        /* Payment Mode Sub-row */
        .payment-meta-row {
            margin-top: 3px;
            font-size: 8.5px;
            color: #374151;
        }
        .payment-meta-row b {
            color: #111827;
        }
        .terms-box {
            margin-top: 2px;
            font-size: 7.4px;
            color: #4b5563;
            line-height: 1.2;
        }

        /* Bottom Signature & Seal Area (Positioned at bottom of each half with generous gap above) */
        .sign-footer-wrap {
            position: absolute;
            bottom: 2mm;
            left: 1mm;
            right: 1mm;
            width: 99.5%;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sign-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .sign-line-box {
            width: 165px;
            border-top: 1.2px solid #1f2937;
            padding-top: 3.5px;
            text-align: center;
            font-size: 9.2px;
            color: #111827;
        }
        .sign-right-align {
            margin-left: auto;
        }
        .sign-stamp-box {
            width: 165px;
            text-align: center;
        }
        .seal-sign-img {
            max-width: 115px;
            max-height: 13mm;
            display: block;
            margin: 0 auto 2px auto;
        }

        /* Middle Cut Here Dashed Line */
        .cut-divider {
            width: 100%;
            height: 6.5mm;
            page-break-inside: avoid;
        }
        .cut-divider-table {
            width: 100%;
            border-collapse: collapse;
            padding-top: 1.8mm;
        }
        .cut-divider-table td {
            vertical-align: middle;
            padding: 0;
        }
        .cut-dashed-line {
            border-bottom: 1.2px dashed #6b7280;
            height: 1px;
        }
        .cut-text {
            font-size: 8.8px;
            color: #4b5563;
            padding: 0 10px;
            text-align: center;
            white-space: nowrap;
        }
    </style>
</head>
@php
    $branchId = $invoiceObj->branch_id ?? ($firstDetail->branch_id ?? (Session::get('branch_id') ?: 1));
    $sessionId = $invoiceObj->session_id ?? ($firstDetail->session_id ?? (Session::get('session_id') ?: 1));
    $getSetting = \App\Models\Setting::where('branch_id', $branchId)->first() ?? Helper::getSetting();
    $feesSetting = isset($feesSetting) && $feesSetting instanceof \App\Models\FeesSetting
        ? $feesSetting
        : \App\Models\FeesSetting::getSetting($branchId, $sessionId);

    $receiptLayout = ($feesSetting->receipt_layout ?? 'a4_dual') === 'a4_single' ? 'a4_single' : 'a4_dual';
    $showSchoolLogo = (bool) ($feesSetting->show_school_logo ?? 1);
    $showWatermark = (bool) ($feesSetting->show_watermark ?? 1);
    $showSignatureBox = (bool) ($feesSetting->show_signature_box ?? 1);
    $showPaymentModeDetails = (bool) ($feesSetting->show_payment_mode_details ?? 1);
    $receiptHeaderTitle = trim((string) ($feesSetting->receipt_header_title ?? 'FEE RECEIPT')) ?: 'FEE RECEIPT';
    $receiptTerms = trim((string) ($feesSetting->receipt_terms_conditions ?? ''));

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
    $feeDate = !empty($rawDate) ? date('d-m-Y', strtotime($rawDate)) : '-';
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
    $sessionLabel = !empty($sessionFrom) ? ($sessionFrom . (!empty($sessionTo) ? '-' . $sessionTo : '')) : '';

    $totalPaid = (float) (($invoiceObj->amount ?? null)
        ?? ($invoiceObj->total_amount ?? null)
        ?? ($isCollection ? $data->sum('paid_amount') ?: $data->sum('total_amount') : ($firstDetail->paid_amount ?? $firstDetail->total_amount ?? ($dataArr['total_amount'] ?? 0))));

    $totalDiscount = (float) (($invoiceObj->discount ?? null)
        ?? ($invoiceObj->discount_given ?? null)
        ?? ($isCollection ? $data->sum('discount') : ($firstDetail->discount ?? ($dataArr['discount'] ?? 0))));

    $totalFine = (float) (($invoiceObj->total_fine ?? null)
        ?? ($isCollection ? $data->sum('installment_fine') : ($firstDetail->installment_fine ?? 0)));

    // Resolve Branding Assets (Logo, Watermark, Seal & Sign) as Base64 if local file exists
    $resolveAssetBase64 = function ($folder, $fileName) {
        if (empty($fileName)) {
            return null;
        }
        $possiblePaths = [
            public_path('schoolimage/setting/' . $folder . '/' . $fileName),
            rtrim((string) env('IMAGE_UPLOAD_PATH'), '/\\') . '/setting/' . $folder . '/' . $fileName,
        ];
        foreach ($possiblePaths as $p) {
            if ($p && @is_file($p) && @is_readable($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION) ?: 'png');
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : ($ext === 'webp' ? 'image/webp' : 'image/png');
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($p));
            }
        }
        return null;
    };

    $logoBase64 = $resolveAssetBase64('left_logo', $getSetting['left_logo'] ?? null);
    $watermarkBase64 = $resolveAssetBase64('watermark_image', $getSetting['watermark_image'] ?? null);
    $sealSignBase64 = $resolveAssetBase64('seal_sign', $getSetting['seal_sign'] ?? null);

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

    // Build dynamic collected fee items list
    $collectedItems = [];
    foreach ($detailsList as $item) {
        $hName = is_object($item)
            ? ($item->fees_group_name ?? ($item->FeesCollect->name ?? 'Tuition Fee'))
            : ($item['fees_group_name'] ?? 'Tuition Fee');
        $hDisc = (float) (is_object($item) ? ($item->discount ?? 0) : ($item['discount'] ?? 0));
        $hFine = (float) (is_object($item) ? ($item->installment_fine ?? 0) : ($item['installment_fine'] ?? 0));
        $hPaid = (float) (is_object($item) ? ($item->paid_amount ?? $item->total_amount ?? 0) : ($item['paid_amount'] ?? $item['total_amount'] ?? 0));

        $collectedItems[] = [
            'head' => $hName,
            'discount' => $hDisc,
            'fine' => $hFine,
            'paid' => $hPaid,
        ];
    }

    if (empty($collectedItems)) {
        $collectedItems[] = [
            'head' => 'Academic / Tuition Fee (' . $feeMonth . ')',
            'discount' => $totalDiscount,
            'fine' => $totalFine,
            'paid' => $totalPaid,
        ];
    }

    $isSingleA4 = ($receiptLayout === 'a4_single');
    $minReservedRows = $isSingleA4 ? 16 : 10;
    $maxRenderRows = $isSingleA4 ? 20 : 10;
    $isCompactTable = count($collectedItems) > $minReservedRows;
    $copies = $isSingleA4 ? [$receiptHeaderTitle] : ['OFFICE COPY', 'PARENT COPY'];
    $termsLines = !empty($receiptTerms) ? array_slice(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $receiptTerms))), 0, $isSingleA4 ? 4 : 2) : [];
@endphp
<body>
    <div class="page-wrap">
        @foreach($copies as $copyIdx => $copyTitle)
            <div class="{{ $isSingleA4 ? 'receipt-full' : 'receipt-half' }}">
                {{-- Center Background Watermark --}}
                @if($showWatermark && $watermarkBase64)
                    <div class="receipt-watermark">
                        <img src="{{ $watermarkBase64 }}" alt="Watermark">
                    </div>
                @endif

                {{-- Top Organization Header --}}
                <table class="org-header-table">
                    <tr>
                        @if($showSchoolLogo)
                            <td class="org-logo-cell">
                                <div class="org-logo-box">
                                    @if($logoBase64)
                                        <img src="{{ $logoBase64 }}" alt="Logo">
                                    @else
                                        <div class="org-logo-initial">{{ strtoupper(substr($getSetting['name'] ?? 'S', 0, 1)) }}</div>
                                    @endif
                                </div>
                            </td>
                        @endif
                        <td>
                            <div class="org-title">{{ $getSetting['name'] ?? 'SCHOOL NAME' }}</div>
                            @if(!empty($getSetting['address']))
                                <div class="org-address">{{ $getSetting['address'] }}</div>
                            @endif
                            <div class="org-contact">
                                @if(!empty($getSetting['mobile']))
                                    <b>Phone:</b> {{ $getSetting['mobile'] }}
                                @endif
                                @if(!empty($getSetting['gmail']))
                                    &nbsp;|&nbsp; <b>Email:</b> {{ $getSetting['gmail'] }}
                                @endif
                                @if(!empty($mobile))
                                    &nbsp;|&nbsp; <b>Student Mob:</b> {{ $mobile }}
                                @endif
                            </div>
                        </td>
                        <td class="copy-badge-cell">
                            <div class="copy-box">{{ $copyTitle }}</div>
                            @if(!$isSingleA4 && !empty($receiptHeaderTitle) && strtoupper($receiptHeaderTitle) !== strtoupper($copyTitle))
                                <div class="session-sub">{{ $receiptHeaderTitle }}{{ !empty($sessionLabel) ? ' • ' . $sessionLabel : '' }}</div>
                            @elseif(!empty($sessionLabel))
                                <div class="session-sub">Session: {{ $sessionLabel }}</div>
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- Dotted Metadata Lines (Rcpt No, Date, Name, Class, Father, Adm. No.) --}}
                <table class="meta-lines-table">
                    <tr>
                        <td style="width: 52%; padding-right: 18px;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Rcpt No.:</td>
                                    <td class="field-value">{{ $receiptNo }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 48%;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Date:</td>
                                    <td class="field-value">{{ $feeDate }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 58%; padding-right: 18px;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Name:</td>
                                    <td class="field-value">{{ $studentName }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 42%;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Class:</td>
                                    <td class="field-value">{{ $classDisplay ?: '-' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 58%; padding-right: 18px;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Father:</td>
                                    <td class="field-value">{{ $fatherName }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 42%;">
                            <table class="field-wrap">
                                <tr>
                                    <td class="field-label">Adm. No.:</td>
                                    <td class="field-value">{{ $admissionNo }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                {{-- Dynamic Tabular Fee Heads Section with Reserved Rows --}}
                <table class="fee-table {{ $isCompactTable ? 'compact-table' : '' }}">
                    <thead>
                        <tr>
                            <th style="width: 8%;" class="text-center">S.No</th>
                            <th style="width: 50%;" class="text-left">Fee Head / Particulars</th>
                            <th style="width: 14%;" class="text-right">Discount (₹)</th>
                            <th style="width: 13%;" class="text-right">Fine (₹)</th>
                            <th style="width: 15%;" class="text-right">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $renderedCount = 0; @endphp
                        @foreach(array_slice($collectedItems, 0, $maxRenderRows) as $idx => $fItem)
                            @php $renderedCount++; @endphp
                            <tr>
                                <td class="text-center">{{ !empty($fItem['head']) ? ($idx + 1) : '' }}</td>
                                <td class="fw-bold">{{ $fItem['head'] }}</td>
                                <td class="text-right">{{ $fItem['discount'] > 0 ? number_format($fItem['discount'], 2) : '-' }}</td>
                                <td class="text-right">{{ $fItem['fine'] > 0 ? number_format($fItem['fine'], 2) : '-' }}</td>
                                <td class="text-right fw-bold">₹{{ number_format($fItem['paid'], 2) }}</td>
                            </tr>
                        @endforeach

                        {{-- Reserved blank rows so layout stays balanced --}}
                        @for($r = $renderedCount; $r < $minReservedRows; $r++)
                            <tr>
                                <td class="text-center">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text-right">&nbsp;</td>
                                <td class="text-right">&nbsp;</td>
                                <td class="text-right">&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-left">
                                TOTAL ₹
                                <span class="words-inline">({{ $amountInWords }})</span>
                            </td>
                            <td class="text-right">₹{{ number_format($totalPaid, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Payment Mode & Ref Line --}}
                @if($showPaymentModeDetails)
                    <div class="payment-meta-row">
                        <b>Payment Mode:</b> {{ strtoupper($paymentMode) }}
                        @if(!empty($transactionId))
                            &nbsp;&nbsp;|&nbsp;&nbsp; <b>Ref / Txn ID:</b> {{ $transactionId }}
                        @endif
                        @if(!empty($invoiceObj->remark))
                            &nbsp;&nbsp;|&nbsp;&nbsp; <b>Note:</b> {{ Str::limit($invoiceObj->remark, 55) }}
                        @endif
                    </div>
                @elseif(!empty($invoiceObj->remark))
                    <div class="payment-meta-row">
                        <b>Note:</b> {{ Str::limit($invoiceObj->remark, 70) }}
                    </div>
                @endif

                {{-- Optional Terms & Conditions / Footer Note --}}
                @if(!empty($termsLines))
                    <div class="terms-box">
                        <b>Terms:</b> {{ implode(' | ', $termsLines) }}
                    </div>
                @endif

                {{-- Generous Blank Space Reserved for Physical Seal / Stamp & Signatures at the Bottom --}}
                @if($showSignatureBox)
                    <div class="sign-footer-wrap">
                        <table class="sign-table">
                            <tr>
                                <td style="width: 50%;">
                                    <div class="sign-line-box">Parent</div>
                                </td>
                                <td style="width: 50%; text-align: right;">
                                    <div class="sign-stamp-box sign-right-align">
                                        @if($sealSignBase64)
                                            <img src="{{ $sealSignBase64 }}" class="seal-sign-img" alt="Seal & Sign">
                                        @endif
                                        <div class="sign-line-box">Cashier / Stamp</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>

            @if(!$isSingleA4 && $copyIdx === 0)
                <div class="cut-divider">
                    <table class="cut-divider-table">
                        <tr>
                            <td style="width: 43%;"><div class="cut-dashed-line"></div></td>
                            <td style="width: 14%;" class="cut-text">cut here</td>
                            <td style="width: 43%;"><div class="cut-dashed-line"></div></td>
                        </tr>
                    </table>
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>
