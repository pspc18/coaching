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
            font-size: 10px;
            color: #111827;
            background: #ffffff;
            line-height: 1.3;
        }
        .page-wrap {
            width: 100%;
            margin: 0 auto;
        }

        /* Half-Page Receipt Block */
        .receipt-half {
            position: relative;
            width: 100%;
            height: 136.5mm;
            padding: 2mm 1mm 1mm 1mm;
            page-break-inside: avoid;
            overflow: hidden;
        }

        /* Organization Header */
        .org-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.8px solid #8b2635;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .org-header-table td {
            vertical-align: middle;
            padding: 0 0 6px 0;
        }
        .org-logo-cell {
            width: 54px;
            padding-right: 10px !important;
        }
        .org-logo-box {
            width: 48px;
            height: 48px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            background: #ffffff;
        }
        .org-logo-box img {
            max-width: 44px;
            max-height: 44px;
            display: block;
            margin: 1px auto;
        }
        .org-logo-initial {
            font-size: 20px;
            font-weight: bold;
            color: #8b2635;
            line-height: 46px;
        }
        .org-title {
            font-size: 17px;
            font-weight: bold;
            color: #8b2635;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 2px 0;
            line-height: 1.15;
        }
        .org-address {
            font-size: 8.8px;
            color: #374151;
            margin: 0 0 1px 0;
            line-height: 1.25;
        }
        .org-contact {
            font-size: 8.5px;
            color: #4b5563;
            margin: 0;
        }
        .copy-badge-cell {
            width: 135px;
            text-align: right;
            vertical-align: top !important;
        }
        .copy-box {
            display: inline-block;
            border: 1.3px solid #1f2937;
            padding: 3.5px 12px;
            font-size: 9.5px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: #ffffff;
        }
        .session-sub {
            font-size: 8px;
            color: #4b5563;
            font-weight: bold;
            margin-top: 4px;
        }

        /* Dotted Student Info Lines */
        .meta-lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .meta-lines-table td {
            padding: 4.5px 0;
            vertical-align: bottom;
            font-size: 10px;
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

        /* 2-Column Fee Heads Grid */
        .fee-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 6px;
        }
        .fee-grid-table > tbody > tr > td {
            padding: 4px 0;
            vertical-align: bottom;
        }
        .fee-item-row {
            width: 100%;
            border-collapse: collapse;
        }
        .fee-item-row td {
            padding: 0;
            vertical-align: bottom;
        }
        .fee-head-name {
            font-size: 10px;
            color: #111827;
            width: 68%;
            padding-right: 8px !important;
        }
        .fee-head-amt {
            width: 32%;
            border-bottom: 1px solid #374151;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            padding: 0 4px 1px 4px !important;
        }

        /* Total Bar */
        .total-bar-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1.5px solid #1f2937;
            margin-top: 4px;
            padding-top: 5px;
        }
        .total-bar-table td {
            padding: 6px 0 2px 0;
            vertical-align: bottom;
        }
        .total-label {
            font-size: 11.5px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
        }
        .words-inline {
            font-size: 8.8px;
            font-weight: normal;
            color: #4b5563;
            margin-left: 10px;
            font-style: italic;
        }
        .total-amt-line {
            border-bottom: 1.3px solid #1f2937;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            padding: 0 6px 2px 6px !important;
        }

        /* Payment Mode Sub-row */
        .payment-meta-row {
            margin-top: 5px;
            font-size: 8.8px;
            color: #374151;
        }
        .payment-meta-row b {
            color: #111827;
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
            padding-top: 4px;
            text-align: center;
            font-size: 9.5px;
            color: #111827;
        }
        .sign-right-align {
            margin-left: auto;
        }

        /* Middle Cut Here Dashed Line */
        .cut-divider {
            width: 100%;
            height: 7mm;
            page-break-inside: avoid;
        }
        .cut-divider-table {
            width: 100%;
            border-collapse: collapse;
            padding-top: 2mm;
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
            font-size: 9px;
            color: #4b5563;
            padding: 0 10px;
            text-align: center;
            white-space: nowrap;
        }
    </style>
</head>
@php
    $getSetting = Helper::getSetting();

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

    // Build 6-slot 2-column fee heads array matching the reference layout
    // First include all paid heads from this receipt, then fill remaining up to 6 slots with standard fee heads
    $feeSlots = [];
    foreach ($detailsList as $item) {
        $hName = is_object($item)
            ? ($item->fees_group_name ?? ($item->FeesCollect->name ?? 'Tuition Fee'))
            : ($item['fees_group_name'] ?? 'Tuition Fee');
        $hAmt = (float) (is_object($item) ? ($item->paid_amount ?? $item->total_amount ?? 0) : ($item['paid_amount'] ?? $item['total_amount'] ?? 0));
        $feeSlots[] = [
            'label' => $hName,
            'amount' => $hAmt > 0 ? number_format($hAmt, 2) : '',
        ];
    }

    if ($totalFine > 0) {
        $feeSlots[] = [
            'label' => 'Late Fine / Other',
            'amount' => number_format($totalFine, 2),
        ];
    }

    if ($totalDiscount > 0) {
        $feeSlots[] = [
            'label' => 'Fee Discount (-)',
            'amount' => '-' . number_format($totalDiscount, 2),
        ];
    }

    $defaultHeads = [
        'Tuition Fee',
        'Admission / Annual Fee',
        'Transport Fee',
        'Exam Fee',
        'Computer / Lab Fee',
        'Late Fine / Other',
    ];

    $existingLabels = array_map(function ($s) {
        return strtolower(trim($s['label']));
    }, $feeSlots);

    foreach ($defaultHeads as $defHead) {
        if (count($feeSlots) >= 6) {
            break;
        }
        if (!in_array(strtolower($defHead), $existingLabels)) {
            $feeSlots[] = [
                'label' => $defHead,
                'amount' => '',
            ];
        }
    }

    while (count($feeSlots) < 6) {
        $feeSlots[] = ['label' => 'Other Fee', 'amount' => ''];
    }

    // Pair into rows of 2 columns (max 4 rows = 8 items so it never overflows the reserved area)
    $feeRows = array_chunk(array_slice($feeSlots, 0, 8), 2);

    $copies = ['OFFICE COPY', 'PARENT COPY'];
@endphp
<body>
    <div class="page-wrap">
        @foreach($copies as $copyIdx => $copyTitle)
            <div class="receipt-half">
                {{-- Top Organization Header --}}
                <table class="org-header-table">
                    <tr>
                        <td class="org-logo-cell">
                            <div class="org-logo-box">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" alt="Logo">
                                @else
                                    <div class="org-logo-initial">{{ strtoupper(substr($getSetting['name'] ?? 'S', 0, 1)) }}</div>
                                @endif
                            </div>
                        </td>
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
                            @if(!empty($sessionLabel))
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

                {{-- 2-Column Fee Heads Section --}}
                <table class="fee-grid-table">
                    <tbody>
                        @foreach($feeRows as $pair)
                            <tr>
                                <td style="width: 48%; padding-right: 18px;">
                                    <table class="fee-item-row">
                                        <tr>
                                            <td class="fee-head-name">{{ $pair[0]['label'] ?? '' }}</td>
                                            <td class="fee-head-amt">{!! !empty($pair[0]['amount']) ? '₹' . e($pair[0]['amount']) : '&nbsp;' !!}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 4%;"></td>
                                <td style="width: 48%;">
                                    @if(isset($pair[1]))
                                        <table class="fee-item-row">
                                            <tr>
                                                <td class="fee-head-name">{{ $pair[1]['label'] }}</td>
                                                <td class="fee-head-amt">{!! !empty($pair[1]['amount']) ? '₹' . e($pair[1]['amount']) : '&nbsp;' !!}</td>
                                            </tr>
                                        </table>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- TOTAL Bar --}}
                <table class="total-bar-table">
                    <tr>
                        <td style="width: 72%;">
                            <span class="total-label">TOTAL ₹</span>
                            <span class="words-inline">({{ $amountInWords }})</span>
                        </td>
                        <td style="width: 28%;" class="total-amt-line">
                            ₹{{ number_format($totalPaid, 2) }}
                        </td>
                    </tr>
                </table>

                {{-- Payment Mode & Ref Line --}}
                <div class="payment-meta-row">
                    <b>Payment Mode:</b> {{ strtoupper($paymentMode) }}
                    @if(!empty($transactionId))
                        &nbsp;&nbsp;|&nbsp;&nbsp; <b>Ref / Txn ID:</b> {{ $transactionId }}
                    @endif
                    @if(!empty($invoiceObj->remark))
                        &nbsp;&nbsp;|&nbsp;&nbsp; <b>Note:</b> {{ Str::limit($invoiceObj->remark, 55) }}
                    @endif
                </div>

                {{-- Generous Blank Space Reserved for Physical Seal / Stamp & Signatures at the Bottom --}}
                <div class="sign-footer-wrap">
                    <table class="sign-table">
                        <tr>
                            <td style="width: 50%;">
                                <div class="sign-line-box">Parent</div>
                            </td>
                            <td style="width: 50%; text-align: right;">
                                <div class="sign-line-box sign-right-align">Cashier / Stamp</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($copyIdx === 0)
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
