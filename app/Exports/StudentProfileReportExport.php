<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentProfileReportExport implements FromArray, ShouldAutoSize, WithEvents, WithTitle
{
    private array $data;
    private array $rows = [];
    private array $sectionRows = [];
    private array $tableHeaderRows = [];
    private array $mergedRows = [];
    private array $currencyRanges = [];
    private array $percentageCells = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->buildRows();
    }

    public function title(): string
    {
        return 'Student Profile';
    }

    public function array(): array
    {
        return $this->rows;
    }

    private function row(array $values = []): int
    {
        $this->rows[] = array_pad(array_slice($values, 0, 8), 8, '');
        return count($this->rows);
    }

    private function section(string $title): void
    {
        $row = $this->row([$title]);
        $this->sectionRows[] = $row;
        $this->mergedRows[] = $row;
    }

    private function tableHeader(array $columns): void
    {
        $this->tableHeaderRows[] = $this->row($columns);
    }

    private function value($value): string
    {
        return trim((string) $value) !== '' ? (string) $value : '-';
    }

    private function date($value): string
    {
        return !empty($value) ? Carbon::parse($value)->format('d M Y') : '-';
    }

    private function buildRows(): void
    {
        $student = $this->data['data'];
        $name = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
        $session = collect([$student->from_year ?? null, $student->to_year ?? null])->filter()->implode('-');
        $feeHeadLedger = collect($this->data['feeHeadLedger'] ?? []);

        $this->mergedRows[] = $this->row(['STUDENT PROFILE REPORT']);
        $this->mergedRows[] = $this->row([$name.' | Admission No. '.$this->value($student->admissionNo ?? null)]);
        $this->mergedRows[] = $this->row(['Generated on '.now()->format('d M Y, h:i A')]);
        $this->row();

        $this->section('STUDENT & ACADEMIC INFORMATION');
        $this->row(['Student Name', $name, '', '', 'Admission No.', $this->value($student->admissionNo ?? null), '', '']);
        $this->row(['Student Unique ID', $this->value($student->attendance_unique_id ?? $student->unique_system_id ?? null), '', '', 'Session', $this->value($session), '', '']);
        $this->row(['Class / Course', $this->value($student->class_name ?? null), '', '', 'Roll No.', $this->value($student->roll_no ?? null), '', '']);
        $this->row(['Exam Roll No.', $this->value($student->exam_roll_no ?? null), '', '', 'Admission Date', $this->date($student->admission_date ?? null), '', '']);
        $this->row(['Date of Birth', $this->date($student->dob ?? null), '', '', 'Gender', $this->value($student->genderName ?? null), '', '']);
        $this->row(['Category', $this->value($student->category ?? $student->caste_category ?? null), '', '', 'Status', (int) ($student->status ?? 0) === 1 ? 'Active' : 'Inactive', '', '']);
        $this->row(['Medium', $this->value($student->medium ?? null), '', '', 'Admission Type', (int) ($student->admission_type_id ?? 0) === 2 ? 'RTE' : 'Non RTE', '', '']);

        $this->section('CONTACT, FAMILY & ADDRESS');
        $this->row(['Student Mobile', $this->value($student->mobile ?? null), '', '', 'Email', $this->value($student->email ?? null), '', '']);
        $this->row(['Father Name', $this->value($student->father_name ?? null), '', '', 'Father Mobile', $this->value($student->father_mobile ?? null), '', '']);
        $this->row(['Mother Name', $this->value($student->mother_name ?? null), '', '', 'Mother Mobile', $this->value($student->mother_mob ?? null), '', '']);
        $this->row(['Guardian Name', $this->value($student->guardian_name ?? null), '', '', 'Guardian Mobile', $this->value($student->guardian_mobile ?? null), '', '']);
        $address = collect([$student->address ?? null, $student->village_city ?? null, $student->city_name ?? null, $student->state_name ?? null, $student->pincode ?? null])->filter()->implode(', ');
        $this->row(['Address', $this->value($address), '', '', 'Religion', $this->value($student->religion ?? null), '', '']);
        $this->row(['Aadhaar', $this->value($student->aadhaar ?? null), '', '', 'APAAR ID', $this->value($student->apaar_id ?? null), '', '']);

        $this->section('FEES SUMMARY');
        $this->tableHeader(['Assigned Fees', 'Paid Fees', 'Discount', 'Fine', 'Outstanding', 'Paid %', '', '']);
        $summaryAssigned = $feeHeadLedger->isNotEmpty()
            ? (float) $feeHeadLedger->sum('assigned_amount')
            : (float) $this->data['assignedFees'];
        $summaryPaid = $feeHeadLedger->isNotEmpty()
            ? (float) $feeHeadLedger->sum('paid_amount')
            : (float) $this->data['paidFees'];
        $summaryDiscount = $feeHeadLedger->isNotEmpty()
            ? (float) $feeHeadLedger->sum('discount')
            : (float) $this->data['feeDiscount'];
        $summaryFine = $feeHeadLedger->isNotEmpty()
            ? (float) $feeHeadLedger->sum('fine_amount')
            : (float) $this->data['feeFine'];
        $summaryDue = $feeHeadLedger->isNotEmpty()
            ? (float) $feeHeadLedger->sum('due_amount')
            : (float) $this->data['dueFees'];
        $summaryPaidPercentage = $summaryAssigned > 0
            ? round(min(100, ($summaryPaid / $summaryAssigned) * 100), 2)
            : 0;
        $feesSummaryRow = $this->row([
            $summaryAssigned, $summaryPaid, $summaryDiscount, $summaryFine,
            $summaryDue, $summaryPaidPercentage, '', '',
        ]);
        $this->currencyRanges[] = "A{$feesSummaryRow}:E{$feesSummaryRow}";
        $this->percentageCells[] = "F{$feesSummaryRow}";

        $this->section('FEE HEAD-WISE SUMMARY');
        $this->tableHeader(['#', 'Fee Head', 'Assigned', 'Discount', 'Paid', 'Fine', 'Due', 'Status']);
        foreach ($feeHeadLedger as $index => $head) {
            $status = $head->due_amount <= 0
                ? 'Paid'
                : (($head->paid_amount > 0 || $head->discount > 0) ? 'Partially Paid' : 'Unpaid');
            $headRow = $this->row([
                $index + 1,
                $this->value($head->name ?? null),
                (float) ($head->assigned_amount ?? 0),
                (float) ($head->discount ?? 0),
                (float) ($head->paid_amount ?? 0),
                (float) ($head->fine_amount ?? 0),
                (float) ($head->due_amount ?? 0),
                $status,
            ]);
            $this->currencyRanges[] = "C{$headRow}:G{$headRow}";
        }

        if ($feeHeadLedger->isNotEmpty()) {
            $feeHeadTotalRow = $this->row([
                '',
                'Total',
                (float) $feeHeadLedger->sum('assigned_amount'),
                (float) $feeHeadLedger->sum('discount'),
                (float) $feeHeadLedger->sum('paid_amount'),
                (float) $feeHeadLedger->sum('fine_amount'),
                (float) $feeHeadLedger->sum('due_amount'),
                '',
            ]);
            $this->currencyRanges[] = "C{$feeHeadTotalRow}:G{$feeHeadTotalRow}";
            $this->tableHeaderRows[] = $feeHeadTotalRow;
        } else {
            $this->mergedRows[] = $this->row(['No fee heads are assigned to this student.']);
        }

        $this->section('FEE HEAD-WISE PAYMENT HISTORY');
        $this->tableHeader(['#', 'Fee Head', 'Payment Date', 'Receipt No.', 'Payment Mode', 'Paid', 'Discount', 'Fine']);
        $paymentIndex = 1;
        foreach ($feeHeadLedger as $head) {
            foreach (collect($head->payments ?? []) as $payment) {
                $paymentRow = $this->row([
                    $paymentIndex++,
                    $this->value($head->name ?? null),
                    $this->date($payment->date ?? null),
                    $this->value($payment->receipt_no ?? null),
                    $this->value($payment->payment_mode ?? null),
                    (float) ($payment->paid_amount ?? 0),
                    (float) ($payment->discount ?? 0),
                    (float) ($payment->installment_fine ?? 0),
                ]);
                $this->currencyRanges[] = "F{$paymentRow}:H{$paymentRow}";
            }
        }
        if ($paymentIndex === 1) {
            $this->mergedRows[] = $this->row(['No fee-head payment records available.']);
        }

        $this->section('ATTENDANCE SUMMARY');
        $this->tableHeader(['Total Marked Days', 'Present', 'Absent', 'Attendance %', '', '', '', '']);
        $attendanceSummaryRow = $this->row([
            $this->data['attendanceTotal'], $this->data['attendancePresent'],
            $this->data['attendanceAbsent'], (float) $this->data['attendancePercentage'], '', '', '', '',
        ]);
        $this->percentageCells[] = "D{$attendanceSummaryRow}";
        $this->tableHeader(['#', 'Date', 'Check In', 'Check Out', 'Status', '', '', '']);
        foreach (collect($this->data['recentAttendance']) as $index => $attendance) {
            $this->row([
                $index + 1, $this->date($attendance->date ?? null),
                $this->value($attendance->time ?? null), $this->value($attendance->out_time ?? null),
                $this->value($attendance->profile_status ?? null), '', '', '',
            ]);
        }

        $this->section('EXAMINATION RESULTS');
        $this->tableHeader(['#', 'Exam', 'Exam Date', 'Subjects', 'Obtained', 'Maximum', 'Percentage', '']);
        foreach (collect($this->data['examResults']) as $index => $result) {
            $examRow = $this->row([
                $index + 1, $result->name, $this->date($result->date), $result->subject_count,
                (float) $result->obtained, (float) $result->maximum, (float) $result->percentage, '',
            ]);
            $this->percentageCells[] = "G{$examRow}";
            foreach ($result->subjects as $subject) {
                $this->row(['', '  - '.$subject->name, '', '', $subject->is_numeric ? (float) $subject->marks : $subject->marks, (float) $subject->maximum, '', '']);
            }
        }
        if (collect($this->data['examResults'])->isEmpty()) {
            $this->mergedRows[] = $this->row(['No examination marks available for the current session.']);
        }

        $this->section('ASSIGNED SUBJECTS');
        $subjects = collect($this->data['assignedSubjects'])->pluck('name')->filter()->values();
        $this->mergedRows[] = $this->row([$subjects->isNotEmpty() ? $subjects->implode('  |  ') : 'No subjects assigned.']);

        $this->section('PROMOTION HISTORY');
        $this->tableHeader(['#', 'Session', 'Class', 'Roll No.', 'Admission No.', 'Status', '', '']);
        foreach (collect($this->data['promotion_history']) as $index => $history) {
            $historySession = collect([$history->from_year ?? null, $history->to_year ?? null])->filter()->implode('-');
            $this->row([$index + 1, $this->value($historySession), $this->value($history->class_name ?? null), $this->value($history->roll_no ?? null), $this->value($history->admissionNo ?? null), (int) ($history->status ?? 0) === 1 ? 'Active' : 'Inactive', '', '']);
        }

        $this->section('SIBLINGS & DOCUMENTS');
        $this->tableHeader(['Type', 'Name / Title', 'Admission No.', 'Class', 'Remark', 'Uploaded On', '', '']);
        foreach (collect($this->data['siblings']) as $sibling) {
            $this->row(['Sibling', trim(($sibling->first_name ?? '').' '.($sibling->last_name ?? '')), $this->value($sibling->admissionNo ?? null), $this->value($sibling->class_name ?? null), '', '', '', '']);
        }
        foreach (collect($this->data['getDocuments']) as $document) {
            $this->row(['Document', $this->value($document->title ?? null), '', '', $this->value($document->remark ?? null), $this->date($document->created_at ?? null), '', '']);
        }

        $this->section('BANK & TRANSPORT INFORMATION');
        $this->row(['Bank Name', $this->value($student->bank_name ?? null), '', '', 'Account No.', $this->value($student->bank_account ?? null), '', '']);
        $this->row(['Account Holder', $this->value($student->bank_account_holder ?? null), '', '', 'IFSC', $this->value($student->ifsc ?? null), '', '']);
        $this->row(['Transport', in_array(strtolower((string) ($student->transport ?? '')), ['1', 'yes'], true) ? 'Yes' : 'No', '', '', 'Bus / Route', $this->value(collect([$student->bus_number ?? null, $student->bus_route ?? null])->filter()->implode(' / ')), '', '']);
        $this->row(['Stoppage', $this->value($student->stoppage ?? null), '', '', 'Transport Charges', $this->value($student->transpor_charges ?? null), '', '']);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->rows);
                $sheet->setShowGridlines(false);
                $sheet->freezePane('A5');
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                $sheet->getStyle("A1:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $sheet->getStyle("A1:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR)->getColor()->setARGB('FFD8DEE9');

                $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(18)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF17365D');
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF17365D');
                $sheet->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAF7');
                $sheet->getStyle('A3:H3')->getFont()->setItalic(true)->getColor()->setARGB('FF666666');

                $colors = ['FF1F4E78', 'FF548235', 'FF7030A0', 'FFC65911', 'FF2F75B5', 'FF008C95'];
                foreach ($this->sectionRows as $index => $row) {
                    $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
                    $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($colors[$index % count($colors)]);
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }
                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->getColor()->setARGB('FF17365D');
                    $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDDEBF7');
                }
                foreach ($this->mergedRows as $row) {
                    $sheet->mergeCells("A{$row}:H{$row}");
                }
                foreach ($this->currencyRanges as $range) {
                    $sheet->getStyle($range)->getNumberFormat()->setFormatCode('₹#,##0.00');
                }
                foreach ($this->percentageCells as $cell) {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.00"%"');
                }

                foreach (['B', 'C', 'D', 'E', 'F', 'G'] as $column) {
                    $sheet->getColumnDimension($column)->setWidth(18);
                }
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(28);
                $sheet->getStyle("A1:H3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);
                $sheet->getHeaderFooter()->setOddFooter('&LStudent Profile Report&CPage &P of &N&RGenerated by ARISE');
            },
        ];
    }
}
