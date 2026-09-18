@php
    $getSetting = Helper::getSetting();
    $sessionRow = DB::table('sessions')
        ->where('id', Session::get('session_id'))
        ->whereNull('deleted_at')
        ->first();
    $subjectCount = count($list_subject ?? []);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Wise Report</title>
    <style>
        :root {
            --report-gutter: 18px;
            --ink: #111;
            --header-grey: #e3e3e3;
            --light-grey: #f2f2f2;
            --lighter-grey: #fafafa;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            color: var(--ink);
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-shell {
            border: 1.5px solid #111;
            box-sizing: border-box;
            overflow: hidden;
            background: #fff;
        }

        .report-header {
            padding: 15px var(--report-gutter) 12px;
            box-sizing: border-box;
            color: var(--ink);
            background: var(--header-grey);
            border-bottom: 1px dotted #444;
        }

        .report-title {
            font-size: 25px;
            font-weight: 700;
            text-align: center;
            margin: 0;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .report-subtitle {
            text-align: center;
            font-size: 12px;
            margin: 6px 0 0;
            color: #333;
            line-height: 1.45;
        }

        .overview-grid,
        .detail-list {
            width: 100%;
            border-collapse: collapse;
        }

        .overview-wrap {
            margin: 10px var(--report-gutter);
        }

        .overview-grid {
            table-layout: fixed;
            border: 1px solid #111;
        }

        .overview-grid > tbody > tr > td {
            width: 50%;
            padding: 0;
            vertical-align: top;
            background: #fff;
        }

        .overview-grid > tbody > tr > td:first-child {
            border-right: 1px solid #111;
        }

        .overview-title {
            padding: 7px 10px;
            background: #dedede;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .55px;
            border-bottom: 1px solid #111;
        }

        .detail-list td {
            padding: 6px 9px;
            font-size: 11px;
            vertical-align: middle;
            border-bottom: 1px dotted #777;
        }

        .detail-list tr:last-child td {
            border-bottom: 0;
        }

        .detail-label {
            width: 38%;
            color: #444;
            font-size: 10px !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .35px;
            background: var(--lighter-grey);
        }

        .detail-value {
            color: #111;
            font-weight: 700;
        }

        .detail-value strong {
            font-size: 13px;
        }

        .detail-note {
            color: #555;
            font-size: 10px;
            font-weight: 400;
            margin-left: 5px;
        }

        .table-wrap {
            padding: 0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table.report-table {
            width: 100%;
            min-width: 1280px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table tfoot {
            display: table-footer-group;
        }

        .page-top-spacer th,
        .page-bottom-spacer td {
            height: 8mm;
            padding: 0 !important;
            background: #fff !important;
            border: 0 !important;
        }

        .report-table th,
        .report-table td {
            padding: 7px 8px;
            border-right: 1px solid #111;
            border-bottom: 1px solid #111;
        }

        .report-table thead th {
            background: #d8d8d8;
            color: #111;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
        }

        .report-table .subject-group {
            background: #cecece;
            padding: 6px 3px;
            font-size: 10px;
        }

        .report-table .metric-head {
            background: #e8e8e8;
            padding: 4px 2px;
            font-size: 9px;
            letter-spacing: .25px;
        }

        .report-table .metric-cell {
            padding: 5px 2px;
            font-size: 9.5px;
            text-align: center;
        }

        .report-table .marks-cell {
            font-weight: 700;
            background: #f1f1f1;
            border-right-width: 1.5px;
        }

        .report-table thead .header-cell {
            background: #fff;
            padding: 0;
            border: 0;
        }

        .header-inner {
            width: 100%;
            box-sizing: border-box;
        }

        .report-table tbody td {
            font-size: 11px;
            background: #fff;
        }

        .report-table tbody tr:nth-child(even) td {
            background: var(--lighter-grey);
        }

        .report-table tbody tr:hover td {
            background: #ededed;
        }

        .data-head th:last-child,
        .data-row td:last-child {
            border-right: 0;
        }

        .data-row:last-child td {
            border-bottom: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #64748b;
        }

        .empty-box {
            margin: 12px var(--report-gutter) 16px;
            padding: 24px 12px;
            text-align: center;
            color: #444;
            background: var(--light-grey);
            border: 1px dotted #333;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 100%;
                min-height: 100%;
                margin: 0;
                box-sizing: border-box;
            }

            body {
                padding: 0 8mm;
            }

            .report-shell {
                width: 100%;
                max-width: 281mm;
                margin: 0 auto;
                box-sizing: border-box;
                overflow: visible;
                border: 0;
            }

            .report-table thead {
                display: table-header-group;
            }

            .report-table tfoot {
                display: table-footer-group;
            }

            .report-table tbody tr {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 767.98px) {
            body {
                font-size: 12px;
                padding: 0;
            }

            .report-shell {
                border: 0;
            }

            .report-header {
                padding: 12px 14px 10px;
            }

            .report-title {
                font-size: 18px;
            }

            .report-subtitle {
                font-size: 11px;
            }

            .overview-wrap {
                min-width: 980px;
            }

            .report-table {
                min-width: 1280px;
            }

            .table-wrap {
                overflow-x: auto;
                overflow-y: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="report-shell">
        <div class="table-wrap">
            @if(empty($reportRows) || count($reportRows) === 0)
                <div class="empty-box">No report data found for the selected filters.</div>
            @else
                <table class="report-table">
                    <thead>
                        <tr class="page-top-spacer">
                            <th colspan="{{ $singleSubjectMode ? 10 : (($subjectCount * 4) + 8) }}"></th>
                        </tr>
                        <tr>
                            <th colspan="{{ $singleSubjectMode ? 10 : (($subjectCount * 4) + 8) }}" class="header-cell">
                                <div class="header-inner">
                                    <div class="report-header">
                                        <h1 class="report-title">{{ $getSetting['name'] ?? '' }}</h1>
                                        <p class="report-subtitle">
                                            {{ $getSetting['address'] ?? '' }}
                                            @if(!empty($getSetting['mobile']))
                                                | Phone: {{ $getSetting['mobile'] }}
                                            @endif
                                            @if(!empty($getSetting['gmail']))
                                                | Email: {{ $getSetting['gmail'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="overview-wrap">
                                        <table class="overview-grid">
                                            <tr>
                                                <td>
                                                    <div class="overview-title">Report Details</div>
                                                    <table class="detail-list">
                                                        <tr>
                                                            <td class="detail-label">Report</td>
                                                            <td class="detail-value">Exam Wise Report</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">Session</td>
                                                            <td class="detail-value">{{ $sessionRow->from_year ?? '' }} - {{ $sessionRow->to_year ?? '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">Exam</td>
                                                            <td class="detail-value">{{ $exam->name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">Class</td>
                                                            <td class="detail-value">{{ $className->name ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <div class="overview-title">Performance Summary</div>
                                                    <table class="detail-list">
                                                        <tr>
                                                            <td class="detail-label">Students</td>
                                                            <td class="detail-value">
                                                                <strong>{{ $summary['total_students'] ?? 0 }}</strong>
                                                                <span class="detail-note">Included in report</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">{{ $summary['topper_label'] ?? 'Topper' }}</td>
                                                            <td class="detail-value">
                                                                <strong>{{ $summary['topper_name'] ?? '-' }}</strong>
                                                                <span class="detail-note">Score: {{ $summary['topper_score'] ?? 0 }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">{{ $singleSubjectMode ? 'Subject Maximum' : 'Report Maximum' }}</td>
                                                            <td class="detail-value">
                                                                <strong>{{ $singleSubjectMode ? ($summary['single_subject_maximum'] ?? 0) : ($summary['report_maximum'] ?? 0) }}</strong>
                                                                <span class="detail-note">
                                                                    {{ $singleSubjectMode ? ($summary['single_subject_name'] ?? '-') : (($summary['selected_subject_count'] ?? 0) . ' selected subjects') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="detail-label">{{ $singleSubjectMode ? 'Average Marks' : 'Average Percentage' }}</td>
                                                            <td class="detail-value">
                                                                <strong>{{ $singleSubjectMode ? ($summary['average_marks'] ?? 0) : (($summary['average_percentage'] ?? 0) . '%') }}</strong>
                                                                <span class="detail-note">Across all students</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr class="data-head">
                            <th rowspan="2" style="width: 30px;">#</th>
                            <th rowspan="2" style="width: 72px;">Admission No.</th>
                            <th rowspan="2" style="width: 55px;">Roll No.</th>
                            <th rowspan="2" style="width: 145px;">Student Name</th>
                            @if($singleSubjectMode)
                                <th colspan="4" class="subject-group">{{ $list_subject->first()->name ?? 'Subject' }} ({{ $summary['single_subject_maximum'] ?? 0 }})</th>
                                <th rowspan="2" style="width: 58px;">Rank</th>
                                <th rowspan="2" style="width: 60px;">%</th>
                            @else
                                @foreach($list_subject as $subject)
                                    <th colspan="4" class="subject-group">{{ $subject->name }} ({{ $summary['subject_maximums'][$subject->id] ?? '-' }})</th>
                                @endforeach
                                <th rowspan="2" style="width: 60px;">Total</th>
                                <th rowspan="2" style="width: 66px;">Maximum</th>
                                <th rowspan="2" style="width: 55px;">Rank</th>
                                <th rowspan="2" style="width: 55px;">%</th>
                            @endif
                        </tr>
                        <tr class="data-head">
                            @foreach($list_subject as $subject)
                                <th class="metric-head">R</th>
                                <th class="metric-head">W</th>
                                <th class="metric-head">L</th>
                                <th class="metric-head" style="border-right-width: 1.5px;">Mk</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="page-bottom-spacer">
                            <td colspan="{{ $singleSubjectMode ? 10 : (($subjectCount * 4) + 8) }}"></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($reportRows as $index => $row)
                            <tr class="data-row">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $row['admission_no'] ?? '' }}</td>
                                <td class="text-center">{{ $row['roll_no'] ?? '-' }}</td>
                                <td>{{ $row['student_name'] ?? '' }}</td>

                                @if($singleSubjectMode)
                                    @php
                                        $subjectRow = $row['subject_rows'][0] ?? null;
                                    @endphp
                                    <td class="metric-cell">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                                    <td class="metric-cell">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                                    <td class="metric-cell">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                                    <td class="metric-cell marks-cell">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                                    <td class="text-center">{{ $row['subject_rank'] ?? '-' }}</td>
                                    <td class="text-center">{{ number_format((float) ($row['percentage'] ?? 0), 2) }}</td>
                                @else
                                    @foreach($row['subject_rows'] as $subjectRow)
                                        <td class="metric-cell">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                                        <td class="metric-cell">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                                        <td class="metric-cell">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                                        <td class="metric-cell marks-cell">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                                    @endforeach
                                    <td class="text-right">{{ number_format((float) ($row['total_obtained'] ?? 0), 2) }}</td>
                                    <td class="text-right">{{ number_format((float) ($row['total_maximum'] ?? 0), 2) }}</td>
                                    <td class="text-center">{{ $row['overall_rank'] ?? '-' }}</td>
                                    <td class="text-center">{{ number_format((float) ($row['percentage'] ?? 0), 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
<script type="text/javascript">
window.print();
</script>
</html>
