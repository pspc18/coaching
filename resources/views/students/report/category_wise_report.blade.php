@php
    $session = DB::table('sessions')->where('id', Session::get('session_id'))->whereNull('deleted_at')->first();
    $sessionLabel = trim(($session->from_year ?? '') . '-' . ($session->to_year ?? ''));
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper report-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="report-hero mb-3">
                <div>
                    <span class="report-kicker">Student Analytics</span>
                    <h1>Category Wise Report</h1>
                    <p>Class-wise category distribution for the current session.</p>
                </div>
                <div class="report-actions">
                    <a href="{{ url('studentsDashboard') }}" class="btn btn-outline-light btn-sm"><i class="fa fa-arrow-left mr-1"></i> Back</a>
                    <button class="btn btn-light btn-sm" id="printFile"><i class="fa fa-print mr-1"></i> Print</button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-6 mb-3">
                    <div class="card report-card stat-card">
                        <div class="card-body">
                            <span class="stat-label">Session</span>
                            <strong>{{ $sessionLabel }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6 mb-3">
                    <div class="card report-card stat-card">
                        <div class="card-body">
                            <span class="stat-label">Total Boys</span>
                            <strong>{{ number_format($grandTotals['boys'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6 mb-3">
                    <div class="card report-card stat-card">
                        <div class="card-body">
                            <span class="stat-label">Total Girls</span>
                            <strong>{{ number_format($grandTotals['girls'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card report-card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Enrollment matrix</h3>
                        <p>Category totals by class</p>
                    </div>
                    <span class="report-count">{{ number_format($grandTotals['total'] ?? 0) }} students</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive report-table-wrap" id="downloadLeaflet">
                        <table class="table table-bordered mb-0 report-table category-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:70px">S.No.</th>
                                    <th rowspan="2">Class Name</th>
                                    @foreach ($categories as $cat)
                                        <th colspan="3" class="text-center">{{ $cat }}</th>
                                    @endforeach
                                    <th colspan="3" class="text-center">Total Enrollment</th>
                                </tr>
                                <tr>
                                    @foreach ($categories as $cat)
                                        <th>Boys</th>
                                        <th>Girls</th>
                                        <th>Total</th>
                                    @endforeach
                                    <th>Total Boys</th>
                                    <th>Total Girls</th>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classType as $type)
                                    @php $summary = $classTotals[$type->id] ?? ['boys' => 0, 'girls' => 0, 'total' => 0, 'categories' => []]; @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $type->name ?? '' }}</td>
                                        @foreach ($categories as $cat)
                                            @php $cell = $summary['categories'][$cat] ?? ['boys' => 0, 'girls' => 0, 'total' => 0]; @endphp
                                            <td>{{ $cell['boys'] }}</td>
                                            <td>{{ $cell['girls'] }}</td>
                                            <td>{{ $cell['total'] }}</td>
                                        @endforeach
                                        <td><b>{{ $summary['boys'] }}</b></td>
                                        <td><b>{{ $summary['girls'] }}</b></td>
                                        <td><b>{{ $summary['total'] }}</b></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + (count($categories) * 3) + 3 }}" class="text-center py-4 text-muted">No class records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td><b>Total</b></td>
                                    @foreach ($categories as $cat)
                                        <td><b>{{ $classType->sum(fn ($type) => ($classTotals[$type->id]['categories'][$cat]['boys'] ?? 0)) }}</b></td>
                                        <td><b>{{ $classType->sum(fn ($type) => ($classTotals[$type->id]['categories'][$cat]['girls'] ?? 0)) }}</b></td>
                                        <td><b>{{ $classType->sum(fn ($type) => ($classTotals[$type->id]['categories'][$cat]['total'] ?? 0)) }}</b></td>
                                    @endforeach
                                    <td><b>{{ $grandTotals['boys'] ?? 0 }}</b></td>
                                    <td><b>{{ $grandTotals['girls'] ?? 0 }}</b></td>
                                    <td><b>{{ $grandTotals['total'] ?? 0 }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.report-page{background:#f4f7fb}
.report-hero{background:linear-gradient(120deg,#233b75,#365bb6);border-radius:12px;padding:18px 20px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 8px 22px rgba(29,55,113,.14)}
.report-kicker{font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.72}
.report-hero h1{font-size:22px;line-height:1.15;margin:4px 0 2px;font-weight:700}
.report-hero p{margin:0;font-size:13px;opacity:.82}
.report-actions{display:flex;gap:8px;flex-wrap:wrap}
.report-card{border:0;border-radius:12px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}
.report-card .card-header{background:#fff;border-bottom:1px solid #edf0f5;padding:12px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.report-card .card-title{margin:0;font-size:14px;font-weight:700;color:#2c3852;float:none}
.report-card .card-header p{font-size:11px;color:#8792a5;margin:2px 0 0}
.report-count{font-size:11px;padding:4px 8px;border-radius:999px;background:#eef3ff;color:#4361ee}
.stat-card .card-body{padding:14px 15px}
.stat-card .stat-label{display:block;font-size:11px;color:#8792a5;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em}
.stat-card strong{font-size:24px;color:#2c3852;line-height:1.1}
.report-table-wrap{max-height:68vh}
.report-table{font-size:12px}
.report-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
.report-table{min-width:1180px}
.report-table thead th{position:sticky;top:0;z-index:2;background:#f8fafc;border-bottom:1px solid #edf0f5;padding:10px 12px;font-size:12px;white-space:nowrap;text-align:center}
.report-table td{padding:9px 12px;vertical-align:middle;border-color:#edf0f5;text-align:center}
.report-table tbody tr:hover{background:#f9fbff}
.report-table tfoot td{background:#f8fafc}
.category-table td:nth-child(2), .category-table th:nth-child(2){text-align:left}
@media(max-width:575px){
    .report-hero{padding:16px;align-items:flex-start;flex-direction:column}
    .report-hero h1{font-size:20px}
    .report-actions{width:100%}
    .report-actions .btn{flex:1 1 calc(50% - 4px);width:auto}
    .report-card .card-header{padding:11px 12px;flex-direction:column;align-items:flex-start}
    .report-card .card-body{padding:11px 12px}
    .stat-card .card-body{padding:12px}
    .stat-card strong{font-size:18px}
    .report-table{min-width:1040px;font-size:11px}
    .report-count{align-self:flex-start}
    .report-card .card-header .report-count{margin-top:4px}
    .report-card .card-header > div{width:100%}
    .report-card .card-header > div p{white-space:normal}
}
</style>

<script>
$(document).ready(function() {
    $("#printFile").click(function() {
        var styles = '';
        $('style, link[rel="stylesheet"]').each(function() {
            styles += $(this).prop('outerHTML');
        });
        var content = $("#downloadLeaflet").html();
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Category Wise Report</title>' + styles + '</head><body style="margin:20px;">');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    });
});
</script>
@endsection
