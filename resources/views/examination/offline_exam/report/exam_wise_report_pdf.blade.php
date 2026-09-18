<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Wise Report</title>
    <style>
        @page { margin: 5mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 7px; }
        table { width: 100%; border-collapse: collapse; }
        .summary { margin-bottom: 3mm; table-layout: fixed; }
        .summary > tbody > tr > td { width: 50%; padding: 0; vertical-align: top; border: 1px solid #111; }
        .summary-title { padding: 4px; background: #d8d8d8; text-align: center; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .detail td { padding: 4px 5px; border-top: 1px solid #111; }
        .detail .label { width: 38%; text-align: center; font-weight: bold; text-transform: uppercase; }
        .detail .value { text-align: center; }
        .detail .note { margin-left: 5px; color: #444; font-size: 6px; }
        .report { table-layout: fixed; }
        .report th, .report td { padding: 3px 2px; border: 1px solid #111; vertical-align: middle; line-height: 1.2; }
        .report thead { display: table-header-group; }
        .report th { background: #d8d8d8; text-align: center; font-weight: bold; }
        .report tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .right { text-align: right; }
        .student-name { white-space: normal; word-break: normal; overflow-wrap: normal; }
        .metric { padding-left: 1px !important; padding-right: 1px !important; text-align: center; font-size: 6px; white-space: nowrap; }
        .marks { background: #f1f1f1; font-weight: bold; }
    </style>
</head>
<body>
    @php $subjectCount = count($list_subject ?? []); @endphp

    <table class="summary">
        <tr>
            <td>
                <div class="summary-title">Report Details</div>
                <table class="detail">
                    <tr><td class="label">Report</td><td class="value"><strong>Exam Wise Report</strong></td></tr>
                    <tr><td class="label">Exam</td><td class="value"><strong>{{ $exam->name ?? '-' }}</strong></td></tr>
                    <tr><td class="label">Class</td><td class="value"><strong>{{ $className->name ?? '-' }}</strong></td></tr>
                </table>
            </td>
            <td>
                <div class="summary-title">Performance Summary</div>
                <table class="detail">
                    <tr><td class="label">Students</td><td class="value"><strong>{{ $summary['total_students'] ?? 0 }}</strong><span class="note">Included in report</span></td></tr>
                    <tr><td class="label">{{ $summary['topper_label'] ?? 'Topper' }}</td><td class="value"><strong>{{ $summary['topper_name'] ?? '-' }}</strong><span class="note">Score: {{ $summary['topper_score'] ?? 0 }}</span></td></tr>
                    <tr><td class="label">{{ $singleSubjectMode ? 'Subject Maximum' : 'Report Maximum' }}</td><td class="value"><strong>{{ $singleSubjectMode ? ($summary['single_subject_maximum'] ?? 0) : ($summary['report_maximum'] ?? 0) }}</strong></td></tr>
                    <tr><td class="label">{{ $singleSubjectMode ? 'Average Marks' : 'Average Percentage' }}</td><td class="value"><strong>{{ $singleSubjectMode ? ($summary['average_marks'] ?? 0) : (($summary['average_percentage'] ?? 0) . '%') }}</strong></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="report">
        <colgroup>
            <col style="width: 6mm;">
            <col style="width: 16mm;">
            <col style="width: 12mm;">
            <col style="width: 110mm;">
            @foreach($list_subject as $subject)
                <col><col><col><col>
            @endforeach
            @if($singleSubjectMode)
                <col style="width: 13mm;"><col style="width: 13mm;">
            @else
                <col style="width: 15mm;"><col style="width: 16mm;"><col style="width: 12mm;"><col style="width: 12mm;">
            @endif
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Admission No.</th>
                <th rowspan="2">Roll No.</th>
                <th rowspan="2">Student Name</th>
                @if($singleSubjectMode)
                    <th colspan="4">{{ $list_subject->first()->name ?? 'Subject' }} ({{ $summary['single_subject_maximum'] ?? 0 }})</th>
                    <th rowspan="2">Rank</th>
                    <th rowspan="2">%</th>
                @else
                    @foreach($list_subject as $subject)
                        <th colspan="4">{{ $subject->name }} ({{ $summary['subject_maximums'][$subject->id] ?? '-' }})</th>
                    @endforeach
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Maximum</th>
                    <th rowspan="2">Rank</th>
                    <th rowspan="2">%</th>
                @endif
            </tr>
            <tr>
                @foreach($list_subject as $subject)
                    <th class="metric">R</th><th class="metric">W</th><th class="metric">L</th><th class="metric">Mk</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($reportRows as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $row['admission_no'] ?? '' }}</td>
                    <td class="center">{{ $row['roll_no'] ?? '-' }}</td>
                    <td class="student-name">{{ $row['student_name'] ?? '' }}</td>
                    @if($singleSubjectMode)
                        @php $subjectRow = $row['subject_rows'][0] ?? null; @endphp
                        <td class="metric">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                        <td class="metric">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                        <td class="metric">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                        <td class="metric marks">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                        <td class="center">{{ $row['subject_rank'] ?? '-' }}</td>
                        <td class="center">{{ number_format((float) ($row['percentage'] ?? 0), 2) }}</td>
                    @else
                        @foreach($row['subject_rows'] as $subjectRow)
                            <td class="metric">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                            <td class="metric">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                            <td class="metric">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                            <td class="metric marks">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                        @endforeach
                        <td class="right">{{ number_format((float) ($row['total_obtained'] ?? 0), 2) }}</td>
                        <td class="right">{{ number_format((float) ($row['total_maximum'] ?? 0), 2) }}</td>
                        <td class="center">{{ $row['overall_rank'] ?? '-' }}</td>
                        <td class="center">{{ number_format((float) ($row['percentage'] ?? 0), 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $singleSubjectMode ? 10 : (($subjectCount * 4) + 8) }}" class="center">No report data found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
