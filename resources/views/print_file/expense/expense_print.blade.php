@php
    $getSetting = Helper::getSetting();
    $first = $data->first();
    $total = $data->sum('amount');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Expense Voucher {{ $first->invoice_no }}</title>
<style>
    @page { size: A4 portrait; margin: 8mm; }
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }
    body { background: #edf1f5; color: #263540; font-family: "Segoe UI", Arial, sans-serif; font-size: 10.5px; }
    .toolbar { width: 210mm; margin: 8px auto; text-align: right; }
    .print-btn { border: 0; border-radius: 4px; padding: 7px 15px; background: #173f5f; color: #fff; cursor: pointer; font-weight: 600; }
    .voucher {
        position: relative;
        width: 210mm;
        height: 297mm;
        margin: 0 auto 15px;
        padding: 8mm 10mm 7mm;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 5px 24px rgba(25, 45, 60, .15);
    }
    .voucher::before { content: ""; position: absolute; inset: 0 0 auto; height: 5px; background: linear-gradient(90deg, #173f5f 0 76%, #c69b48 76%); }
    .header { display: flex; align-items: center; min-height: 18mm; padding-bottom: 7px; border-bottom: 1px solid #ccd5dc; }
    .logo { width: 55px; max-height: 50px; object-fit: contain; }
    .org { flex: 1; text-align: right; }
    .org h1 { margin: 0 0 2px; color: #173f5f; font-family: Georgia, serif; font-size: 19px; line-height: 1.1; }
    .org p { margin: 1px 0; color: #687681; font-size: 9px; }
    .heading { display: flex; align-items: center; justify-content: space-between; margin: 8px 0; }
    .heading h2 { margin: 0; color: #173f5f; font-family: Georgia, serif; font-size: 16px; letter-spacing: 1.4px; }
    .heading small { color: #7b8790; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; }
    .voucher-number { padding: 4px 9px; border: 1px solid #d7c49d; border-radius: 3px; background: #fffaf0; color: #735d31; font-weight: 700; }
    .meta { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1px; overflow: hidden; border: 1px solid #d8e0e6; border-radius: 4px; background: #d8e0e6; }
    .meta-item { min-height: 32px; padding: 5px 8px; background: #f7f9fa; }
    .meta-label { display: block; color: #77858e; font-size: 7.5px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; }
    .meta-value { display: block; margin-top: 1px; color: #253744; font-weight: 600; }
    .status { display: inline-block; padding: 1px 7px; border-radius: 10px; background: {{ $first->payment_status === 'pending' ? '#fff0c2' : '#dff4e7' }}; color: {{ $first->payment_status === 'pending' ? '#856404' : '#176b3a' }}; font-size: 8px; font-weight: 700; }
    .items { width: 100%; min-height: 175mm; margin-top: 8px; border-collapse: collapse; table-layout: fixed; }
    .items th { padding: 5px 6px; background: #173f5f; color: #fff; font-size: 8px; letter-spacing: .4px; text-align: left; text-transform: uppercase; }
    .items td { padding: 5px 6px; border: 1px solid #dce2e7; }
    .items tbody tr:nth-child(even) { background: #f8fafb; }
    .items .data-row { height: 8mm; }
    .items .filler-row { height: 160mm; background: #fff !important; }
    .items .filler-row td { padding: 0; border-top: 0; }
    .items th:first-child, .items td:first-child { width: 28px; text-align: center; }
    .items th:nth-last-child(-n+3), .items td:nth-last-child(-n+3) { text-align: right; }
    .summary { display: flex; justify-content: space-between; gap: 12px; margin-top: 7px; }
    .notes { flex: 1; padding: 5px 8px; border-left: 3px solid #c69b48; background: #faf8f3; color: #57656e; line-height: 1.35; }
    .notes strong { color: #314652; }
    .total { width: 50mm; padding: 5px 9px; border: 1px solid #cad6df; background: #f3f7fa; text-align: right; }
    .total span { display: block; color: #71808b; font-size: 8px; text-transform: uppercase; }
    .total strong { color: #173f5f; font-size: 15px; }
    .signatures { display: flex; justify-content: space-between; margin-top: 25mm; }
    .signature { width: 48mm; padding-top: 3px; border-top: 1px solid #71808a; text-align: center; color: #52616b; font-size: 8.5px; }
    .footer-note { position: absolute; bottom: 2.5mm; left: 0; width: 100%; color: #9aa4ab; font-size: 7px; text-align: center; }
    @media print {
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            text-shadow: none !important;
        }
        html, body {
            width: auto;
            min-height: 0;
            height: auto;
            margin: 0;
            padding: 0;
            background: #fff;
            overflow: visible;
        }
        body, .voucher, .voucher * {
            visibility: visible !important;
            opacity: 1 !important;
        }
        .toolbar { display: none; }
        .voucher {
            width: 100%;
            min-height: 0;
            height: auto;
            margin: 0;
            padding: 0;
            overflow: visible;
            box-shadow: none;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .voucher::before { top: -8mm; right: -8mm; left: -8mm; }
        .signatures { margin-top: 22mm; }
        .footer-note { position: static; margin-top: 7mm; }
        .items th {
            color: #fff !important;
            -webkit-text-fill-color: #fff !important;
        }
        .org h1, .heading h2, .total strong { color: #173f5f !important; -webkit-text-fill-color: #173f5f !important; }
        .meta-value, .items td, .notes, .signature { color: #263540 !important; -webkit-text-fill-color: #263540 !important; }
        .status { color: {{ $first->payment_status === 'pending' ? '#856404' : '#176b3a' }} !important; -webkit-text-fill-color: {{ $first->payment_status === 'pending' ? '#856404' : '#176b3a' }} !important; }
    }
</style>
</head>
<body>
<div class="toolbar"><button class="print-btn" type="button" onclick="window.print()">Print A4 Voucher</button></div>
<main class="voucher">
    <header class="header">
        <img class="logo" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" alt="Logo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
        <div class="org"><h1>{{ $getSetting['name'] ?? '' }}</h1><p>{{ $getSetting['address'] ?? '' }}</p><p>{{ $getSetting['mobile'] ?? '' }} @if(!empty($getSetting['gmail'])) &nbsp;|&nbsp; {{ $getSetting['gmail'] }} @endif</p></div>
    </header>

    <div class="heading"><div><h2>EXPENSE VOUCHER</h2><small>Official payment record</small></div><div class="voucher-number">{{ $first->invoice_no }}</div></div>

    <section class="meta">
        <div class="meta-item"><span class="meta-label">Expense Date</span><span class="meta-value">{{ $first->date ? date('d M Y', strtotime($first->date)) : '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Paid To / Vendor</span><span class="meta-value">{{ $first->payee_name ?: '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Payment Status</span><span class="meta-value"><span class="status">{{ strtoupper($first->payment_status ?: 'paid') }}</span></span></div>
        <div class="meta-item"><span class="meta-label">Payment Mode</span><span class="meta-value">{{ $paymentMode ?: '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Transaction Reference</span><span class="meta-value">{{ $first->payment_reference ?: '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Vendor Bill No.</span><span class="meta-value">{{ $first->bill_no ?: '-' }}</span></div>
    </section>

    <table class="items"><thead><tr><th>#</th><th>Category</th><th>Particular</th><th>Qty</th><th>Rate</th><th>Amount</th></tr></thead><tbody>
    @foreach($data as $i => $item)<tr class="data-row"><td>{{ $i + 1 }}</td><td>{{ $categories[$item->category_id] ?? 'Other' }}</td><td>{{ $item->name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format((float) $item->rate, 2) }}</td><td>{{ number_format((float) $item->amount, 2) }}</td></tr>@endforeach
    <tr class="filler-row" style="height: {{ max(30, 160 - (($data->count() - 1) * 8)) }}mm;">
        <td></td><td></td><td></td><td></td><td></td><td></td>
    </tr>
    </tbody></table>

    <div class="summary">
        <div class="notes"><strong>Purpose / Notes:</strong> {{ $first->description ?: '-' }}<br><small>Expense Type: {{ ucfirst(str_replace('_', ' ', $first->expense_type ?: 'one_time')) }}@if($first->recurring_frequency) &nbsp;•&nbsp; {{ ucfirst(str_replace('_', ' ', $first->recurring_frequency)) }} @endif</small></div>
        <div class="total"><span>Voucher Total</span><strong>INR {{ number_format($total, 2) }}</strong></div>
    </div>

    <div class="signatures"><div class="signature">Prepared By</div><div class="signature">Verified By</div><div class="signature">Authorized Signatory</div></div>
    <div class="footer-note">Computer-generated expense voucher for internal accounting records.</div>
</main>
</body>
</html>
