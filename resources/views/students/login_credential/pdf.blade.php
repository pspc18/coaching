<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Credential Report</title>
    <style>
        @page { margin: 24px 28px 32px; }
        body { margin: 0; color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .organization { text-align: center; border-bottom: 2px solid #243b76; padding-bottom: 10px; }
        .organization h1 { margin: 0 0 5px; color: #243b76; font-size: 22px; }
        .organization p { margin: 2px 0; color: #4b5563; font-size: 10px; }
        .report-heading { margin: 13px 0 10px; text-align: center; }
        .report-heading h2 { margin: 0 0 4px; font-size: 16px; }
        .report-heading p { margin: 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th { background: #243b76; color: #fff; padding: 8px 6px; text-align: left; font-size: 10px; }
        td { border: 1px solid #d9dee8; padding: 7px 6px; vertical-align: middle; }
        tbody tr:nth-child(even) { background: #f5f7fb; }
        .serial { width: 35px; text-align: center; }
        .credential { font-family: DejaVu Sans Mono, monospace; color: #163f73; word-break: break-all; }
        .mobile { width: 86px; }
        .footer { position: fixed; right: 0; bottom: -20px; left: 0; color: #6b7280; text-align: center; font-size: 8px; }
    </style>
</head>
<body>
    <div class="organization">
        <h1>{{ $setting->name ?? 'Organization' }}</h1>
        @if(!empty($setting->address))<p>{{ $setting->address }}{{ !empty($setting->pincode) ? ' - '.$setting->pincode : '' }}</p>@endif
        @if(!empty($setting->mobile) || !empty($setting->gmail))
            <p>
                @if(!empty($setting->mobile))Phone: {{ $setting->mobile }}@endif
                @if(!empty($setting->mobile) && !empty($setting->gmail)) &nbsp;|&nbsp; @endif
                @if(!empty($setting->gmail))Email: {{ $setting->gmail }}@endif
            </p>
        @endif
    </div>

    <div class="report-heading">
        <h2>Student Login Credentials</h2>
        <p>List Type: {{ strtoupper($statusLabel ?? 'active') }} Students &nbsp; | &nbsp; Class: {{ $className }} &nbsp; | &nbsp; Total Students: {{ count($data) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="serial">S.No.</th>
                <th>Admission No.</th>
                <th>Attendance Unique ID</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Mobile No.</th>
                <th>Username</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td class="serial">{{ $loop->iteration }}</td>
                    <td class="credential">{{ $item->admissionNo ?: '-' }}</td>
                    <td class="credential">{{ $item->attendance_unique_id ?: ($item->unique_system_id ?: '-') }}</td>
                    <td>{{ trim(($item->first_name ?? '').' '.($item->last_name ?? '')) ?: '-' }}</td>
                    <td>{{ $item->father_name ?: '-' }}</td>
                    <td class="credential">{{ $item->mobile ?: '-' }}</td>
                    <td class="credential">{{ $item->userName ?: '-' }}</td>
                    <td class="credential">{{ $item->confirm_password ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Confidential {{ strtolower($statusLabel ?? 'active') }} student login credential report</div>
</body>
</html>
