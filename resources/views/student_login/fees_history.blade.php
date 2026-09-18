@php
    $getUser = Helper::getUser();
    $totalFees = (float) ($summary['totalFees'] ?? 0);
    $paidFees = (float) ($summary['paidFees'] ?? 0);
    $discount = (float) ($summary['discount'] ?? 0);
    $currentDue = (float) ($summary['currentSessionDue'] ?? 0);
    $finePaid = (float) ($summary['finePaid'] ?? 0);
    $paymentProgress = $totalFees > 0 ? min(100, (int) round((($paidFees + $discount) / $totalFees) * 100)) : 0;
@endphp
@extends('student_login.layout.app')
@section('title', 'Fees')
@section('page_title', 'FEES')
@section('page_sub', Session::get('first_name').' · '.(optional($getUser->ClassTypes)->name ?? 'Student'))

@section('content')
<section class="student-fees-app">
    <div class="fees-hero">
        <div class="fees-hero-pattern"></div>
        <div class="fees-hero-head">
            <div><span>Current outstanding</span><h1>₹{{ number_format($currentDue, 2) }}</h1><p>{{ $currentDue > 0 ? 'Payment is pending for this session' : 'All assigned fees are cleared' }}</p></div>
            <span class="fees-hero-icon"><i class="bi bi-wallet2"></i></span>
        </div>
        <div class="fees-progress-copy"><span>Payment progress</span><strong>{{ $paymentProgress }}%</strong></div>
        <div class="fees-progress"><span style="width:{{ $paymentProgress }}%"></span></div>
        <div class="fees-hero-stats">
            <div><span>Assigned</span><strong>₹{{ number_format($totalFees, 2) }}</strong></div>
            <div><span>Received</span><strong>₹{{ number_format($paidFees, 2) }}</strong></div>
        </div>
    </div>

    <div class="fees-content">
        <div class="fees-mini-stats">
            <div><span class="mini-stat-icon discount"><i class="bi bi-tag"></i></span><span><small>Discount</small><strong>₹{{ number_format($discount, 2) }}</strong></span></div>
            <div><span class="mini-stat-icon fine"><i class="bi bi-exclamation-circle"></i></span><span><small>Fine paid</small><strong>₹{{ number_format($finePaid, 2) }}</strong></span></div>
        </div>

        <div class="fees-section-heading">
            <div><span>Fee breakdown</span><small>Head-wise payment history</small></div>
            <span class="section-count">{{ $feeHeadLedger->count() }} heads</span>
        </div>

        <div class="fee-head-list">
            @forelse($feeHeadLedger as $head)
                @php
                    $headStatus = $head->due_amount <= 0 ? 'paid' : ($head->paid_amount > 0 || $head->discount > 0 ? 'partial' : 'unpaid');
                    $headStatusLabel = $headStatus === 'paid' ? 'Paid' : ($headStatus === 'partial' ? 'Partial' : 'Unpaid');
                    $headProgress = $head->assigned_amount > 0 ? min(100, (int) round((($head->paid_amount + $head->discount) / $head->assigned_amount) * 100)) : 0;
                @endphp
                <article class="fee-head-card" id="fee-head-{{ $head->fees_group_id }}">
                    <button type="button" class="fee-head-toggle" data-target="fee-head-content-{{ $head->fees_group_id }}" aria-expanded="false">
                        <span class="fee-head-icon"><i class="bi bi-receipt"></i></span>
                        <span class="fee-head-copy"><strong>{{ $head->name }}</strong><small>@if(!empty($head->due_date))Due {{ \Carbon\Carbon::parse($head->due_date)->format('d M Y') }}@else Fee head #{{ $head->fees_group_id }}@endif</small></span>
                        <span class="fee-head-status {{ $headStatus }}">{{ $headStatusLabel }}</span>
                        <i class="bi bi-chevron-down fee-head-chevron"></i>
                    </button>

                    <div class="fee-head-overview">
                        <div><span>Assigned</span><strong>₹{{ number_format($head->assigned_amount, 2) }}</strong></div>
                        <div><span>Paid</span><strong class="amount-paid">₹{{ number_format($head->paid_amount, 2) }}</strong></div>
                        <div><span>Due</span><strong class="amount-due">₹{{ number_format($head->due_amount, 2) }}</strong></div>
                    </div>
                    <div class="fee-head-progress"><span style="width:{{ $headProgress }}%"></span></div>

                    <div class="fee-head-content d-none" id="fee-head-content-{{ $head->fees_group_id }}">
                        <div class="fee-extra-metrics">
                            <div><span>Discount</span><strong>₹{{ number_format($head->discount, 2) }}</strong></div>
                            <div><span>Fine paid</span><strong>₹{{ number_format($head->fine_amount, 2) }}</strong></div>
                        </div>
                        <div class="transaction-heading"><span>Transactions</span><small>{{ $head->payments->count() }} entries</small></div>
                        @forelse($head->payments as $payment)
                            <div class="transaction-row">
                                <span class="transaction-icon {{ (int) $payment->status === 0 ? 'received' : 'pending' }}"><i class="bi {{ (int) $payment->status === 0 ? 'bi-check2' : 'bi-clock' }}"></i></span>
                                <div class="transaction-copy"><strong>{{ $payment->receipt_no ?: 'Payment #'.$loop->iteration }}</strong><small>{{ !empty($payment->date) ? \Carbon\Carbon::parse($payment->date)->format('d M Y') : 'Date unavailable' }} · {{ $payment->payment_mode ?: 'Payment mode unavailable' }}</small></div>
                                <div class="transaction-amount"><strong>₹{{ number_format((float) $payment->paid_amount, 2) }}</strong><small class="{{ (int) $payment->status === 0 ? 'received' : 'pending' }}">{{ (int) $payment->status === 0 ? 'Received' : 'Pending' }}</small></div>
                                @if((float) $payment->discount > 0 || (float) $payment->installment_fine > 0)
                                    <div class="transaction-meta">@if((float) $payment->discount > 0)<span>Discount ₹{{ number_format((float) $payment->discount, 2) }}</span>@endif @if((float) $payment->installment_fine > 0)<span>Fine ₹{{ number_format((float) $payment->installment_fine, 2) }}</span>@endif</div>
                                @endif
                            </div>
                        @empty
                            <div class="fee-inline-empty"><i class="bi bi-receipt-cutoff"></i><span>No payment received for this fee head.</span></div>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="fees-empty"><span><i class="bi bi-wallet"></i></span><h2>No fees assigned</h2><p>No fee heads are assigned for the selected session.</p></div>
            @endforelse
        </div>

        <div class="fees-section-heading receipts-heading">
            <div><span>Fee receipts</span><small>View or download payment receipts</small></div>
            <span class="section-count">{{ $feeReceipts->count() }}</span>
        </div>

        <div class="receipt-card-list">
            @forelse($feeReceipts as $receipt)
                @php $isReceived = (int) $receipt->status === 0; @endphp
                <article class="receipt-card">
                    <div class="receipt-card-head">
                        <span class="receipt-card-icon"><i class="bi bi-file-earmark-text"></i></span>
                        <div><span>Receipt number</span><strong>{{ $receipt->invoice_no ?: '#'.$receipt->id }}</strong><small>{{ !empty($receipt->payment_date) ? \Carbon\Carbon::parse($receipt->payment_date)->format('d M Y') : 'Date unavailable' }} · {{ $receipt->payment_mode ?: 'Mode unavailable' }}</small></div>
                        <span class="receipt-status {{ $isReceived ? 'received' : 'pending' }}">{{ $isReceived ? 'Received' : 'Pending' }}</span>
                    </div>
                    <div class="receipt-amount-row"><div><span>Amount paid</span><strong>₹{{ number_format((float) $receipt->amount, 2) }}</strong></div><span class="receipt-heads">{{ $receipt->fee_head_names ?: 'Fee payment' }}</span></div>
                    @if((float) $receipt->discount > 0 || (float) $receipt->total_fine > 0)
                        <div class="receipt-adjustments">@if((float) $receipt->discount > 0)<span><i class="bi bi-tag"></i> Discount ₹{{ number_format((float) $receipt->discount, 2) }}</span>@endif @if((float) $receipt->total_fine > 0)<span><i class="bi bi-exclamation-circle"></i> Fine ₹{{ number_format((float) $receipt->total_fine, 2) }}</span>@endif</div>
                    @endif
                    <div class="receipt-actions">
                        <!-- <a href="{{ route('student.fees.receipt.view', $receipt->id) }}" target="_blank" rel="noopener"><i class="bi bi-eye"></i> View receipt</a> -->
                        <a href="{{ route('student.fees.receipt.download', $receipt->id) }}" class="download"><i class="bi bi-download"></i> Download PDF</a>
                    </div>
                </article>
            @empty
                <div class="fees-empty"><span><i class="bi bi-receipt"></i></span><h2>No receipts available</h2><p>Your payment receipts will appear here after a fee is received.</p></div>
            @endforelse
        </div>
    </div>
</section>

<style>
.student-fees-app{min-height:calc(100vh - 154px)!important;padding:0 0 28px!important;color:#253858!important;background:#f3f6fb!important;overflow:hidden!important}.fees-hero{position:relative!important;min-height:220px!important;padding:18px 15px 67px!important;overflow:hidden!important;color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4 55%,#172d73)!important;border-radius:0 0 29px 29px!important;box-shadow:0 15px 32px rgba(31,56,139,.2)!important}.fees-hero-pattern{position:absolute!important;inset:0!important;opacity:.11!important;background-image:linear-gradient(rgba(255,255,255,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.3) 1px,transparent 1px)!important;background-size:28px 28px!important}.fees-hero-head{position:relative!important;z-index:1!important;display:flex!important;align-items:flex-start!important;justify-content:space-between!important;gap:12px!important}.fees-hero-head>div{min-width:0!important}.fees-hero-head span{display:block!important;color:rgba(255,255,255,.67)!important;font-size:8px!important;font-weight:700!important;letter-spacing:.075em!important;text-transform:uppercase!important}.fees-hero-head h1{margin:3px 0!important;color:#fff!important;font-size:25px!important;line-height:1.2!important;font-weight:750!important;letter-spacing:-.02em!important}.fees-hero-head p{margin:0!important;color:rgba(255,255,255,.72)!important;font-size:8px!important}.fees-hero-icon{width:48px!important;height:48px!important;min-width:48px!important;border:1px solid rgba(255,255,255,.28)!important;border-radius:15px!important;display:grid!important;place-items:center!important;color:#3156d3!important;background:rgba(255,255,255,.94)!important;box-shadow:0 8px 20px rgba(13,31,83,.24)!important;font-size:22px!important}.fees-progress-copy{position:relative!important;z-index:1!important;margin:18px 1px 5px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;color:rgba(255,255,255,.7)!important;font-size:8px!important}.fees-progress-copy strong{color:#fff!important;font-size:9px!important}.fees-progress{position:relative!important;z-index:1!important;height:5px!important;border-radius:99px!important;overflow:hidden!important;background:rgba(255,255,255,.18)!important}.fees-progress span{display:block!important;height:100%!important;border-radius:99px!important;background:linear-gradient(90deg,#79e4b0,#c3f2db)!important}.fees-hero-stats{position:absolute!important;right:15px!important;bottom:14px!important;left:15px!important;z-index:1!important;display:grid!important;grid-template-columns:1fr 1fr!important;padding:10px 3px!important;border:1px solid rgba(255,255,255,.17)!important;border-radius:15px!important;background:rgba(15,35,90,.28)!important;backdrop-filter:blur(10px)!important}.fees-hero-stats>div{padding:0 12px!important}.fees-hero-stats>div+div{border-left:1px solid rgba(255,255,255,.16)!important}.fees-hero-stats span{display:block!important;color:rgba(255,255,255,.62)!important;font-size:7px!important;text-transform:uppercase!important;letter-spacing:.06em!important}.fees-hero-stats strong{display:block!important;margin-top:3px!important;color:#fff!important;font-size:11px!important;font-weight:700!important}
.fees-content{padding:0 13px!important}.fees-mini-stats{display:grid!important;grid-template-columns:1fr 1fr!important;gap:9px!important;margin-top:13px!important}.fees-mini-stats>div{min-width:0!important;padding:10px!important;border:1px solid #e7ebf3!important;border-radius:15px!important;display:flex!important;align-items:center!important;gap:9px!important;background:#fff!important;box-shadow:0 6px 18px rgba(42,55,92,.05)!important}.mini-stat-icon{width:34px!important;height:34px!important;min-width:34px!important;border-radius:11px!important;display:grid!important;place-items:center!important;font-size:15px!important}.mini-stat-icon.discount{color:#1089a0!important;background:#e4f7fa!important}.mini-stat-icon.fine{color:#d78a00!important;background:#fff4dc!important}.fees-mini-stats small{display:block!important;color:#98a2b3!important;font-size:7px!important;text-transform:uppercase!important;letter-spacing:.05em!important}.fees-mini-stats strong{display:block!important;margin-top:2px!important;color:#253858!important;font-size:10px!important}.fees-section-heading{display:flex!important;align-items:center!important;justify-content:space-between!important;margin:22px 2px 9px!important}.fees-section-heading>div>span{display:block!important;color:#26354e!important;font-size:14px!important;font-weight:750!important}.fees-section-heading small{display:block!important;margin-top:1px!important;color:#98a2b3!important;font-size:9px!important}.section-count{padding:4px 7px!important;border-radius:99px!important;color:#3156d3!important;background:#e8eeff!important;font-size:8px!important;font-weight:700!important}
.fee-head-card{margin-bottom:10px!important;border:1px solid #e6ebf3!important;border-radius:18px!important;color:#253858!important;background:#fff!important;box-shadow:0 7px 22px rgba(42,55,92,.055)!important;overflow:hidden!important}.fee-head-toggle{width:100%!important;min-height:66px!important;padding:11px 12px!important;border:0!important;display:flex!important;align-items:center!important;gap:9px!important;text-align:left!important;color:#253858!important;background:transparent!important}.fee-head-icon{width:39px!important;height:39px!important;min-width:39px!important;border-radius:12px!important;display:grid!important;place-items:center!important;color:#3156d3!important;background:#e8eeff!important;font-size:17px!important}.fee-head-copy{min-width:0!important;flex:1!important}.fee-head-copy strong{display:block!important;overflow:hidden!important;color:#253858!important;font-size:12px!important;line-height:1.3!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}.fee-head-copy small{display:block!important;margin-top:3px!important;color:#98a2b3!important;font-size:8px!important}.fee-head-status{padding:3px 6px!important;border-radius:99px!important;font-size:7px!important;font-weight:700!important}.fee-head-status.paid{color:#11845b!important;background:#e4f8ef!important}.fee-head-status.partial{color:#a66b00!important;background:#fff4dc!important}.fee-head-status.unpaid{color:#c84545!important;background:#ffeceb!important}.fee-head-chevron{color:#98a2b3!important;font-size:11px!important;transition:transform .2s ease!important}.fee-head-toggle[aria-expanded="true"] .fee-head-chevron{transform:rotate(180deg)!important}.fee-head-overview{display:grid!important;grid-template-columns:repeat(3,1fr)!important;margin:0 12px!important;padding:10px 0!important;border-top:1px solid #eef1f6!important}.fee-head-overview>div{min-width:0!important;text-align:center!important}.fee-head-overview>div+div{border-left:1px solid #eef1f6!important}.fee-head-overview span{display:block!important;color:#98a2b3!important;font-size:7px!important;text-transform:uppercase!important}.fee-head-overview strong{display:block!important;margin-top:3px!important;color:#253858!important;font-size:9px!important;font-weight:700!important;white-space:nowrap!important}.amount-paid{color:#139466!important}.amount-due{color:#d14d4d!important}.fee-head-progress{height:3px!important;margin:0 12px 11px!important;border-radius:99px!important;background:#edf0f5!important;overflow:hidden!important}.fee-head-progress span{display:block!important;height:100%!important;border-radius:99px!important;background:linear-gradient(90deg,#3156d3,#7590eb)!important}.fee-head-content{border-top:1px solid #eef1f6!important;padding:11px 12px 12px!important}.fee-extra-metrics{display:grid!important;grid-template-columns:1fr 1fr!important;gap:8px!important}.fee-extra-metrics>div{padding:8px!important;border-radius:10px!important;background:#f7f9fc!important}.fee-extra-metrics span{display:block!important;color:#98a2b3!important;font-size:7px!important}.fee-extra-metrics strong{display:block!important;margin-top:2px!important;color:#253858!important;font-size:9px!important}.transaction-heading{display:flex!important;justify-content:space-between!important;margin:14px 1px 7px!important}.transaction-heading span{color:#475467!important;font-size:9px!important;font-weight:700!important;text-transform:uppercase!important;letter-spacing:.05em!important}.transaction-heading small{color:#98a2b3!important;font-size:8px!important}.transaction-row{position:relative!important;min-height:51px!important;padding:8px 0!important;display:flex!important;align-items:center!important;gap:8px!important;border-top:1px solid #eef1f6!important}.transaction-icon{width:31px!important;height:31px!important;min-width:31px!important;border-radius:10px!important;display:grid!important;place-items:center!important;font-size:13px!important}.transaction-icon.received{color:#139466!important;background:#e4f8ef!important}.transaction-icon.pending{color:#d78a00!important;background:#fff4dc!important}.transaction-copy{min-width:0!important;flex:1!important}.transaction-copy strong{display:block!important;color:#344054!important;font-size:9px!important;font-weight:700!important}.transaction-copy small{display:block!important;margin-top:2px!important;overflow:hidden!important;color:#98a2b3!important;font-size:7px!important;text-overflow:ellipsis!important;white-space:nowrap!important}.transaction-amount{text-align:right!important}.transaction-amount strong{display:block!important;color:#253858!important;font-size:9px!important}.transaction-amount small{display:block!important;margin-top:2px!important;font-size:7px!important}.transaction-amount small.received{color:#139466!important}.transaction-amount small.pending{color:#d78a00!important}.transaction-meta{position:absolute!important;right:0!important;bottom:-2px!important;display:flex!important;gap:5px!important;color:#8a94a6!important;font-size:6px!important}.fee-inline-empty{padding:18px 8px!important;display:flex!important;flex-direction:column!important;align-items:center!important;gap:5px!important;color:#98a2b3!important;font-size:8px!important}.fee-inline-empty i{font-size:20px!important;color:#8295ca!important}
.receipts-heading{margin-top:24px!important}.receipt-card{margin-bottom:10px!important;padding:12px!important;border:1px solid #e6ebf3!important;border-radius:18px!important;color:#253858!important;background:#fff!important;box-shadow:0 7px 22px rgba(42,55,92,.055)!important}.receipt-card-head{display:flex!important;align-items:flex-start!important;gap:9px!important}.receipt-card-icon{width:39px!important;height:39px!important;min-width:39px!important;border-radius:12px!important;display:grid!important;place-items:center!important;color:#3156d3!important;background:#e8eeff!important;font-size:17px!important}.receipt-card-head>div{min-width:0!important;flex:1!important}.receipt-card-head>div>span{display:block!important;color:#98a2b3!important;font-size:7px!important;text-transform:uppercase!important}.receipt-card-head strong{display:block!important;margin-top:1px!important;color:#253858!important;font-size:11px!important;font-weight:700!important}.receipt-card-head small{display:block!important;margin-top:2px!important;overflow:hidden!important;color:#98a2b3!important;font-size:7px!important;text-overflow:ellipsis!important;white-space:nowrap!important}.receipt-status{padding:3px 6px!important;border-radius:99px!important;font-size:7px!important;font-weight:700!important}.receipt-status.received{color:#11845b!important;background:#e4f8ef!important}.receipt-status.pending{color:#a66b00!important;background:#fff4dc!important}.receipt-amount-row{margin-top:11px!important;padding:10px!important;border-radius:12px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:9px!important;background:#f7f9fc!important}.receipt-amount-row>div span{display:block!important;color:#98a2b3!important;font-size:7px!important}.receipt-amount-row>div strong{display:block!important;margin-top:2px!important;color:#253858!important;font-size:13px!important}.receipt-heads{max-width:52%!important;overflow:hidden!important;color:#6c788b!important;font-size:8px!important;text-align:right!important;text-overflow:ellipsis!important;white-space:nowrap!important}.receipt-adjustments{display:flex!important;gap:6px!important;flex-wrap:wrap!important;margin-top:8px!important}.receipt-adjustments span{padding:4px 6px!important;border-radius:8px!important;color:#7b8798!important;background:#f1f4f8!important;font-size:7px!important}.receipt-actions{display:grid!important;grid-template-columns:1fr 1fr!important;gap:7px!important;margin-top:11px!important}.receipt-actions a{min-height:39px!important;border:1px solid #dfe5ef!important;border-radius:11px!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:5px!important;color:#536176!important;text-decoration:none!important;background:#fff!important;font-size:8px!important;font-weight:650!important}.receipt-actions a.download{color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important;border-color:transparent!important;box-shadow:0 5px 12px rgba(49,86,211,.19)!important}.fees-empty{padding:34px 15px!important;border:1px dashed #dce2ec!important;border-radius:18px!important;text-align:center!important;background:#fff!important}.fees-empty>span{width:51px!important;height:51px!important;margin:0 auto 9px!important;border-radius:16px!important;display:grid!important;place-items:center!important;color:#7186c9!important;background:#eaf0ff!important;font-size:22px!important}.fees-empty h2{margin:0!important;color:#344054!important;font-size:12px!important;font-weight:700!important}.fees-empty p{margin:5px auto 0!important;max-width:240px!important;color:#98a2b3!important;font-size:8px!important;line-height:1.45!important}
:root[data-theme="dark"] .student-fees-app{color:#e7ecf4!important;background:#111827!important}:root[data-theme="dark"] .fees-mini-stats>div,:root[data-theme="dark"] .fee-head-card,:root[data-theme="dark"] .receipt-card,:root[data-theme="dark"] .fees-empty{color:#e7ecf4!important;background:#1b2433!important;border-color:#2c3748!important;box-shadow:none!important}:root[data-theme="dark"] .fees-mini-stats strong,:root[data-theme="dark"] .fees-section-heading>div>span,:root[data-theme="dark"] .fee-head-toggle,:root[data-theme="dark"] .fee-head-copy strong,:root[data-theme="dark"] .fee-head-overview strong,:root[data-theme="dark"] .fee-extra-metrics strong,:root[data-theme="dark"] .transaction-copy strong,:root[data-theme="dark"] .transaction-amount strong,:root[data-theme="dark"] .receipt-card-head strong,:root[data-theme="dark"] .receipt-amount-row>div strong,:root[data-theme="dark"] .fees-empty h2{color:#e7ecf4!important}:root[data-theme="dark"] .fee-head-overview,:root[data-theme="dark"] .fee-head-content,:root[data-theme="dark"] .transaction-row{border-color:#303b4c!important}:root[data-theme="dark"] .fee-extra-metrics>div,:root[data-theme="dark"] .receipt-amount-row{background:#222e42!important}:root[data-theme="dark"] .receipt-actions a{color:#aab4c3!important;background:#222e42!important;border-color:#354258!important}:root[data-theme="dark"] .receipt-actions a.download{color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important}
@media(max-width:359px){.fees-hero{padding-left:12px!important;padding-right:12px!important}.fees-hero-stats{left:12px!important;right:12px!important}.fees-content{padding-left:10px!important;padding-right:10px!important}.receipt-card,.fee-head-toggle{padding-left:10px!important;padding-right:10px!important}}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.fee-head-toggle').forEach(function(button){
        button.addEventListener('click',function(){
            var content=document.getElementById(button.dataset.target);
            var opening=content.classList.contains('d-none');
            content.classList.toggle('d-none',!opening);
            button.setAttribute('aria-expanded',opening?'true':'false');
        });
    });
});
</script>
@endsection
