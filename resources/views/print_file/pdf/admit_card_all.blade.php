@php
    $getSetting = Helper::getSetting();
    $getSession = Helper::getSession();
    $session = DB::table('sessions')->where('id', Session::get('session_id'))->whereNull('deleted_at')->first();
    $sessionName = ($session->from_year ?? '') . (!empty($session->to_year) ? '-' . $session->to_year : '');
    
    // Resolve Exam info if exam_id exists
    $examInfo = null;
    $currentExamId = $exam_id ?? request()->route('exam_id') ?? null;
    if (!empty($currentExamId)) {
        $examInfo = DB::table('exams')->where('id', $currentExamId)->whereNull('deleted_at')->first();
    }
    $examTitle = $examInfo->name ?? 'EXAMINATION';

    // School Address clean-up (avoid duplicate pincode)
    $schoolAddress = $getSetting['address'] ?? '';
    $schoolPincode = $getSetting['pincode'] ?? '';
    if (!empty($schoolPincode) && strpos($schoolAddress, $schoolPincode) === false) {
        $schoolAddress .= ' - ' . $schoolPincode;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards - {{ $examTitle }} ({{ $sessionName }})</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* CSS Reset & Print Box Geometry */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 4mm 6mm;
        }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            color: #111827;
            background: #e2e8f0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Screen Preview Toolbar */
        .no-print-toolbar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
            margin-bottom: 15px;
        }
        .toolbar-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
        }
        .btn-tool {
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-close {
            background: #475569;
            color: #ffffff;
        }
        .btn-close:hover {
            background: #334155;
        }

        /* A4 Page Container (Screen) */
        .a4-page {
            width: 210mm;
            height: 289mm;
            max-height: 289mm;
            background: #ffffff;
            margin: 0 auto 20px auto;
            padding: 3mm 4mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            page-break-after: always;
            page-break-inside: avoid;
        }

        /* Admit Card Box (Exactly fits 2 per A4) */
        .admit-card {
            width: 100%;
            height: 139mm;
            max-height: 139mm;
            min-height: 139mm;
            border: 1.5px solid #0f172a;
            border-radius: 4px;
            padding: 3mm 4mm;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
        }

        /* Header Section */
        .card-header {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
            gap: 10px;
        }
        .header-logo {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-logo img {
            max-width: 48px;
            max-height: 48px;
            object-fit: contain;
        }
        .header-info {
            flex: 1;
            text-align: center;
            line-height: 1.2;
        }
        .school-name {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .school-address {
            font-size: 8pt;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 1.5px;
        }
        .school-contact {
            font-size: 7.5pt;
            color: #374151;
        }

        /* Examination Badge Banner */
        .exam-banner {
            background: #0f172a;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-align: center;
            padding: 2.2px 0;
            text-transform: uppercase;
            border-radius: 2px;
            margin-bottom: 1.8mm;
        }

        /* Candidate Details & Photo */
        .candidate-section {
            display: flex;
            gap: 8px;
            margin-bottom: 1.8mm;
            align-items: stretch;
        }
        .candidate-grid {
            flex: 1;
            width: 100%;
            border-collapse: collapse;
        }
        .candidate-grid td {
            font-size: 8pt;
            line-height: 1.3;
            padding: 2px 2px;
            vertical-align: middle;
        }
        .candidate-grid .label {
            color: #374151;
            font-weight: 700;
            width: 18%;
            white-space: nowrap;
        }
        .candidate-grid .colon {
            width: 2%;
            text-align: center;
            color: #374151;
            font-weight: 700;
        }
        .candidate-grid .value {
            color: #000000;
            font-weight: 700;
            font-size: 8.5pt;
            width: 30%;
        }
        .candidate-grid .val-highlight {
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid #94a3b8;
            display: inline-block;
            font-size: 9.5pt;
            font-weight: 800;
            color: #0f172a;
        }

        /* Photo Box */
        .photo-box {
            width: 23mm;
            height: 27mm;
            border: 1px dashed #64748b;
            border-radius: 3px;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            text-align: center;
            position: relative;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            font-size: 6.5pt;
            color: #64748b;
            font-weight: 700;
            line-height: 1.2;
            padding: 3px;
            text-transform: uppercase;
        }

        /* Schedule Table (16 Fixed Rows, Fills Available Height Evenly) */
        .schedule-table-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5mm;
            min-height: 0;
        }
        .schedule-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .schedule-table th,
        .schedule-table td {
            border: 0.8px solid #0f172a;
            padding: 1.5px 3.5px;
            vertical-align: middle;
        }
        .schedule-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            font-size: 8pt;
            height: 4.8mm;
            border-bottom: 1.2px solid #0f172a;
        }
        .schedule-table td {
            font-size: 8pt;
            line-height: 1.15;
            height: 4.3mm;
        }
        .schedule-table td.col-sr {
            width: 5%;
            text-align: center;
            font-weight: 700;
        }
        .schedule-table td.col-subject {
            width: 36%;
            font-weight: 700;
            color: #000000;
        }
        .schedule-table td.col-date {
            width: 16%;
            text-align: center;
            font-weight: 600;
        }
        .schedule-table td.col-day {
            width: 14%;
            text-align: center;
            font-weight: 600;
        }
        .schedule-table td.col-time {
            width: 17%;
            text-align: center;
            font-weight: 600;
        }
        .schedule-table td.col-sign {
            width: 12%;
            text-align: center;
        }
        .schedule-table tr.empty-row td {
            background: #ffffff;
        }
        .schedule-table tr.empty-row td.col-sr {
            color: #64748b;
        }

        /* Important Instructions */
        .instructions-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 2px;
            padding: 2px 6px;
            font-size: 7.2pt;
            line-height: 1.25;
            color: #1f2937;
            margin-bottom: 1.5mm;
        }
        .instructions-box b {
            color: #0f172a;
            font-weight: 800;
        }

        /* Signatures Footer */
        .signatures-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 4mm;
        }
        .sig-block {
            text-align: center;
            width: 22%;
        }
        .sig-line {
            border-top: 1.2px solid #0f172a;
            margin-bottom: 2px;
            height: 1px;
        }
        .sig-title {
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Cut Divider Between Cards */
        .cut-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 5mm;
            margin: 1mm 0;
            text-align: center;
            overflow: hidden;
        }
        .cut-line {
            flex: 1;
            border-bottom: 1.5px dashed #475569;
            height: 1px;
        }
        .cut-text {
            padding: 0 12px;
            font-size: 7.5pt;
            font-weight: 800;
            color: #334155;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .cut-icon {
            font-size: 8.5pt;
            margin: 0 2px;
        }

        /* Print Media Styles */
        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .a4-page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 1.5mm 2mm !important;
                width: 100% !important;
                height: 289mm !important;
                max-height: 289mm !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
            }
            .a4-page:last-child {
                page-break-after: avoid !important;
            }
            .admit-card {
                height: 139mm !important;
                max-height: 139mm !important;
                min-height: 139mm !important;
            }
        }
    </style>
</head>
<body>

    <!-- Non-printing Control Toolbar -->
    <div class="no-print-toolbar">
        <div class="toolbar-title">
            <i class="fa fa-id-card-o"></i> Admit Card Print Preview &bull; {{ $examTitle }} ({{ $sessionName }})
        </div>
        <div class="toolbar-actions">
            <button onclick="window.print()" class="btn-tool btn-print">
                <i class="fa fa-print"></i> Print Admit Cards
            </button>
            <button onclick="window.close(); if(history.length > 1){ history.back(); }" class="btn-tool btn-close">
                <i class="fa fa-arrow-left"></i> Back / Close
            </button>
        </div>
    </div>

    @if(!empty($data) && count($data) > 0)
        {{-- Group into chunks of 2 for exactly 2 admit cards per A4 page --}}
        @foreach($data->chunk(2) as $chunkIndex => $chunk)
            <div class="a4-page">
                @foreach($chunk as $cardIndex => $item)
                    @php
                        // Fetch schedules for candidate's class & exam
                        $sub = DB::table('examination_schedules')
                            ->select('examination_schedules.*', 'subject.name as subject_name')
                            ->leftJoin('subject as subject', 'subject.id', '=', 'examination_schedules.subject_id')
                            ->where('examination_schedules.class_type_id', $item->class_type_id)
                            ->when(!empty($currentExamId), function($q) use ($currentExamId) {
                                return $q->where('examination_schedules.exam_id', $currentExamId);
                            })
                            ->where('examination_schedules.session_id', Session::get('session_id'))
                            ->where('examination_schedules.branch_id', Session::get('branch_id'))
                            ->whereNull('examination_schedules.deleted_at')
                            ->whereNull('subject.deleted_at')
                            ->where('examination_schedules.date', '!=', '1970-01-01')
                            ->orderBy('examination_schedules.date', 'ASC')
                            ->get();

                        $totalSubjects = count($sub);
                        $reservedRows = max(16, $totalSubjects);
                    @endphp

                    <div class="admit-card">
                        {{-- 1. Header --}}
                        <div class="card-header">
                            <div class="header-logo">
                                @if(!empty($getSetting['left_logo']))
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="Logo">
                                @else
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}" alt="Logo">
                                @endif
                            </div>
                            <div class="header-info">
                                <div class="school-name">{{ $getSetting['name'] ?? 'EDUCATION INSTITUTION' }}</div>
                                <div class="school-address">
                                    {{ $schoolAddress }}
                                </div>
                                <div class="school-contact">
                                    @if(!empty($getSetting['mobile']))<span><b>Phone:</b> {{ $getSetting['mobile'] }}</span>@endif
                                    @if(!empty($getSetting['gmail']))<span> &bull; <b>Email:</b> {{ $getSetting['gmail'] }}</span>@endif
                                </div>
                            </div>
                        </div>

                        {{-- 2. Exam Banner --}}
                        <div class="exam-banner">
                            ADMIT CARD &bull; {{ $examTitle }} (ACADEMIC SESSION: {{ $sessionName }})
                        </div>

                        {{-- 3. Candidate Details & Photo --}}
                        <div class="candidate-section">
                            <table class="candidate-grid">
                                <tr>
                                    <td class="label">Roll No.</td>
                                    <td class="colon">:</td>
                                    <td class="value"><span class="val-highlight">{{ !empty($item->exam_roll_no) ? $item->exam_roll_no : (!empty($item->roll_no) ? $item->roll_no : ($item->admissionNo ?? '---')) }}</span></td>
                                    <td class="label">Admission No.</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ $item->admissionNo ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Student Name</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ strtoupper(trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''))) }}</td>
                                    <td class="label">Class / Sec</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ $item->class_name ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Father's Name</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ strtoupper($item->father_name ?? '---') }}</td>
                                    <td class="label">Mother's Name</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ strtoupper($item->mother_name ?? '---') }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Date of Birth</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ !empty($item->dob) && $item->dob != '1970-01-01' ? date('d-m-Y', strtotime($item->dob)) : '---' }}</td>
                                    <td class="label">Contact No.</td>
                                    <td class="colon">:</td>
                                    <td class="value">{{ $item->mobile ?? ($item->father_mobile ?? '---') }}</td>
                                </tr>
                            </table>

                            <div class="photo-box">
                                @if(!empty($item->student_profile_image))
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$item->student_profile_image }}" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" 
                                         alt="Photo">
                                    <div class="photo-placeholder" style="display:none;">
                                        Affix Recent<br>Passport<br>Photo
                                    </div>
                                @else
                                    <div class="photo-placeholder">
                                        Affix Recent<br>Passport<br>Photo
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 4. Examination Schedule Table (Reserved 16 Rows, Fills remaining card height) --}}
                        <div class="schedule-table-wrap">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 36%; text-align: left; padding-left: 5px;">Subject</th>
                                        <th style="width: 16%;">Exam Date</th>
                                        <th style="width: 14%;">Day</th>
                                        <th style="width: 17%;">Timing</th>
                                        <th style="width: 12%;">Invigilator Sign</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($r = 0; $r < $reservedRows; $r++)
                                        @if(isset($sub[$r]))
                                            @php $sRow = $sub[$r]; @endphp
                                            <tr>
                                                <td class="col-sr">{{ $r + 1 }}</td>
                                                <td class="col-subject" style="text-align: left; padding-left: 5px;">{{ $sRow->subject_name ?? '-' }}</td>
                                                <td class="col-date">{{ !empty($sRow->date) && $sRow->date != '1970-01-01' ? date('d-m-Y', strtotime($sRow->date)) : '-' }}</td>
                                                <td class="col-day">{{ !empty($sRow->date) && $sRow->date != '1970-01-01' ? date('l', strtotime($sRow->date)) : '-' }}</td>
                                                <td class="col-time">
                                                    @if(!empty($sRow->from_time))
                                                        {{ date('h:i A', strtotime($sRow->from_time)) }} - {{ date('h:i A', strtotime($sRow->to_time)) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="col-sign"></td>
                                            </tr>
                                        @else
                                            <tr class="empty-row">
                                                <td class="col-sr">{{ $r + 1 }}</td>
                                                <td class="col-subject">&nbsp;</td>
                                                <td class="col-date">&nbsp;</td>
                                                <td class="col-day">&nbsp;</td>
                                                <td class="col-time">&nbsp;</td>
                                                <td class="col-sign">&nbsp;</td>
                                            </tr>
                                        @endif
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        {{-- 5. Important Instructions --}}
                        <div class="instructions-box">
                            <b>Instructions:</b> 1. Candidate must carry this Admit Card &amp; ID card to examination hall daily. 2. Electronic items, smart watches &amp; mobile phones are strictly prohibited. 3. Reach examination room at least 15 minutes before reporting time.
                        </div>

                        {{-- 6. Signatures Footer --}}
                        <div class="signatures-footer">
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Candidate's Sign</div>
                            </div>
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Class Teacher</div>
                            </div>
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Center Superintendent</div>
                            </div>
                            <div class="sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title">Principal / Seal</div>
                            </div>
                        </div>
                    </div>

                    {{-- Dashed Cut Divider if 2 cards in current page and between card 1 and card 2 --}}
                    @if($loop->first && $chunk->count() > 1)
                        <div class="cut-divider">
                            <span class="cut-line"></span>
                            <span class="cut-text">
                                <span class="cut-icon">&#9986;</span> CUT FROM HERE <span class="cut-icon">&#9986;</span>
                            </span>
                            <span class="cut-line"></span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 50px; font-family: sans-serif;">
            <h3>No Admit Card Data Found</h3>
            <p>Please check if students are assigned to this exam or check filter criteria.</p>
        </div>
    @endif

</body>
</html>