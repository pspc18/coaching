@php
    $getSetting = Helper::getSetting();
    $employeeName = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
    $designation = $data['role_name'] ?? 'Staff Member';
    $joiningDateValue = $data['joining_date'] ?? $data['staff_joining_date'] ?? null;
    $dobValue = $data['dob'] ?? $data['staff_dob'] ?? null;
    $joiningDate = !empty($joiningDateValue) ? date('d F Y', strtotime($joiningDateValue)) : 'Not provided';
    $dob = !empty($dobValue) ? date('d F Y', strtotime($dobValue)) : 'Not provided';
    $joiningAmount = isset($data['joining_amount']) && $data['joining_amount'] !== null && $data['joining_amount'] !== ''
        ? (float) $data['joining_amount']
        : null;
    $formattedJoiningAmount = $joiningAmount !== null ? 'INR '.number_format($joiningAmount, 2) : 'Not assigned';
    $letterDate = date('d F Y');
    $referenceNo = 'JL/'.date('Y').'/'.str_pad($data['id'] ?? 0, 4, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joining Letter - {{ $employeeName }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: A4 portrait; margin: 0; }
        body {
            margin: 0;
            background: #eef1f5;
            color: #263238;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
            line-height: 1.65;
        }
        .toolbar {
            width: 210mm;
            max-width: calc(100% - 24px);
            margin: 14px auto 0;
            text-align: right;
        }
        .print-button {
            border: 0;
            border-radius: 5px;
            padding: 9px 18px;
            background: #173f5f;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }
        .letter {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 14px auto 28px;
            padding: 15mm 17mm 13mm;
            background: #fff;
            box-shadow: 0 5px 25px rgba(22, 40, 55, .12);
            overflow: hidden;
        }
        .letter::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 7px;
            background: linear-gradient(90deg, #102f4a 0 68%, #c6a15b 68% 100%);
        }
        .letter-header {
            display: table;
            width: 100%;
            padding-bottom: 14px;
            border-bottom: 1px solid #d8dee4;
        }
        .letter-header::after {
            content: "OFFICIAL COMMUNICATION";
            position: absolute;
            top: 12px;
            right: 17mm;
            color: #8a96a0;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1.8px;
        }
        .logo-wrap, .organization { display: table-cell; vertical-align: middle; }
        .logo-wrap { width: 105px; }
        .logo-wrap img { display: block; max-width: 88px; max-height: 82px; }
        .organization { text-align: right; }
        .organization h1 {
            margin: 0 0 3px;
            color: #173f5f;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 27px;
            line-height: 1.2;
            letter-spacing: .3px;
        }
        .organization p { margin: 1px 0; color: #596773; font-size: 12px; line-height: 1.45; }
        .contact-line { color: #344955 !important; font-weight: 600; }
        .document-heading { text-align: center; margin: 12px 0 12px; }
        .document-heading h2 {
            display: inline-block;
            margin: 0;
            padding: 0 5px 4px;
            border-bottom: 2px solid #c59b3d;
            color: #173f5f;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 22px;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }
        .document-heading p {
            margin: 3px 0 0;
            color: #7a8790;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .meta { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .meta td { padding: 1px 0; font-size: 13px; }
        .meta td:last-child { text-align: right; }
        .recipient { margin-bottom: 12px; }
        .recipient strong { color: #173f5f; font-size: 15px; }
        .subject {
            margin: 12px 0;
            padding: 9px 12px;
            border-left: 4px solid #c59b3d;
            background: #f6f8fa;
            font-weight: 700;
            color: #173f5f;
        }
        .content p { margin: 0 0 9px; text-align: justify; }
        .details {
            width: 100%;
            margin: 12px 0;
            border-collapse: collapse;
            border: 1px solid #d7dde2;
        }
        .details th, .details td { padding: 8px 11px; border-bottom: 1px solid #e1e5e9; text-align: left; }
        .details tr:last-child th, .details tr:last-child td { border-bottom: 0; }
        .details th { width: 24%; background: #f5f7f9; color: #405361; font-size: 12px; text-transform: uppercase; letter-spacing: .35px; }
        .details td { width: 26%; font-weight: 600; }
        .compensation {
            position: relative;
            margin: 12px 0;
            padding: 11px 17px 11px 62px;
            border: 1px solid #dfd4bd;
            border-radius: 7px;
            background: linear-gradient(135deg, #fffdf8, #faf6ed);
        }
        .compensation::before {
            content: "\20B9";
            position: absolute;
            left: 17px;
            top: 50%;
            width: 30px;
            height: 30px;
            margin-top: -15px;
            border-radius: 50%;
            background: #c6a15b;
            color: #fff;
            font: 700 18px/30px Georgia, serif;
            text-align: center;
        }
        .compensation-label {
            display: block;
            color: #786849;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .compensation-value { color: #173f5f; font-size: 18px; font-weight: 700; }
        .compensation-note { color: #77828a; font-size: 10px; }
        .signatures {
            display: table;
            position: absolute;
            left: 17mm;
            right: 17mm;
            bottom: 22mm;
            width: calc(100% - 34mm);
            margin: 0;
        }
        .signature-block { display: table-cell; width: 50%; vertical-align: bottom; }
        .signature-block:last-child { text-align: right; }
        .signature-space { display: block; height: 38px; }
        .signature-line { display: inline-block; min-width: 175px; padding-top: 5px; border-top: 1px solid #697780; font-weight: 700; }
        .signature-caption { display: block; color: #687680; font-size: 11px; }
        .footer {
            position: absolute;
            left: 17mm;
            right: 17mm;
            bottom: 8mm;
            padding-top: 7px;
            border-top: 1px solid #d8dee4;
            text-align: center;
            color: #7b8790;
            font-size: 10px;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: #fff;
                overflow: hidden;
            }
            .toolbar { display: none; }
            .letter {
                width: 210mm;
                height: 297mm;
                min-height: 297mm;
                max-height: 297mm;
                margin: 0;
                padding: 15mm 17mm 13mm;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            .signatures {
                left: 17mm;
                right: 17mm;
                bottom: 22mm;
                width: calc(100% - 34mm);
            }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button class="print-button" type="button" onclick="window.print()">Print Letter</button></div>

    <main class="letter">
        <header class="letter-header">
            <div class="logo-wrap">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"
                     alt="Organization logo"
                     onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
            </div>
            <div class="organization">
                <h1>{{ $getSetting['name'] ?? '' }}</h1>
                <p>{{ $getSetting['address'] ?? '' }}</p>
                <p class="contact-line">
                    {{ $getSetting['mobile'] ?? '' }}
                    @if(!empty($getSetting['mobile']) && !empty($getSetting['gmail'])) &nbsp; | &nbsp; @endif
                    {{ $getSetting['gmail'] ?? '' }}
                </p>
            </div>
        </header>

        <section class="document-heading">
            <h2>Joining Letter</h2>
            <p>Employment Confirmation</p>
        </section>

        <table class="meta">
            <tr>
                <td><strong>Reference No.:</strong> {{ $referenceNo }}</td>
                <td><strong>Date:</strong> {{ $letterDate }}</td>
            </tr>
        </table>

        <div class="recipient">
            <div>To,</div>
            <strong>{{ $employeeName ?: 'Employee' }}</strong><br>
            @if(!empty($data['address'])){{ $data['address'] }}<br>@endif
            @if(!empty($data['email'])){{ $data['email'] }}@endif
            @if(!empty($data['email']) && !empty($data['mobile'])) &nbsp; | &nbsp; @endif
            @if(!empty($data['mobile'])){{ $data['mobile'] }}@endif
        </div>

        <div class="subject">Subject: Confirmation of Joining as {{ $designation }}</div>

        <section class="content">
            <p>Dear {{ $employeeName ?: 'Employee' }},</p>

            <p>We are pleased to confirm your joining with <strong>{{ $getSetting['name'] ?? 'our organization' }}</strong> in the position of <strong>{{ $designation }}</strong>, effective from <strong>{{ $joiningDate }}</strong>.</p>

            <p>Your association with the organization will be governed by the terms and conditions communicated to you at the time of appointment, together with the policies and procedures in force from time to time. We trust that you will carry out your responsibilities with integrity, professionalism, and commitment.</p>

            <table class="details">
                <tr>
                    <th>Employee Name</th><td>{{ $employeeName ?: '—' }}</td>
                    <th>Designation</th><td>{{ $designation }}</td>
                </tr>
                <tr>
                    <th>Date of Joining</th><td>{{ $joiningDate }}</td>
                    <th>Date of Birth</th><td>{{ $dob }}</td>
                </tr>
                <tr>
                    <th>Mobile Number</th><td>{{ $data['mobile'] ?? '—' }}</td>
                    <th>Email Address</th><td>{{ $data['email'] ?? '—' }}</td>
                </tr>
            </table>

            <div class="compensation">
                <span class="compensation-label">Joining Amount / Per Month</span>
                <span class="compensation-value">{{ $formattedJoiningAmount }}</span><br>
                <span class="compensation-note">As recorded in the employee's joining details.</span>
            </div>

            <p>Please submit any outstanding employment and identity documents to the administration office. We welcome you to the team and look forward to a productive and successful association.</p>

        </section>

        <section class="signatures">
            <div class="signature-block">
                <span class="signature-space"></span>
                <span class="signature-line">{{ $employeeName ?: 'Employee' }}</span>
                <span class="signature-caption">Employee Signature</span>
            </div>
            <div class="signature-block">
                <span class="signature-space"></span>
                <span class="signature-line">Authorized Signatory</span>
                <span class="signature-caption">For {{ $getSetting['name'] ?? 'the Organization' }}</span>
            </div>
        </section>

        <footer class="footer">This is an official joining confirmation issued by {{ $getSetting['name'] ?? 'the organization' }}.</footer>
    </main>
</body>
</html>
