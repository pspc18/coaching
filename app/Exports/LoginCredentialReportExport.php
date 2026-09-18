<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LoginCredentialReportExport extends StringValueBinder implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithTitle, WithCustomValueBinder
{
    private array $rows;
    private $setting;
    private string $className;
    private string $studentStatusLabel;

    public function __construct($students, $setting, string $className, string $studentStatusLabel = 'active')
    {
        $this->setting = $setting;
        $this->className = $className;
        $this->studentStatusLabel = $studentStatusLabel;
        $this->rows = collect($students)->values()->map(function ($student, $index) {
            return [
                (string) ($index + 1),
                (string) ($student->admissionNo ?: '-'),
                (string) ($student->attendance_unique_id ?: ($student->unique_system_id ?: '-')),
                trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: '-',
                (string) ($student->father_name ?: '-'),
                (string) ($student->mobile ?: '-'),
                (string) ($student->userName ?: '-'),
                (string) ($student->confirm_password ?: '-'),
            ];
        })->all();
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['S.No.', 'Admission No.', 'Attendance Unique ID', 'Student Name', 'Father Name', 'Mobile No.', 'Username', 'Password'];
    }

    public function title(): string
    {
        return 'Login Credentials';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 5);
                $headingRow = 6;
                $lastRow = count($this->rows) + $headingRow;
                $organizationName = $this->setting->name ?? 'Organization';
                $address = collect([
                    $this->setting->address ?? null,
                    $this->setting->pincode ?? null,
                ])->filter(function ($value) {
                    return trim((string) $value) !== '';
                })->implode(' - ');
                $contact = collect([
                    !empty($this->setting->mobile) ? 'Phone: '.$this->setting->mobile : null,
                    !empty($this->setting->gmail) ? 'Email: '.$this->setting->gmail : null,
                ])->filter()->implode(' | ');

                $sheet->setCellValue('A1', $organizationName);
                $sheet->setCellValue('A2', collect([$address, $contact])->filter()->implode(' | '));
                $sheet->setCellValue('A3', 'STUDENT LOGIN CREDENTIALS');
                $sheet->setCellValue('A4', 'List Type: '.strtoupper($this->studentStatusLabel).' STUDENTS | Class: '.$this->className.' | Total Students: '.count($this->rows));
                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:G{$row}");
                }

                $sheet->freezePane('A7');
                $sheet->setAutoFilter("A{$headingRow}:H{$lastRow}");
                $sheet->getStyle("A{$headingRow}:H{$headingRow}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle("A{$headingRow}:H{$headingRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF243B76');
                $sheet->getStyle("A{$headingRow}:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$headingRow}:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD9DEE8');
                $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B7:C{$lastRow}")->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle("F7:H{$lastRow}")->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(26)->getColor()->setARGB('FF243B76');
                $sheet->getStyle('A2:H2')->getFont()->setSize(10)->getColor()->setARGB('FF4B5563');
                $sheet->getStyle('A3:H3')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1F2937');
                $sheet->getStyle('A4:H4')->getFont()->setSize(11)->getColor()->setARGB('FF6B7280');
                $sheet->getRowDimension(1)->setRowHeight(38);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(26);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getColumnDimension('D')->setWidth(28);
                $sheet->getColumnDimension('E')->setWidth(24);
                $sheet->getColumnDimension('F')->setWidth(16);
                $sheet->getColumnDimension('G')->setWidth(22);
                $sheet->getColumnDimension('H')->setWidth(22);
                $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getHeaderFooter()->setOddFooter('&LConfidential student login credential report&CPage &P of &N&RGenerated by ARISE');
            },
        ];
    }
}
