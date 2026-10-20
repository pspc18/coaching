<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fees Receipt</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.3;
        }
        .receipt-page {
            width: 100%;
            max-width: 194mm;
            margin: 0 auto;
        }
        .receipt-card {
            border: 1.5px solid #0f2942;
            border-radius: 4px;
            background: #ffffff;
            padding: 8px 10px 7px;
            page-break-inside: avoid;
            position: relative;
        }
        /* Header */
        .hdr-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #0f2942;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .hdr-table td {
            vertical-align: middle;
            padding: 0 0 4px 0;
        }
        .logo-box {
            width: 48px;
            height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            background: #f8fafc;
            overflow: hidden;
        }
        .logo-box img {
            max-width: 44px;
            max-height: 44px;
            display: block;
            margin: 1px auto;
        }
        .logo-fallback {
            font-size: 16px;
            font-weight: bold;
            color: #0f2942;
            line-height: 46px;
        }
        .inst-name {
            font-size: 15px;
            font-weight: bold;
            color: #0f2942;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0 0 2px 0;
        }
        .inst-sub {
            font-size: 8.5px;
            color: #475569;
            margin: 0;
            line-height: 1.25;
        }
        .badge-copy {
            display: inline-block;
            background: #0f2942;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 2.5px 7px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-label {
            font-size: 11px;
            font-weight: bold;
            color: #0f2942;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .session-pill {
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
        }

        /* Student & Receipt Info Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .meta-table td {
            padding: 3px 6px;
            font-size: 8.8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .meta-lbl {
            color: #64748b;
            font-weight: bold;
            width: 14%;
            text-transform: uppercase;
            font-size: 7.8px;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
            width: 36%;
        }
        .meta-divider {
            border-left: 1px solid #cbd5e1;
        }

        /* Fee Breakdown Table */
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .fee-table th {
            background: #0f2942;
            color: #ffffff;
            font-size: 8.2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 4px 6px;
            border: 1px solid #0f2942;
            text-align: left;
        }
        .fee-table td {
            padding: 3.5px 6px;
            font-size: 8.8px;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            vertical-align: middle;
        }
        .fee-table tr.alt-row td {
            background: #f8fafc;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .font-bold {
            font-weight: bold;
        }

        /* Totals & Words Section */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .summary-table td {
            vertical-align: top;
            padding: 0;
        }
        .words-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 4px 7px;
            border-radius: 3px;
            min-height: 40px;
        }
        .words-lbl {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 1px;
        }
        .words-val {
            font-size: 8.8px;
            font-weight: bold;
            color: #0f2942;
        }
        .totals-box {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-box td {
            padding: 2.5px 6px;
            font-size: 8.5px;
            border: 1px solid #cbd5e1;
        }
        .totals-box tr.net-row td {
            background: #eef6ff;
            color: #0f2942;
            font-size: 9.5px;
            font-weight: bold;
            border-color: #93c5fd;
        }

        /* Footer & Signatures */
        .ftr-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
        }
        .ftr-table td {
            vertical-align: bottom;
            padding-top: 3px;
        }
        .terms-list {
            margin: 0;
            padding-left: 11px;
            font-size: 7.5px;
            color: #64748b;
            line-height: 1.25;
        }
        .sign-box {
            text-align: right;
            font-size: 8px;
            font-weight: bold;
            color: #0f2942;
        }
        .sign-line {
            display: inline-block;
            width: 120px;
            border-top: 1px solid #475569;
            padding-top: 2px;
            text-align: center;
            margin-top: 14px;
        }

        /* Cut Here Divider */
        .cut-divider-wrap {
            width: 100%;
            margin: 14px 0;
            text-align: center;
            page-break-inside: avoid;
        }
        .cut-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cut-table td {
            vertical-align: middle;
            padding: 0;
        }
        .cut-line {
            border-bottom: 1.2px dashed #64748b;
            height: 1px;
        }
        .cut-badge {
            font-size: 7.8px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1px 8px;
            background: #f1f5f9;
            border: 1px dashed #94a3b8;
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
    $feeDate = !empty($rawDate) ? date('d M Y', strtotime($rawDate)) : '-';
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

    $grossAmount = $totalPaid + $totalDiscount - $totalFine;
    if ($grossAmount < $totalPaid) {
        $grossAmount = $totalPaid;
    }

    // Resolve Logo as Base64 if local file exists, else skip broken remote calls in PDF
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
    $copies = ['OFFICE COPY', 'STUDENT COPY'];
@endphp
<body>
    <div class="receipt-page">
        @foreach($copies as $copyIdx => $copyTitle)
            <div class="receipt-card">
                {{-- Header --}}
                <table class="hdr-table">
                    <tr>
                        <td style="width: 56px;">
                            <div class="logo-box">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" alt="Logo">
                                @else
                                    <div class="logo-fallback">{{ strtoupper(substr($getSetting['name'] ?? 'S', 0, 1)) }}</div>
                                @endif
                            </div>
                        </td>
                        <td style="padding-left: 8px;">
                            <div class="inst-name">{{ $getSetting['name'] ?? 'Educational Institute' }}</div>
                            <div class="inst-sub">
                                {{ $getSetting['address'] ?? '' }}
                                @if(!empty($getSetting['mobile']) || !empty($getSetting['gmail']))
                                    <br>
                                    <b>Ph:</b> {{ $getSetting['mobile'] ?? '-' }}
                                    @if(!empty($getSetting['gmail']))
                                        &nbsp;|&nbsp; <b>Email:</b> {{ $getSetting['gmail'] }}
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="text-right" style="width: 135px;">
                            <div class="badge-copy">{{ $copyTitle }}</div>
                            <div class="doc-label">FEE RECEIPT</div>
                            <div class="session-pill">Session: {{ $sessionLabel }}</div>
                        </td>
                    </tr>
                </table>

                {{-- Student & Receipt Meta Details --}}
                <table class="meta-table">
                    <tr>
                        <td class="meta-lbl">Receipt No</td>
                        <td class="meta-val">{{ $receiptNo }}</td>
                        <td class="meta-lbl meta-divider">Receipt Date</td>
                        <td class="meta-val">{{ $feeDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Student Name</td>
                        <td class="meta-val">{{ $studentName }}</td>
                        <td class="meta-lbl meta-divider">Admission No</td>
                        <td class="meta-val">{{ $admissionNo }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Father's Name</td>
                        <td class="meta-val">{{ $fatherName }}</td>
                        <td class="meta-lbl meta-divider">Class / Course</td>
                        <td class="meta-val">{{ $classDisplay ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Mobile No</td>
                        <td class="meta-val">{{ $mobile }}</td>
                        <td class="meta-lbl meta-divider">Payment Mode</td>
                        <td class="meta-val">
                            {{ strtoupper($paymentMode) }}
                            @if(!empty($transactionId))
                                <span style="font-weight: normal; font-size: 8px; color: #475569;">(Ref: {{ $transactionId }})</span>
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- Fee Particulars Table --}}
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;" class="text-center">S.No</th>
                            <th style="width: 50%;">Particulars / Fee Head</th>
                            <th style="width: 14%;" class="text-right">Discount (₹)</th>
                            <th style="width: 14%;" class="text-right">Fine (₹)</th>
                            <th style="width: 14%;" class="text-right">Paid Amt (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowCount = 0; @endphp
                        @forelse($detailsList as $idx => $item)
                            @php
                                $rowCount++;
                                $headName = is_object($item)
                                    ? ($item->fees_group_name ?? ($item->FeesCollect->name ?? 'Tuition / Academic Fee'))
                                    : ($item['fees_group_name'] ?? 'Tuition / Academic Fee');
                                $itemDisc = (float) (is_object($item) ? ($item->discount ?? 0) : ($item['discount'] ?? 0));
                                $itemFine = (float) (is_object($item) ? ($item->installment_fine ?? 0) : ($item['installment_fine'] ?? 0));
                                $itemPaid = (float) (is_object($item) ? ($item->paid_amount ?? $item->total_amount ?? 0) : ($item['paid_amount'] ?? $item['total_amount'] ?? 0));
                            @endphp
                            <tr class="{{ $idx % 2 === 1 ? 'alt-row' : '' }}">
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td class="font-bold">{{ $headName }}</td>
                                <td class="text-right">{{ $itemDisc > 0 ? number_format($itemDisc, 2) : '0.00' }}</td>
                                <td class="text-right">{{ $itemFine > 0 ? number_format($itemFine, 2) : '0.00' }}</td>
                                <td class="text-right font-bold">{{ number_format($itemPaid, 2) }}</td>
                            </tr>
                        @empty
                            @php $rowCount = 1; @endphp
                            <tr>
                                <td class="text-center">1</td>
                                <td class="font-bold">Academic / Course Fee ({{ $feeMonth }})</td>
                                <td class="text-right">{{ number_format($totalDiscount, 2) }}</td>
                                <td class="text-right">{{ number_format($totalFine, 2) }}</td>
                                <td class="text-right font-bold">{{ number_format($totalPaid, 2) }}</td>
                            </tr>
                        @endforelse

                        {{-- Keep consistent compact height with filler rows if fewer than 2 items --}}
                        @for($r = $rowCount; $r < 2; $r++)
                            <tr>
                                <td class="text-center" style="color: transparent;">-</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                {{-- Summary & Amount in Words --}}
                <table class="summary-table">
                    <tr>
                        <td style="width: 60%; padding-right: 8px;">
                            <div class="words-box">
                                <div class="words-lbl">Amount in Words</div>
                                <div class="words-val">{{ $amountInWords }}</div>
                                @if(!empty($invoiceObj->remark))
                                    <div style="font-size: 7.8px; color: #64748b; margin-top: 2px;">
                                        <b>Remark:</b> {{ $invoiceObj->remark }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td style="width: 40%;">
                            <table class="totals-box">
                                @if($totalDiscount > 0 || $totalFine > 0)
                                    <tr>
                                        <td>Total Discount</td>
                                        <td class="text-right">- ₹{{ number_format($totalDiscount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($totalFine > 0)
                                    <tr>
                                        <td>Late Fine</td>
                                        <td class="text-right">+ ₹{{ number_format($totalFine, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="net-row">
                                    <td>Total Paid Amount</td>
                                    <td class="text-right">₹{{ number_format($totalPaid, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                {{-- Footer & Signatory --}}
                <table class="ftr-table">
                    <tr>
                        <td style="width: 68%;">
                            <ul class="terms-list">
                                <li>Fees once paid are neither refundable nor transferable under any circumstances.</li>
                                <li>Subject to realization of cheque / electronic payment settlement.</li>
                                <li>This is a computer-generated official receipt.</li>
                            </ul>
                        </td>
                        <td style="width: 32%;" class="sign-box">
                            <div class="sign-line">Authorized Signatory</div>
                        </td>
                    </tr>
                </table>
            </div>

            @if($copyIdx === 0)
                <div class="cut-divider-wrap">
                    <table class="cut-table">
                        <tr>
                            <td style="width: 38%;"><div class="cut-line"></div></td>
                            <td style="width: 24%;" class="text-center">
                                <span class="cut-badge">&#9986; Cut From Here</span>
                            </td>
                            <td style="width: 38%;"><div class="cut-line"></div></td>
                        </tr>
                    </table>
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>
