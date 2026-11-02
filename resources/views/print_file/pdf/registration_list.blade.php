<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Enquiry & Registration Report</title>
    <style>
        @page {
            margin: 20px 22px 25px 22px;
            size: A4 portrait;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-wrap {
            margin-bottom: 8px;
        }
        .report-banner {
            background-color: #002C54;
            color: #ffffff;
            padding: 7px 10px;
            border-radius: 3px;
            margin: 8px 0 10px;
            text-align: center;
        }
        .report-title {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .report-subtitle {
            margin: 2px 0 0;
            font-size: 9px;
            color: #93c5fd;
            font-weight: normal;
        }
        .meta-strip {
            width: 100%;
            margin-bottom: 8px;
            font-size: 9px;
            color: #475569;
        }
        .meta-strip td {
            padding: 2px 0;
        }

        table.enquiry-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.enquiry-table thead {
            display: table-header-group;
        }
        table.enquiry-table tr {
            page-break-inside: avoid;
        }
        table.enquiry-table th {
            background-color: #002C54;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 5px 4px;
            border: 1px solid #001a33;
            text-transform: uppercase;
            text-align: left;
        }
        table.enquiry-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-size: 9px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }
        table.enquiry-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }

        /* Status Pill */
        .status-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .status-active { background: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
        .status-admission { background: #dbeafe; color: #1e40af; border: 0.5px solid #93c5fd; }
        .status-new { background: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
        .status-missed { background: #fee2e2; color: #991b1b; border: 0.5px solid #fca5a5; }
        .status-closed { background: #f1f5f9; color: #475569; border: 0.5px solid #cbd5e1; }

        .footer-note {
            margin-top: 12px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    @php
        $getSetting = Helper::getSetting();
        $totalRecords = is_countable($data ?? []) ? count($data ?? []) : 0;
    @endphp

    <div class="header-wrap">
        @include('print_file.print_header')
    </div>

    <div class="report-banner">
        <h2 class="report-title">Student Enquiry &amp; Registration Report</h2>
        <div class="report-subtitle">Official Student Inquiry Records | Arise ERP System</div>
    </div>

    <table class="meta-strip">
        <tr>
            <td style="width: 50%;">
                <b>Report Date:</b> {{ date('d-M-Y h:i A') }}
            </td>
            <td style="width: 50%; text-align: right;">
                <b>Total Enquiries:</b> {{ $totalRecords }}
            </td>
        </tr>
    </table>

    <table class="enquiry-table">
        <thead>
            <tr>
                <th style="width: 24px;" class="text-center">#</th>
                <th style="width: 50px;" class="text-center">Reg. No</th>
                <th style="width: 95px;">Student Name</th>
                <th style="width: 65px;">Class</th>
                <th style="width: 85px;">Father Name</th>
                <th style="width: 65px;">Mobile No</th>
                <th style="width: 55px;" class="text-center">Reg. Date</th>
                <th style="width: 55px;" class="text-center">Follow Up</th>
                <th style="width: 55px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data) && count($data) > 0)
                @php $i = 1; @endphp
                @foreach ($data as $item)
                    @php
                        $fullName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
                        if (empty($fullName)) $fullName = 'Enquiry #' . ($item->id ?? '');

                        $regNo = $item['registration_no'] ?? ($item['id_number'] ?? ($item->id ?? '-'));
                        $className = $item['class_name'] ?? '-';
                        if (!empty($item['section_name'])) {
                            $className .= ' (' . $item['section_name'] . ')';
                        }
                        $fatherName = $item['father_name'] ?? '-';
                        $mobile = $item['mobile'] ?? '-';

                        $regDate = !empty($item['registration_date']) ? date('d-M-Y', strtotime($item['registration_date'])) : '-';
                        $followupDate = !empty($item->latest_followup_date) ? date('d-M-Y', strtotime($item->latest_followup_date)) : (!empty($item['next_follow_up_date']) ? date('d-M-Y', strtotime($item['next_follow_up_date'])) : '-');

                        $latestStatus = $item->latest_status ?? ($item['status'] ?? 'New');
                        $adStatus = ($item->ad_status ?? '') == 'Admission' ? 'Admission' : '';
                        $statusSlug = strtolower($latestStatus);

                        $badgeClass = match(true) {
                            $adStatus === 'Admission' || $statusSlug === 'admission' => 'status-admission',
                            in_array($statusSlug, ['active', 'follow up', 'followup']) => 'status-active',
                            $statusSlug === 'new' => 'status-new',
                            $statusSlug === 'missed' => 'status-missed',
                            $statusSlug === 'closed' => 'status-closed',
                            default => 'status-active'
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-center font-bold">{{ $regNo }}</td>
                        <td class="font-bold">{{ $fullName }}</td>
                        <td>{{ $className }}</td>
                        <td>{{ $fatherName }}</td>
                        <td>{{ $mobile }}</td>
                        <td class="text-center">{{ $regDate }}</td>
                        <td class="text-center">{{ $followupDate }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $badgeClass }}">
                                {{ $adStatus === 'Admission' ? 'Admitted' : $latestStatus }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center" style="padding: 16px; color: #64748b;">
                        No enquiry records found.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-note">
        This is an official computer-generated document from {{ $getSetting['name'] ?? 'Arise ERP' }}. Generated on {{ date('d-M-Y H:i:s') }}.
    </div>

</body>
</html>
