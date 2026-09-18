<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Today's Attendance Report</title>
    <style>
        @page { margin: 22px 20px 18px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 11px; }
        .report-title { text-align: center; margin: 12px 0 8px; }
        .report-title h2 { margin: 0; font-size: 18px; }
        .report-title p { margin: 4px 0 0; font-size: 11px; color: #6b7280; }
        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 12px 0 14px;
            font-size: 11px;
        }
        .meta-box {
            flex: 1;
            border: 1px solid #dbe3ee;
            border-radius: 8px;
            padding: 8px 10px;
            background: #f9fbfd;
        }
        .summary-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }
        .summary-card {
            flex: 1;
            border: 1px solid #dbe3ee;
            border-radius: 8px;
            padding: 8px 10px;
            background: #fff;
        }
        .summary-card b { display: block; font-size: 16px; margin-top: 3px; }
        .summary-card small { color: #6b7280; }
        .notice {
            margin: 8px 0 12px;
            padding: 9px 11px;
            border-radius: 8px;
            background: #fff7e6;
            color: #8a6507;
            border: 1px solid #f1d28a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #d8e0ea;
            padding: 7px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #eef3f8;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .status {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
        }
        .status-absent { background: #fde8ea; color: #b4232c; }
        .status-not-marked { background: #eef2ff; color: #3448c5; }
        .muted { color: #6b7280; }
        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $getSetting = $getSetting ?? Helper::getSetting();
    @endphp

    @include('print_file.print_header')

    <div class="report-title">
        <h2>Today's Attendance Absent / Not Marked Report</h2>
        <p>{{ $reportDate->format('d M Y') }}</p>
    </div>

    <div class="meta-row">
        <div class="meta-box">
            <b>Report Date</b><br>
            {{ $reportDate->format('d-m-Y') }}
        </div>
        <div class="meta-box">
            <b>Absent Students</b><br>
            {{ number_format($absentCount) }}
        </div>
        <div class="meta-box">
            <b>Not Marked Students</b><br>
            {{ number_format($notMarkedCount) }}
        </div>
    </div>

    @if($isHoliday)
        <div class="notice">
            Today is marked as a holiday or event in the academic calendar. No absent or not marked students are listed.
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">S.No.</th>
                <th style="width: 11%;">Class</th>
                <th style="width: 11%;">Admission No</th>
                <th style="width: 13%;">Attendance UID</th>
                <th style="width: 22%;">Student Name</th>
                <th style="width: 19%;">Father Name</th>
                <th style="width: 10%;">Mobile</th>
                <th style="width: 9%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['class_name'] ?: '-' }}</td>
                    <td>{{ $row['admission_no'] ?: '-' }}</td>
                    <td>{{ $row['attendance_unique_id'] ?: '-' }}</td>
                    <td>{{ $row['name'] ?: '-' }}</td>
                    <td>{{ $row['father_name'] ?: '-' }}</td>
                    <td>{{ $row['mobile'] ?: '-' }}</td>
                    <td class="text-center">
                        <span class="status {{ $row['status'] === 'Absent' ? 'status-absent' : 'status-not-marked' }}">
                            {{ $row['status'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center muted" style="padding: 18px 8px;">
                        No absent or not marked students found for today.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y, h:i A') }}.
    </div>
</body>
</html>
