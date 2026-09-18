@extends('layout.app')

@php
    $studentCount = $data ? 1 : 0;
    $examCount = count($examIds ?? []);
    $subjectCount = count($subjectIds ?? []);
    $otherCount = count($otherIds ?? []);
    $profileImage = !empty($data->image)
        ? env('IMAGE_SHOW_PATH').'profile/'.$data->image
        : env('IMAGE_SHOW_PATH').'default/user_image.jpg';
@endphp

@section('content')
<div class="content-wrapper performance-detail-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="detail-hero mb-3">
                <div class="detail-hero__main">
                    <span class="detail-kicker">Academic Performance</span>
                    <h1>Student Performance Detail</h1>
                    <p>Same report flow, reorganized for faster review on desktop and mobile.</p>
                </div>
                <div class="detail-hero__actions">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back
                    </a>
                    @if($examCount > 0)
                        <button class="btn btn-light btn-sm" type="button" onclick="printTable()">
                            <i class="fa fa-print mr-1"></i> Print
                        </button>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card perf-mini-card">
                        <div class="card-body">
                            <span class="mini-label">Exams</span>
                            <strong>{{ $examCount }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card perf-mini-card">
                        <div class="card-body">
                            <span class="mini-label">Subjects</span>
                            <strong>{{ $subjectCount }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card perf-mini-card">
                        <div class="card-body">
                            <span class="mini-label">Other</span>
                            <strong>{{ $otherCount }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card perf-mini-card">
                        <div class="card-body">
                            <span class="mini-label">Attendance</span>
                            <strong>{{ $attendance }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-9 mb-3">
                    <div class="card performance-card h-100">
                        <div class="card-header performance-card__header">
                            <div>
                                <h3 class="card-title">
                                    <i class="fa fa-chart-bar mr-1"></i> Average Subjects Score
                                </h3>
                                <p>Open the printed table or review the responsive cards below</p>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @if($examCount > 0)
                                <div class="student-profile-strip">
                                    <img src="{{ $profileImage }}" alt="student profile" class="student-avatar" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'">
                                    <div class="student-profile-strip__meta">
                                        <h4>{{ $data->first_name ?? '' }} {{ $data->last_name ?? '' }}</h4>
                                        <div class="profile-subtext">
                                            <span>Mobile: {{ $data->mobile ?? '' }}</span>
                                            <span>Father: {{ $data->father_name ?? '' }}</span>
                                            <span>Mother: {{ $data->mother_name ?? '' }}</span>
                                            <span>Father Mobile: {{ $data->father_mobile ?? '' }}</span>
                                            <span>Attendance: {{ $attendance }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive performance-table-wrap d-none d-md-block">
                                    <table id="printTable" class="table table-bordered table-striped performance-table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="min-width:180px">Exam</th>
                                                @foreach($subjectIds as $subjectId)
                                                    <th style="min-width:170px">{{ $subjectList[$subjectId] ?? 'Subject' }}</th>
                                                @endforeach
                                                @foreach($otherIds as $otherId)
                                                    <th style="min-width:170px">{{ $otherList[$otherId] ?? 'Other' }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examIds as $examId)
                                                <tr>
                                                    <td class="exam-name">
                                                        {{ $examList[$examId] ?? 'Exam' }}
                                                    </td>
                                                    @foreach($subjectIds as $subjectId)
                                                        @php
                                                            $cell = $performanceMatrix[$examId][$subjectId] ?? ['student_marks' => 0, 'max_marks' => 0, 'percentage' => null];
                                                        @endphp
                                                        <td>
                                                            <div class="mark-stack">
                                                                <span class="text-success">Obtained = {{ $cell['student_marks'] ?? 0 }}</span>
                                                                <span class="text-danger">Maximum = {{ $cell['max_marks'] ?? 0 }}</span>
                                                                <span class="text-info">Percentage = {{ $cell['percentage'] !== null ? $cell['percentage'] : 'N/A' }}%</span>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                    @foreach($otherIds as $otherId)
                                                        @php
                                                            $cell = $otherPerformanceMatrix[$examId][$otherId] ?? ['student_marks' => 0];
                                                        @endphp
                                                        <td>
                                                            <div class="mark-stack">
                                                                <span class="text-success">Obtained = {{ $cell['student_marks'] ?? 0 }}</span>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mobile-exam-list d-md-none">
                                    @foreach($examIds as $examId)
                                        <div class="mobile-exam-card">
                                            <div class="mobile-exam-card__head">
                                                <div>
                                                    <h4>{{ $examList[$examId] ?? 'Exam' }}</h4>
                                                    <p>Subject-wise performance</p>
                                                </div>
                                            </div>
                                            <div class="mobile-score-grid">
                                                @foreach($subjectIds as $subjectId)
                                                    @php
                                                        $cell = $performanceMatrix[$examId][$subjectId] ?? ['student_marks' => 0, 'max_marks' => 0, 'percentage' => null];
                                                    @endphp
                                                    <div class="mobile-score-item">
                                                        <span>{{ $subjectList[$subjectId] ?? 'Subject' }}</span>
                                                        <strong>{{ $cell['percentage'] !== null ? $cell['percentage'] : 'N/A' }}%</strong>
                                                        <small>O: {{ $cell['student_marks'] ?? 0 }} / M: {{ $cell['max_marks'] ?? 0 }}</small>
                                                    </div>
                                                @endforeach
                                                @foreach($otherIds as $otherId)
                                                    @php
                                                        $cell = $otherPerformanceMatrix[$examId][$otherId] ?? ['student_marks' => 0];
                                                    @endphp
                                                    <div class="mobile-score-item">
                                                        <span>{{ $otherList[$otherId] ?? 'Other' }}</span>
                                                        <strong>{{ $cell['student_marks'] ?? 0 }}</strong>
                                                        <small>Obtained marks</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    No exam performance data was found for this student.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class="card performance-card">
                        <div class="card-header performance-card__header">
                            <div>
                                <h3 class="card-title">Student info</h3>
                                <p>Quick summary card</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-stack">
                                <div class="info-row">
                                    <span>Name</span>
                                    <strong>{{ $data->first_name ?? '' }} {{ $data->last_name ?? '' }}</strong>
                                </div>
                                <div class="info-row">
                                    <span>Mobile</span>
                                    <strong>{{ $data->mobile ?? '' }}</strong>
                                </div>
                                <div class="info-row">
                                    <span>Father</span>
                                    <strong>{{ $data->father_name ?? '' }}</strong>
                                </div>
                                <div class="info-row">
                                    <span>Mother</span>
                                    <strong>{{ $data->mother_name ?? '' }}</strong>
                                </div>
                                <div class="info-row">
                                    <span>Father Mobile</span>
                                    <strong>{{ $data->father_mobile ?? '' }}</strong>
                                </div>
                                <div class="info-row">
                                    <span>Attendance</span>
                                    <strong>{{ $attendance }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.performance-detail-page{background:#f4f7fb}
.detail-hero{background:linear-gradient(120deg,#233b75,#365bb6);border-radius:14px;padding:18px 20px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 8px 22px rgba(29,55,113,.14)}
.detail-kicker{font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.72}
.detail-hero h1{font-size:22px;line-height:1.15;margin:4px 0 2px;font-weight:700}
.detail-hero p{margin:0;font-size:13px;opacity:.82;max-width:560px}
.detail-hero__actions{display:flex;gap:8px;flex-wrap:wrap}
.performance-card{border:0;border-radius:14px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}
.performance-card__header{background:#fff;border-bottom:1px solid #edf0f5;padding:12px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.performance-card .card-title{margin:0;font-size:14px;font-weight:700;color:#2c3852;float:none}
.performance-card__header p{font-size:11px;color:#8792a5;margin:2px 0 0}
.perf-mini-card{border:0;border-radius:14px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}
.perf-mini-card .card-body{padding:14px 15px}
.mini-label{display:block;font-size:11px;color:#8792a5;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em}
.perf-mini-card strong{font-size:24px;color:#2c3852;line-height:1.1}
.student-profile-strip{display:flex;gap:14px;align-items:center;padding:14px 15px;border-bottom:1px solid #edf0f5;background:#fbfcff}
.student-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 6px 16px rgba(36,52,82,.12)}
.student-profile-strip__meta h4{margin:0 0 6px;font-size:16px;font-weight:800;color:#23324d}
.profile-subtext{display:flex;flex-wrap:wrap;gap:8px 14px;font-size:12px;color:#5d6b82}
.performance-table-wrap{overflow:auto;-webkit-overflow-scrolling:touch}
.performance-table{font-size:12px;min-width:920px}
.performance-table thead th{position:sticky;top:0;z-index:2;background:#f8fafc;border-bottom:1px solid #edf0f5;padding:10px 12px;font-size:12px;white-space:nowrap}
.performance-table td{vertical-align:top;padding:10px 12px;border-color:#edf0f5}
.exam-name{font-weight:700;color:#23324d;white-space:nowrap}
.mark-stack{display:flex;flex-direction:column;gap:3px;line-height:1.35}
.mobile-exam-list{display:flex;flex-direction:column;gap:10px;padding:12px}
.mobile-exam-card{border:1px solid #edf0f5;border-radius:14px;background:#fff;padding:12px;box-shadow:0 2px 10px rgba(36,52,82,.04)}
.mobile-exam-card__head h4{margin:0;font-size:14px;font-weight:800;color:#23324d}
.mobile-exam-card__head p{margin:3px 0 0;font-size:11px;color:#8792a5}
.mobile-score-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px}
.mobile-score-item{border:1px solid #edf0f5;border-radius:12px;padding:10px 12px;background:#f9fbff}
.mobile-score-item span{display:block;font-size:10px;color:#8792a5;text-transform:uppercase;letter-spacing:.03em;margin-bottom:3px}
.mobile-score-item strong{display:block;font-size:16px;color:#23324d}
.mobile-score-item small{display:block;font-size:11px;color:#5d6b82;margin-top:2px}
.info-stack{display:flex;flex-direction:column;gap:10px}
.info-row{border:1px solid #edf0f5;border-radius:12px;padding:10px 12px;background:#f9fbff}
.info-row span{display:block;font-size:10px;color:#8792a5;text-transform:uppercase;letter-spacing:.03em;margin-bottom:3px}
.info-row strong{display:block;font-size:13px;color:#23324d;line-height:1.3}
.empty-state{padding:28px 16px;text-align:center;color:#7f8a9d;font-size:13px}
@media(max-width:575px){
    .detail-hero{padding:16px;align-items:flex-start;flex-direction:column}
    .detail-hero h1{font-size:20px}
    .detail-hero__actions{width:100%}
    .detail-hero__actions .btn{width:100%}
    .performance-card__header{padding:11px 12px;flex-direction:column;align-items:flex-start}
    .performance-card .card-body{padding:0}
    .perf-mini-card .card-body{padding:12px}
    .perf-mini-card strong{font-size:21px}
    .student-profile-strip{padding:12px;align-items:flex-start}
    .student-avatar{width:60px;height:60px}
    .profile-subtext{gap:6px 10px}
    .mobile-score-grid{grid-template-columns:1fr}
}
</style>

<script>
    function printTable() {
        var table = document.getElementById('printTable');
        if (!table) return;

        var printWindow = window.open('', '', 'height=700,width=1000');
        var printStyle = `
            <style>
                body{font-family:Arial,sans-serif;padding:20px;color:#222}
                table{width:100%;border-collapse:collapse}
                th,td{border:1px solid #ddd;padding:8px;vertical-align:top}
                th{background:#f2f4f8;text-align:left}
                .mark-stack{display:flex;flex-direction:column;gap:3px}
            </style>
        `;

        printWindow.document.write('<html><head><title>Print Table</title>');
        printWindow.document.write(printStyle);
        printWindow.document.write('</head><body>');
        printWindow.document.write(table.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }
</script>
@endsection
